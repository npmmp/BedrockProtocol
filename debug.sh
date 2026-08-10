#!/bin/bash

# Bedrock Protocol Debug Script
# Verifies all packets and types compile correctly

echo "=== BedrockProtocol Debug Script ==="
echo ""

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

ERRORS=0
WARNINGS=0
SUCCESS=0

# Check PHP version
echo "1. Checking PHP version..."
PHP_VERSION=$(php -r 'echo PHP_VERSION;')
echo "   PHP Version: $PHP_VERSION"
echo ""

# Check syntax errors
echo "2. Checking PHP syntax errors..."
echo ""

SYNTAX_ERRORS=$(find src -name "*.php" -exec php -l {} \; 2>&1 | grep -v "No syntax errors" | head -50)

if [ -n "$SYNTAX_ERRORS" ]; then
    echo -e "${RED}Syntax errors found:${NC}"
    echo "$SYNTAX_ERRORS"
    ERRORS=$((ERRORS + $(echo "$SYNTAX_ERRORS" | wc -l)))
else
    echo -e "${GREEN}No syntax errors found${NC}"
fi
echo ""

# Check for undefined constants
echo "3. Checking for undefined constants..."
echo ""

UNDEFINED_CONSTANTS=$(grep -rn "::NETWORK_ID" src/*.php | grep -v "ProtocolInfo::" | head -20)

if [ -n "$UNDEFINED_CONSTANTS" ]; then
    echo -e "${YELLOW}Potential undefined constants:${NC}"
    echo "$UNDEFINED_CONSTANTS"
    WARNINGS=$((WARNINGS + $(echo "$UNDEFINED_CONSTANTS" | wc -l)))
else
    echo -e "${GREEN}No undefined constants found${NC}"
fi
echo ""

# Check for missing use statements
echo "4. Checking for missing use statements..."
echo ""

MISSING_USE=$(grep -rn "new " src/*.php | grep -v "use " | grep -v "new self" | grep -v "new static" | head -20)

if [ -n "$MISSING_USE" ]; then
    echo -e "${YELLOW}Potential missing use statements:${NC}"
    echo "$MISSING_USE"
    WARNINGS=$((WARNINGS + $(echo "$MISSING_USE" | wc -l)))
else
    echo -e "${GREEN}No missing use statements found${NC}"
fi
echo ""

# Check packet registry
echo "5. Checking packet registry..."
echo ""

REGISTERED_PACKETS=$(grep -c "registerPacket" src/PacketPool.php)
TOTAL_PACKETS=$(find src -name "*Packet.php" | wc -l)

echo "   Registered packets: $REGISTERED_PACKETS"
echo "   Total packet files: $TOTAL_PACKETS"

if [ "$REGISTERED_PACKETS" -lt "$TOTAL_PACKETS" ]; then
    echo -e "${YELLOW}Warning: Some packets may not be registered${NC}"
    WARNINGS=$((WARNINGS + 1))
else
    echo -e "${GREEN}All packets appear to be registered${NC}"
fi
echo ""

# Check handler interface
echo "6. Checking handler interface..."
echo ""

HANDLER_METHODS=$(grep -c "function handle" src/PacketHandlerInterface.php)
HANDLER_IMPL=$(grep -c "function handle" src/PacketHandlerDefaultImplTrait.php)

echo "   Interface methods: $HANDLER_METHODS"
echo "   Implementation methods: $HANDLER_IMPL"

if [ "$HANDLER_METHODS" -ne "$HANDLER_IMPL" ]; then
    echo -e "${RED}Error: Handler interface and implementation mismatch${NC}"
    ERRORS=$((ERRORS + 1))
else
    echo -e "${GREEN}Handler interface and implementation match${NC}"
fi
echo ""

# Check new packet types
echo "7. Checking new packet types for 1.26.40..."
echo ""

NEW_PACKETS=(
    "ClientboundControlSchemeSetPacket"
    "PrimitiveShapesPacket"
    "ServerboundPackSettingChangePacket"
    "ClientboundDataStorePacket"
    "GraphicsOverrideParameterPacket"
    "ServerboundDataStorePacket"
    "ClientboundDataDrivenUIShowScreenPacket"
    "ClientboundDataDrivenUICloseScreenPacket"
    "ClientboundDataDrivenUIReloadPacket"
    "ClientboundTextureShiftPacket"
    "VoxelShapesPacket"
    "CameraSplinePacket"
    "CameraAimAssistActorPriorityPacket"
    "ResourcePacksReadyForValidationPacket"
    "LocatorBarPacket"
    "PartyChangedPacket"
    "ServerboundDataDrivenScreenClosedPacket"
    "SyncWorldClocksPacket"
    "ClientboundAttributeLayerSyncPacket"
    "ServerStoreInfoPacket"
    "ServerPresenceInfoPacket"
    "ClientboundUpdateSoundDataPacket"
    "SendPartyDestinationCookiePacket"
    "PartyDestinationCookieResponsePacket"
    "PlayerLocationPacket"
)

MISSING_PACKETS=()
for packet in "${NEW_PACKETS[@]}"; do
    if [ ! -f "src/$packet.php" ]; then
        MISSING_PACKETS+=("$packet")
    fi
done

if [ ${#MISSING_PACKETS[@]} -gt 0 ]; then
    echo -e "${RED}Missing packets:${NC}"
    for packet in "${MISSING_PACKETS[@]}"; do
        echo "   - $packet"
    done
    ERRORS=$((ERRORS + ${#MISSING_PACKETS[@]}))
else
    echo -e "${GREEN}All 25 new packets exist${NC}"
    SUCCESS=$((SUCCESS + 1))
fi
echo ""

# Check new types
echo "8. Checking new types for 1.26.40..."
echo ""

NEW_TYPES=(
    "types/DeltaMoveFlags.php"
    "types/input/InputFlag.php"
)

MISSING_TYPES=()
for type in "${NEW_TYPES[@]}"; do
    if [ ! -f "src/$type" ]; then
        MISSING_TYPES+=("$type")
    fi
done

if [ ${#MISSING_TYPES[@]} -gt 0 ]; then
    echo -e "${RED}Missing types:${NC}"
    for type in "${MISSING_TYPES[@]}"; do
        echo "   - $type"
    done
    ERRORS=$((ERRORS + ${#MISSING_TYPES[@]}))
else
    echo -e "${GREEN}All new types exist${NC}"
    SUCCESS=$((SUCCESS + 1))
fi
echo ""

# Check ProtocolInfo
echo "9. Checking ProtocolInfo..."
echo ""

PROTOCOL_VERSION=$(grep -o "CURRENT_PROTOCOL = [0-9]*" src/ProtocolInfo.php | grep -o "[0-9]*")
MINECRAFT_VERSION=$(grep -o "MINECRAFT_VERSION = '[^']*'" src/ProtocolInfo.php | cut -d"'" -f2)
MINECRAFT_VERSION_NETWORK=$(grep -o "MINECRAFT_VERSION_NETWORK = '[^']*'" src/ProtocolInfo.php | cut -d"'" -f2)

echo "   Protocol version: $PROTOCOL_VERSION"
echo "   Minecraft version: $MINECRAFT_VERSION"
echo "   Network version: $MINECRAFT_VERSION_NETWORK"

if [ "$PROTOCOL_VERSION" = "2168" ]; then
    echo -e "${GREEN}Protocol version is correct for 1.26.40${NC}"
    SUCCESS=$((SUCCESS + 1))
else
    echo -e "${RED}Protocol version mismatch (expected 2168)${NC}"
    ERRORS=$((ERRORS + 1))
fi
echo ""

# Summary
echo "=== Debug Summary ==="
echo ""
echo -e "${GREEN}Success: $SUCCESS${NC}"
echo -e "${YELLOW}Warnings: $WARNINGS${NC}"
echo -e "${RED}Errors: $ERRORS${NC}"
echo ""

if [ $ERRORS -eq 0 ]; then
    echo -e "${GREEN}✓ BedrockProtocol is ready for 1.26.40!${NC}"
    exit 0
else
    echo -e "${RED}✗ There are errors that need to be fixed${NC}"
    exit 1
fi

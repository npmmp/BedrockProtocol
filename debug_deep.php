<?php

declare(strict_types=1);

/**
 * BedrockProtocol Deep Debug Script (No Composer)
 * Tests packet structure and constants
 */

echo "=== BedrockProtocol Deep Debug ===\n\n";

// Manual autoload for testing
$sources = [
    __DIR__ . '/src/ProtocolInfo.php',
    __DIR__ . '/src/types/DeltaMoveFlags.php',
    __DIR__ . '/src/types/input/InputFlag.php',
];

foreach ($sources as $file) {
    if (file_exists($file)) {
        require_once $file;
    }
}

use pocketmine\network\mcpe\protocol\ProtocolInfo;

// Test 1: Protocol Info
echo "1. Testing ProtocolInfo...\n";
echo "   Protocol: " . ProtocolInfo::CURRENT_PROTOCOL . "\n";
echo "   Version: " . ProtocolInfo::MINECRAFT_VERSION . "\n";
echo "   Network: " . ProtocolInfo::MINECRAFT_VERSION_NETWORK . "\n";

if (ProtocolInfo::CURRENT_PROTOCOL === 2168) {
    echo "   ✓ Protocol version correct\n";
} else {
    echo "   ✗ Protocol version mismatch\n";
}
echo "\n";

// Test 2: New Packet IDs
echo "2. Testing new packet IDs...\n";
$newPackets = [
    'PLAYER_LOCATION_PACKET' => 0x146,
    'CLIENTBOUND_CONTROL_SCHEME_SET_PACKET' => 0x147,
    'PRIMITIVE_SHAPES_PACKET' => 0x148,
    'SERVERBOUND_PACK_SETTING_CHANGE_PACKET' => 0x149,
    'CLIENTBOUND_DATA_STORE_PACKET' => 0x14a,
    'GRAPHICS_OVERRIDE_PARAMETER_PACKET' => 0x14b,
    'SERVERBOUND_DATA_STORE_PACKET' => 0x14c,
    'CLIENTBOUND_DATA_DRIVEN_UI_SHOW_SCREEN_PACKET' => 0x14d,
    'CLIENTBOUND_DATA_DRIVEN_UI_CLOSE_SCREEN_PACKET' => 0x14e,
    'CLIENTBOUND_DATA_DRIVEN_UI_RELOAD_PACKET' => 0x14f,
    'CLIENTBOUND_TEXTURE_SHIFT_PACKET' => 0x150,
    'VOXEL_SHAPES_PACKET' => 0x151,
    'CAMERA_SPLINE_PACKET' => 0x152,
    'CAMERA_AIM_ASSIST_ACTOR_PRIORITY_PACKET' => 0x153,
    'RESOURCE_PACKS_READY_FOR_VALIDATION_PACKET' => 0x154,
    'LOCATOR_BAR_PACKET' => 0x155,
    'PARTY_CHANGED_PACKET' => 0x156,
    'SERVERBOUND_DATA_DRIVEN_SCREEN_CLOSED_PACKET' => 0x157,
    'SYNC_WORLD_CLOCKS_PACKET' => 0x158,
    'CLIENTBOUND_ATTRIBUTE_LAYER_SYNC_PACKET' => 0x159,
    'SERVER_STORE_INFO_PACKET' => 0x15a,
    'SERVER_PRESENCE_INFO_PACKET' => 0x15b,
    'CLIENTBOUND_UPDATE_SOUND_DATA_PACKET' => 0x15c,
    'SEND_PARTY_DESTINATION_COOKIE_PACKET' => 0x15d,
    'PARTY_DESTINATION_COOKIE_RESPONSE_PACKET' => 0x15e,
];

$allCorrect = true;
foreach ($newPackets as $name => $expectedId) {
    $constantName = "pocketmine\\network\\mcpe\\protocol\\ProtocolInfo::$name";
    if (defined($constantName)) {
        $actualId = constant($constantName);
        if ($actualId === $expectedId) {
            echo "   ✓ $name = 0x" . dechex($actualId) . "\n";
        } else {
            echo "   ✗ $name = 0x" . dechex($actualId) . " (expected 0x" . dechex($expectedId) . ")\n";
            $allCorrect = false;
        }
    } else {
        echo "   ✗ $name not defined\n";
        $allCorrect = false;
    }
}

if ($allCorrect) {
    echo "   ✓ All 25 new packet IDs correct\n";
} else {
    echo "   ✗ Some packet IDs are incorrect\n";
}
echo "\n";

// Test 3: New Types
echo "3. Testing new types...\n";
$types = [
    'DeltaMoveFlags' => __DIR__ . '/src/types/DeltaMoveFlags.php',
    'InputFlag' => __DIR__ . '/src/types/input/InputFlag.php',
];

foreach ($types as $name => $file) {
    if (file_exists($file)) {
        echo "   ✓ $name exists\n";
        
        // Check for syntax errors
        $output = [];
        $returnCode = 0;
        exec("php -l " . escapeshellarg($file) . " 2>&1", $output, $returnCode);
        
        if ($returnCode === 0) {
            echo "     - No syntax errors\n";
        } else {
            echo "     - Syntax errors found\n";
        }
    } else {
        echo "   ✗ $name not found\n";
    }
}
echo "\n";

// Test 4: File structure
echo "4. Testing file structure...\n";
$expectedFiles = [
    'src/ProtocolInfo.php',
    'src/types/DeltaMoveFlags.php',
    'src/types/input/InputFlag.php',
    'src/PlayerLocationPacket.php',
    'src/ClientboundControlSchemeSetPacket.php',
    'src/PrimitiveShapesPacket.php',
    'src/ServerboundPackSettingChangePacket.php',
    'src/ClientboundDataStorePacket.php',
    'src/GraphicsOverrideParameterPacket.php',
    'src/ServerboundDataStorePacket.php',
    'src/ClientboundDataDrivenUIShowScreenPacket.php',
    'src/ClientboundDataDrivenUICloseScreenPacket.php',
    'src/ClientboundDataDrivenUIReloadPacket.php',
    'src/ClientboundTextureShiftPacket.php',
    'src/VoxelShapesPacket.php',
    'src/CameraSplinePacket.php',
    'src/CameraAimAssistActorPriorityPacket.php',
    'src/ResourcePacksReadyForValidationPacket.php',
    'src/LocatorBarPacket.php',
    'src/PartyChangedPacket.php',
    'src/ServerboundDataDrivenScreenClosedPacket.php',
    'src/SyncWorldClocksPacket.php',
    'src/ClientboundAttributeLayerSyncPacket.php',
    'src/ServerStoreInfoPacket.php',
    'src/ServerPresenceInfoPacket.php',
    'src/ClientboundUpdateSoundDataPacket.php',
    'src/SendPartyDestinationCookiePacket.php',
    'src/PartyDestinationCookieResponsePacket.php',
];

$allExist = true;
foreach ($expectedFiles as $file) {
    if (file_exists(__DIR__ . '/' . $file)) {
        echo "   ✓ $file\n";
    } else {
        echo "   ✗ $file missing\n";
        $allExist = false;
    }
}

if ($allExist) {
    echo "   ✓ All 25 new packet files exist\n";
} else {
    echo "   ✗ Some files are missing\n";
}
echo "\n";

// Test 5: Syntax check all new packets
echo "5. Syntax checking all new packets...\n";
$syntaxErrors = 0;
foreach ($expectedFiles as $file) {
    $fullPath = __DIR__ . '/' . $file;
    if (file_exists($fullPath)) {
        $output = [];
        $returnCode = 0;
        exec("php -l " . escapeshellarg($fullPath) . " 2>&1", $output, $returnCode);
        
        if ($returnCode !== 0) {
            echo "   ✗ $file has syntax errors\n";
            echo "     " . implode("\n     ", $output) . "\n";
            $syntaxErrors++;
        }
    }
}

if ($syntaxErrors === 0) {
    echo "   ✓ All new packets have valid syntax\n";
} else {
    echo "   ✗ $syntaxErrors files have syntax errors\n";
}
echo "\n";

// Summary
echo "=== Debug Summary ===\n";
echo "Protocol: 2168 (Bedrock 1.26.40)\n";
echo "New Packets: 25\n";
echo "New Types: 2 (DeltaMoveFlags, InputFlag)\n";
echo "\n";

if ($allCorrect && $allExist && $syntaxErrors === 0) {
    echo "✓ BedrockProtocol 1.26.40 is ready!\n";
    exit(0);
} else {
    echo "✗ There are issues that need to be fixed\n";
    exit(1);
}

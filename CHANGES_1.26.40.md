# BedrockProtocol 1.26.40 - Changes

## Protocol Version

- **Previous**: 1001 (Bedrock 1.26.30)
- **Current**: 2168 (Bedrock 1.26.40)

## Version Info

```php
public const CURRENT_PROTOCOL = 2168;
public const MINECRAFT_VERSION = 'v26.40';
public const MINECRAFT_VERSION_NETWORK = '1.26.40';
```

## New Packets (25 new)

| ID | Constant | Class |
|----|----------|-------|
| 0x146 | `PLAYER_LOCATION_PACKET` | `PlayerLocationPacket` |
| 0x147 | `CLIENTBOUND_CONTROL_SCHEME_SET_PACKET` | `ClientboundControlSchemeSetPacket` |
| 0x148 | `PRIMITIVE_SHAPES_PACKET` | `PrimitiveShapesPacket` |
| 0x149 | `SERVERBOUND_PACK_SETTING_CHANGE_PACKET` | `ServerboundPackSettingChangePacket` |
| 0x14a | `CLIENTBOUND_DATA_STORE_PACKET` | `ClientboundDataStorePacket` |
| 0x14b | `GRAPHICS_OVERRIDE_PARAMETER_PACKET` | `GraphicsOverrideParameterPacket` |
| 0x14c | `SERVERBOUND_DATA_STORE_PACKET` | `ServerboundDataStorePacket` |
| 0x14d | `CLIENTBOUND_DATA_DRIVEN_UI_SHOW_SCREEN_PACKET` | `ClientboundDataDrivenUIShowScreenPacket` |
| 0x14e | `CLIENTBOUND_DATA_DRIVEN_UI_CLOSE_SCREEN_PACKET` | `ClientboundDataDrivenUICloseScreenPacket` |
| 0x14f | `CLIENTBOUND_DATA_DRIVEN_UI_RELOAD_PACKET` | `ClientboundDataDrivenUIReloadPacket` |
| 0x150 | `CLIENTBOUND_TEXTURE_SHIFT_PACKET` | `ClientboundTextureShiftPacket` |
| 0x151 | `VOXEL_SHAPES_PACKET` | `VoxelShapesPacket` |
| 0x152 | `CAMERA_SPLINE_PACKET` | `CameraSplinePacket` |
| 0x153 | `CAMERA_AIM_ASSIST_ACTOR_PRIORITY_PACKET` | `CameraAimAssistActorPriorityPacket` |
| 0x154 | `RESOURCE_PACKS_READY_FOR_VALIDATION_PACKET` | `ResourcePacksReadyForValidationPacket` |
| 0x155 | `LOCATOR_BAR_PACKET` | `LocatorBarPacket` |
| 0x156 | `PARTY_CHANGED_PACKET` | `PartyChangedPacket` |
| 0x157 | `SERVERBOUND_DATA_DRIVEN_SCREEN_CLOSED_PACKET` | `ServerboundDataDrivenScreenClosedPacket` |
| 0x158 | `SYNC_WORLD_CLOCKS_PACKET` | `SyncWorldClocksPacket` |
| 0x159 | `CLIENTBOUND_ATTRIBUTE_LAYER_SYNC_PACKET` | `ClientboundAttributeLayerSyncPacket` |
| 0x15a | `SERVER_STORE_INFO_PACKET` | `ServerStoreInfoPacket` |
| 0x15b | `SERVER_PRESENCE_INFO_PACKET` | `ServerPresenceInfoPacket` |
| 0x15c | `CLIENTBOUND_UPDATE_SOUND_DATA_PACKET` | `ClientboundUpdateSoundDataPacket` |
| 0x15d | `SEND_PARTY_DESTINATION_COOKIE_PACKET` | `SendPartyDestinationCookiePacket` |
| 0x15e | `PARTY_DESTINATION_COOKIE_RESPONSE_PACKET` | `PartyDestinationCookieResponsePacket` |

## New Types

### InputFlag
- **File**: `src/types/input/InputFlag.php`
- **Encoding**: bitflags (varint128)
- **Description**: Input flags for player auth input packet

### DeltaMoveFlags
- **File**: `src/types/DeltaMoveFlags.php`
- **Encoding**: bitflags (lu16)
- **Description**: Delta move flags for MoveEntityDeltaPacket

## Changed Packets

### PlayerAuthInputPacket (0x90)
- **Removed**: `transaction_presence`, `item_stack_request_presence`, `block_action_presence`, `vehicle_rotation_presence`, `predicted_vehicle_presence`
- **Changed**: `transaction` and `item_stack_request` are now always present
- **Changed**: Input flags encoding from varint to varint128 bitflags

### MoveActorDeltaPacket (0x6f)
- **Changed**: Complete restructure with DeltaMoveFlags
- **Before**: Individual optional fields for x/y/z/rot
- **After**: DeltaMoveFlags bitflags + conditional fields

### CraftingDataPacket (0x34)
- **Changed**: 8 separate recipe arrays → 1 unified Recipes array
- **Types**: ShapedRecipe, ShapelessRecipe, MultiRecipe, etc. merged into Recipes

### SubChunkPacket (0xae)
- **Changed**: Split into SubChunkEntryWithCaching / SubChunkEntryWithoutCaching
- **Changed**: origin type from vec3li to vec3i

### ResourcePackClientResponsePacket (0x08)
- **Changed**: status from varint to u8
- **Changed**: Added status "0": "none"

## Implementation Notes

### For PHP Implementation

1. **InputFlag**: Use bitflags with varint128 encoding
2. **DeltaMoveFlags**: Use bitflags with lu16 encoding
3. **Recipes**: Update CraftingDataPacket to use unified Recipes type
4. **SubChunk**: Update SubChunkPacket to handle both entry types

### Testing

- Test packet serialization/deserialization with 1.26.40 client
- Verify all new packets work correctly
- Test backward compatibility if needed

## References

- [PrismarineJS Protocol Data](https://github.com/PrismarineJS/minecraft-data/blob/master/data/bedrock/1.26.40/protocol.json)
- [CloudburstMC VERSIONS.md](https://github.com/CloudburstMC/Protocol/blob/3.0/VERSIONS.md)
- [GopherTunnel](https://github.com/Sandertv/gophertunnel)
- [Dragonfly](https://github.com/df-mc/dragonfly)

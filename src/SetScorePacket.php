<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol;

use pmmp\encoding\ByteBufferReader;
use pmmp\encoding\ByteBufferWriter;
use pmmp\encoding\VarInt;
use pocketmine\network\mcpe\protocol\serializer\CommonTypes;
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;
use function count;

class SetScorePacket extends DataPacket implements ClientboundPacket{
	public const NETWORK_ID = ProtocolInfo::SET_SCORE_PACKET;

	public const TYPE_CHANGE = 0;
	public const TYPE_REMOVE = 1;

	public int $type;
	/** @var ScorePacketEntry[] */
	public array $entries = [];

	/**
	 * @generate-create-func
	 * @param ScorePacketEntry[] $entries
	 */
	public static function create(int $type, array $entries) : self{
		$result = new self;
		$result->type = $type;
		$result->entries = $entries;
		return $result;
	}

	protected function decodePayload(ByteBufferReader $in) : void{
		$count = VarInt::readUnsignedInt($in);
		$this->entries = [];
		for($i = 0; $i < $count; ++$i){
			$entry = new ScorePacketEntry();

			$variant = VarInt::readUnsignedInt($in);
			$typeName = CommonTypes::getString($in);
			$entry->scoreboardId = VarInt::readSignedLong($in);

			$entry->type = match($typeName){
				'remove' => ScorePacketEntry::TYPE_REMOVE,
				'changeplayer' => ScorePacketEntry::TYPE_PLAYER,
				'changeentity' => ScorePacketEntry::TYPE_ENTITY,
				'changefakeplayer' => ScorePacketEntry::TYPE_FAKE_PLAYER,
				default => ScorePacketEntry::TYPE_REMOVE,
			};

			switch($entry->type){
				case ScorePacketEntry::TYPE_REMOVE:
					$hasObjective = $in->getBool();
					if($hasObjective){
						$entry->objectiveName = CommonTypes::getString($in);
					}
					break;
				case ScorePacketEntry::TYPE_PLAYER:
				case ScorePacketEntry::TYPE_ENTITY:
					$entry->objectiveName = CommonTypes::getString($in);
					$entry->score = VarInt::readSignedInt($in);
					$entry->actorUniqueId = VarInt::readSignedLong($in);
					break;
				case ScorePacketEntry::TYPE_FAKE_PLAYER:
					$entry->objectiveName = CommonTypes::getString($in);
					$entry->score = VarInt::readSignedInt($in);
					$entry->customName = CommonTypes::getString($in);
					break;
			}
			$this->entries[] = $entry;
		}
	}

	protected function encodePayload(ByteBufferWriter $out) : void{
		VarInt::writeUnsignedInt($out, count($this->entries));
		foreach($this->entries as $entry){
			$typeName = match($entry->type){
				ScorePacketEntry::TYPE_REMOVE => 'remove',
				ScorePacketEntry::TYPE_PLAYER => 'changeplayer',
				ScorePacketEntry::TYPE_ENTITY => 'changeentity',
				ScorePacketEntry::TYPE_FAKE_PLAYER => 'changefakeplayer',
				default => 'remove',
			};
			$variant = match($entry->type){
				ScorePacketEntry::TYPE_REMOVE => 0,
				ScorePacketEntry::TYPE_PLAYER => 1,
				ScorePacketEntry::TYPE_ENTITY => 2,
				ScorePacketEntry::TYPE_FAKE_PLAYER => 3,
				default => 0,
			};
			VarInt::writeUnsignedInt($out, $variant);
			CommonTypes::putString($out, $typeName);
			VarInt::writeSignedLong($out, $entry->scoreboardId);

			switch($entry->type){
				case ScorePacketEntry::TYPE_REMOVE:
					$hasObjective = ($entry->objectiveName !== '');
					$out->putBool($hasObjective);
					if($hasObjective){
						CommonTypes::putString($out, $entry->objectiveName);
					}
					break;
				case ScorePacketEntry::TYPE_PLAYER:
				case ScorePacketEntry::TYPE_ENTITY:
					CommonTypes::putString($out, $entry->objectiveName);
					VarInt::writeSignedInt($out, $entry->score);
					VarInt::writeSignedLong($out, $entry->actorUniqueId ?? 0);
					break;
				case ScorePacketEntry::TYPE_FAKE_PLAYER:
					CommonTypes::putString($out, $entry->objectiveName);
					VarInt::writeSignedInt($out, $entry->score);
					CommonTypes::putString($out, $entry->customName ?? '');
					break;
			}
		}
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleSetScore($this);
	}
}

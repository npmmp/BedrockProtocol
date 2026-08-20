<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol;

use pmmp\encoding\ByteBufferReader;
use pmmp\encoding\ByteBufferWriter;
use pmmp\encoding\LE;
use pmmp\encoding\VarInt;
use pocketmine\color\Color;
use pocketmine\network\mcpe\protocol\serializer\CommonTypes;
use pocketmine\network\mcpe\protocol\types\PlayerListEntry;
use Ramsey\Uuid\Uuid;
use function count;

class PlayerListPacket extends DataPacket implements ClientboundPacket{
	public const NETWORK_ID = ProtocolInfo::PLAYER_LIST_PACKET;

	public const TYPE_ADD = 0;
	public const TYPE_REMOVE = 1;

	public int $type;
	/** @var PlayerListEntry[] */
	public array $entries = [];

	/**
	 * @param PlayerListEntry[] $entries
	 */
	public static function add(array $entries) : self{
		$result = new self;
		$result->type = self::TYPE_ADD;
		$result->entries = $entries;
		return $result;
	}

	/**
	 * @param PlayerListEntry[] $entries
	 */
	public static function remove(array $entries) : self{
		$result = new self;
		$result->type = self::TYPE_REMOVE;
		$result->entries = $entries;
		return $result;
	}

	protected function decodePayload(ByteBufferReader $in) : void{
		$count = VarInt::readUnsignedInt($in);
		$this->entries = [];
		for($i = 0; $i < $count; ++$i){
			$entry = new PlayerListEntry();
			$entry->uuid = CommonTypes::getUUID($in);

			if($entry->uuid->equals(Uuid::fromString(Uuid::NIL))){
				$this->entries[] = $entry;
				continue;
			}

			$this->type = self::TYPE_ADD;
			$entry->actorUniqueId = CommonTypes::getActorUniqueId($in);
			$entry->username = CommonTypes::getString($in);
			$entry->xboxUserId = CommonTypes::getString($in);
			$entry->platformChatId = CommonTypes::getString($in);
			$entry->buildPlatform = LE::readSignedInt($in);
			$entry->skinData = CommonTypes::getSkin($in);
			$entry->isTeacher = CommonTypes::getBool($in);
			$entry->isHost = CommonTypes::getBool($in);
			$entry->isSubClient = CommonTypes::getBool($in);
			$entry->color = Color::fromARGB(LE::readUnsignedInt($in));
			$entry->skinData->setVerified(CommonTypes::getBool($in));
			$this->entries[] = $entry;
		}

		if(count($this->entries) === 0 || $this->type !== self::TYPE_ADD){
			$this->type = self::TYPE_REMOVE;
		}
	}

	protected function encodePayload(ByteBufferWriter $out) : void{
		VarInt::writeUnsignedInt($out, count($this->entries));
		foreach($this->entries as $entry){
			if($this->type === self::TYPE_ADD){
				CommonTypes::putUUID($out, $entry->uuid);
				CommonTypes::putActorUniqueId($out, $entry->actorUniqueId);
				CommonTypes::putString($out, $entry->username);
				CommonTypes::putString($out, $entry->xboxUserId);
				CommonTypes::putString($out, $entry->platformChatId);
				LE::writeSignedInt($out, $entry->buildPlatform);
				CommonTypes::putSkin($out, $entry->skinData);
				CommonTypes::putBool($out, $entry->isTeacher);
				CommonTypes::putBool($out, $entry->isHost);
				CommonTypes::putBool($out, $entry->isSubClient);
				LE::writeUnsignedInt($out, ($entry->color ?? new Color(255, 255, 255))->toARGB());
				CommonTypes::putBool($out, $entry->skinData->isVerified());
			}else{
				CommonTypes::putUUID($out, $entry->uuid);
			}
		}
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handlePlayerList($this);
	}
}

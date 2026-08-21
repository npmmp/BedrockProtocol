<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol;

use pmmp\encoding\Byte;
use pmmp\encoding\ByteBufferReader;
use pmmp\encoding\ByteBufferWriter;
use pmmp\encoding\LE;
use pmmp\encoding\VarInt;
use pocketmine\color\Color;
use pocketmine\network\mcpe\protocol\serializer\CommonTypes;
use pocketmine\network\mcpe\protocol\types\PlayerListEntry;
use function count;

class PlayerListPacket extends DataPacket implements ClientboundPacket{
	public const NETWORK_ID = ProtocolInfo::PLAYER_LIST_PACKET;

	public const TYPE_ADD = 0;
	public const TYPE_REMOVE = 1;

	/** @var PlayerListEntry[] */
	public array $entries = [];

	/**
	 * @param PlayerListEntry[] $entries
	 */
	public static function add(array $entries) : self{
		$result = new self;
		foreach($entries as $entry){
			$entry->actionType = self::TYPE_ADD;
		}
		$result->entries = $entries;
		return $result;
	}

	/**
	 * @param PlayerListEntry[] $entries
	 */
	public static function remove(array $entries) : self{
		$result = new self;
		foreach($entries as $entry){
			$entry->actionType = self::TYPE_REMOVE;
		}
		$result->entries = $entries;
		return $result;
	}

	protected function decodePayload(ByteBufferReader $in) : void{
		$count = VarInt::readUnsignedInt($in);
		$this->entries = [];
		for($i = 0; $i < $count; ++$i){
			$entry = new PlayerListEntry();

			$variant = VarInt::readUnsignedInt($in);
			$legacyAction = Byte::readUnsigned($in);
			$entry->actionType = ($variant === 1) ? self::TYPE_ADD : self::TYPE_REMOVE;

			$entry->uuid = CommonTypes::getUUID($in);

			if($entry->actionType === self::TYPE_REMOVE){
				$this->entries[] = $entry;
				continue;
			}

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
	}

	protected function encodePayload(ByteBufferWriter $out) : void{
		VarInt::writeUnsignedInt($out, count($this->entries));
		foreach($this->entries as $entry){
			$variant = ($entry->actionType === self::TYPE_ADD) ? 1 : 0;
			VarInt::writeUnsignedInt($out, $variant);
			Byte::writeUnsigned($out, $entry->actionType);
			CommonTypes::putUUID($out, $entry->uuid);

			if($entry->actionType === self::TYPE_REMOVE){
				continue;
			}

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
		}
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handlePlayerList($this);
	}
}

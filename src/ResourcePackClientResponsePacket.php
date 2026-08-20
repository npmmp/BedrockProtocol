<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol;

use pmmp\encoding\ByteBufferReader;
use pmmp\encoding\ByteBufferWriter;
use pmmp\encoding\VarInt;
use pocketmine\network\mcpe\protocol\serializer\CommonTypes;
use function count;

class ResourcePackClientResponsePacket extends DataPacket implements ServerboundPacket{
	public const NETWORK_ID = ProtocolInfo::RESOURCE_PACK_CLIENT_RESPONSE_PACKET;

	public const STATUS_REFUSED = 0;
	public const STATUS_SEND_PACKS = 1;
	public const STATUS_HAVE_ALL_PACKS = 2;
	public const STATUS_COMPLETED = 3;

	private const RESPONSE_NAMES = ['cancel', 'downloading', 'downloadingfinished', 'resourcepackstackfinished'];

	public int $status;
	public string $response = '';
	/** @var string[] */
	public array $packIds = [];

	/**
	 * @generate-create-func
	 * @param string[] $packIds
	 */
	public static function create(int $status, array $packIds = []) : self{
		$result = new self;
		$result->status = $status;
		$result->response = self::RESPONSE_NAMES[$status] ?? '';
		$result->packIds = $packIds;
		return $result;
	}

	protected function decodePayload(ByteBufferReader $in) : void{
		$this->status = VarInt::readUnsignedInt($in);
		$this->response = CommonTypes::getString($in);
		if($this->status === self::STATUS_SEND_PACKS){
			$entryCount = VarInt::readUnsignedInt($in);
			$this->packIds = [];
			while($entryCount-- > 0){
				$this->packIds[] = CommonTypes::getString($in);
			}
		}
	}

	protected function encodePayload(ByteBufferWriter $out) : void{
		VarInt::writeUnsignedInt($out, $this->status);
		CommonTypes::putString($out, $this->response);
		if($this->status === self::STATUS_SEND_PACKS){
			VarInt::writeUnsignedInt($out, count($this->packIds));
			foreach($this->packIds as $id){
				CommonTypes::putString($out, $id);
			}
		}
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleResourcePackClientResponse($this);
	}
}

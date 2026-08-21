<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol;

use pmmp\encoding\ByteBufferReader;
use pmmp\encoding\ByteBufferWriter;
use pmmp\encoding\LE;
use pmmp\encoding\VarInt;
use pocketmine\network\mcpe\protocol\serializer\CommonTypes;
use pocketmine\network\mcpe\protocol\types\ChunkPosition;
use pocketmine\network\mcpe\protocol\types\DimensionIds;
use pocketmine\utils\Limits;
use function count;
use const PHP_INT_MAX;

class LevelChunkPacket extends DataPacket implements ClientboundPacket{
	public const NETWORK_ID = ProtocolInfo::LEVEL_CHUNK_PACKET;

	//this appears large enough for a world height of 1024 blocks - it may need to be increased in the future
	private const MAX_BLOB_HASHES = 64;

	private ChunkPosition $chunkPosition;
	/** @phpstan-var DimensionIds::* */
	private int $dimensionId;
	private int $subChunkCount;
	private int $subChunkLimit = -1;
	/** @var int[] */
	private array $usedBlobHashes = [];
	private string $extraPayload;

	/**
	 * @generate-create-func
	 * @param int[] $usedBlobHashes
	 * @phpstan-param DimensionIds::* $dimensionId
	 */
	public static function create(ChunkPosition $chunkPosition, int $dimensionId, int $subChunkCount, int $subChunkLimit, array $usedBlobHashes, string $extraPayload) : self{
		$result = new self;
		$result->chunkPosition = $chunkPosition;
		$result->dimensionId = $dimensionId;
		$result->subChunkCount = $subChunkCount;
		$result->subChunkLimit = $subChunkLimit;
		$result->usedBlobHashes = $usedBlobHashes;
		$result->extraPayload = $extraPayload;
		return $result;
	}

	public function getChunkPosition() : ChunkPosition{ return $this->chunkPosition; }

	public function getDimensionId() : int{ return $this->dimensionId; }

	public function getSubChunkCount() : int{
		return $this->subChunkCount;
	}

	public function getSubChunkLimit() : int{
		return $this->subChunkLimit;
	}

	/**
	 * @return int[]
	 */
	public function getUsedBlobHashes() : array{
		return $this->usedBlobHashes;
	}

	public function getExtraPayload() : string{
		return $this->extraPayload;
	}

	protected function decodePayload(ByteBufferReader $in) : void{
		$this->chunkPosition = ChunkPosition::read($in);
		$this->dimensionId = VarInt::readSignedInt($in);
		$this->subChunkCount = VarInt::readUnsignedInt($in);

		$this->subChunkLimit = -1;
		$hasSubChunkLimit = $in->getBool();
		if($hasSubChunkLimit){
			$this->subChunkLimit = VarInt::readSignedInt($in);
		}

		$this->usedBlobHashes = [];
		$count = VarInt::readUnsignedInt($in);
		if($count > self::MAX_BLOB_HASHES){
			throw new PacketDecodeException("Expected at most " . self::MAX_BLOB_HASHES . " blob hashes, got " . $count);
		}
		for($i = 0; $i < $count; ++$i){
			$this->usedBlobHashes[] = LE::readUnsignedLong($in);
		}
		$this->extraPayload = CommonTypes::getString($in);
	}

	protected function encodePayload(ByteBufferWriter $out) : void{
		$this->chunkPosition->write($out);
		VarInt::writeSignedInt($out, $this->dimensionId);

		if($this->subChunkCount > 64){
			throw new \InvalidArgumentException("SubChunkCount must not exceed 64");
		}
		VarInt::writeUnsignedInt($out, $this->subChunkCount);

		if($this->subChunkLimit !== -1){
			$out->putBool(true);
			VarInt::writeSignedInt($out, $this->subChunkLimit);
		}else{
			$out->putBool(false);
		}

		VarInt::writeUnsignedInt($out, count($this->usedBlobHashes));
		foreach($this->usedBlobHashes as $hash){
			LE::writeUnsignedLong($out, $hash);
		}
		CommonTypes::putString($out, $this->extraPayload);
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleLevelChunk($this);
	}
}

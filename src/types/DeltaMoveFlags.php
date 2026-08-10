<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol\types;

/**
 * Auto-generated from Bedrock 1.26.40 protocol data.
 * Delta move flags for MoveEntityDeltaPacket.
 */
final class DeltaMoveFlags{

	public const HAS_X = 1 << 0;
	public const HAS_Y = 1 << 1;
	public const HAS_Z = 1 << 2;
	public const HAS_ROT_X = 1 << 3;
	public const HAS_ROT_Y = 1 << 4;
	public const HAS_ROT_Z = 1 << 5;
	public const ON_GROUND = 1 << 6;
	public const TELEPORT = 1 << 7;
	public const FORCE_MOVE = 1 << 8;

	private int $flags;

	public function __construct(int $flags = 0){
		$this->flags = $flags;
	}

	public static function fromBitflags(int $flags) : self{
		return new self($flags);
	}

	public function hasX() : bool{
		return ($this->flags & self::HAS_X) !== 0;
	}

	public function hasY() : bool{
		return ($this->flags & self::HAS_Y) !== 0;
	}

	public function hasZ() : bool{
		return ($this->flags & self::HAS_Z) !== 0;
	}

	public function hasRotX() : bool{
		return ($this->flags & self::HAS_ROT_X) !== 0;
	}

	public function hasRotY() : bool{
		return ($this->flags & self::HAS_ROT_Y) !== 0;
	}

	public function hasRotZ() : bool{
		return ($this->flags & self::HAS_ROT_Z) !== 0;
	}

	public function isOnGround() : bool{
		return ($this->flags & self::ON_GROUND) !== 0;
	}

	public function isTeleport() : bool{
		return ($this->flags & self::TELEPORT) !== 0;
	}

	public function isForceMove() : bool{
		return ($this->flags & self::FORCE_MOVE) !== 0;
	}

	public function getFlags() : int{
		return $this->flags;
	}
}

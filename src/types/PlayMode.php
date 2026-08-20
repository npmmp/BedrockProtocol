<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol\types;

/**
 * Enum used by PlayerAuthInputPacket.
 */
final class PlayMode{

	private function __construct(){
		//NOOP
	}

	public const NORMAL = 0;
	public const TEASER = 1;
	public const SCREEN = 2;
	public const VIEWER = 3;
	public const REALITY = 4;
	public const PLACEMENT = 5;
	public const LIVING_ROOM = 6;
	public const EXIT_LEVEL = 7;
	public const EXIT_LEVEL_LIVING_ROOM = 8;
	public const NUM_MODES = 9;
}

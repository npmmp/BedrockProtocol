<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol\types;

class ScorePacketEntry{
	public const TYPE_REMOVE = 0;
	public const TYPE_PLAYER = 1;
	public const TYPE_ENTITY = 2;
	public const TYPE_FAKE_PLAYER = 3;

	public int $scoreboardId;
	public string $objectiveName = '';
	public int $score = 0;
	public int $type;
	/** @var int|null (if type entity or player) */
	public ?int $actorUniqueId;
	/** @var string|null (if type fake player) */
	public ?string $customName;
}

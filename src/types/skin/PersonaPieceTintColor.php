<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol\types\skin;

use pocketmine\color\Color;

final class PersonaPieceTintColor{

	/**
	 * @param Color[4] $colors
	 */
	public function __construct(
		private string $pieceType,
		private array $colors
	){}

	public function getPieceType() : string{
		return $this->pieceType;
	}

	/**
	 * @return Color[4]
	 */
	public function getColors() : array{
		return $this->colors;
	}
}

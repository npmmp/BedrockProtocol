<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol\types\recipe;

use pmmp\encoding\ByteBufferReader;
use pmmp\encoding\ByteBufferWriter;
use pmmp\encoding\VarInt;
use pocketmine\network\mcpe\protocol\serializer\CommonTypes;
use function count;

final class RecipeUnlockingRequirement{

	/**
	 * @param RecipeIngredient[]|null $unlockingIngredients
	 * @phpstan-param list<RecipeIngredient>|null $unlockingIngredients
	 */
	public function __construct(
		private int $context = 0,
		private ?array $unlockingIngredients = null
	){}

	public function getContext() : int{ return $this->context; }

	/**
	 * @return RecipeIngredient[]|null
	 * @phpstan-return list<RecipeIngredient>|null
	 */
	public function getUnlockingIngredients() : ?array{ return $this->unlockingIngredients; }

	public static function read(ByteBufferReader $in) : self{
		$context = VarInt::readSignedInt($in);
		$present = $in->getBool();
		$unlockingIngredients = null;
		if($present){
			$unlockingIngredients = [];
			$count = VarInt::readUnsignedInt($in);
			for($i = 0; $i < $count; $i++){
				$unlockingIngredients[] = CommonTypes::getRecipeIngredient($in);
			}
		}

		return new self($context, $unlockingIngredients);
	}

	public function write(ByteBufferWriter $out) : void{
		VarInt::writeSignedInt($out, $this->context);
		$present = $this->unlockingIngredients !== null;
		$out->putBool($present);
		if($present){
			VarInt::writeUnsignedInt($out, count($this->unlockingIngredients));
			foreach($this->unlockingIngredients as $ingredient){
				CommonTypes::putRecipeIngredient($out, $ingredient);
			}
		}
	}
}

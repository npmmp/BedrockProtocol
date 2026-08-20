<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol;

use pmmp\encoding\ByteBufferReader;
use pmmp\encoding\ByteBufferWriter;
use pmmp\encoding\VarInt;
use pocketmine\network\mcpe\protocol\serializer\CommonTypes;
use pocketmine\network\mcpe\protocol\types\recipe\MaterialReducerRecipe;
use pocketmine\network\mcpe\protocol\types\recipe\MaterialReducerRecipeOutput;
use pocketmine\network\mcpe\protocol\types\recipe\MultiRecipe;
use pocketmine\network\mcpe\protocol\types\recipe\PotionContainerChangeRecipe;
use pocketmine\network\mcpe\protocol\types\recipe\PotionTypeRecipe;
use pocketmine\network\mcpe\protocol\types\recipe\ShapedRecipe;
use pocketmine\network\mcpe\protocol\types\recipe\ShapelessRecipe;
use pocketmine\network\mcpe\protocol\types\recipe\SmithingTransformRecipe;
use pocketmine\network\mcpe\protocol\types\recipe\SmithingTrimRecipe;
use function count;

class CraftingDataPacket extends DataPacket implements ClientboundPacket{
	public const NETWORK_ID = ProtocolInfo::CRAFTING_DATA_PACKET;

	/** @var ShapedRecipe[] */
	public array $shapedRecipes = [];
	/** @var ShapelessRecipe[] */
	public array $shapelessRecipes = [];
	/** @var MultiRecipe[] */
	public array $multiRecipes = [];
	/** @var ShapelessRecipe[] */
	public array $shulkerBoxRecipes = [];
	/** @var ShapelessRecipe[] */
	public array $shapelessChemistryRecipes = [];
	/** @var ShapedRecipe[] */
	public array $shapedChemistryRecipes = [];
	/** @var SmithingTransformRecipe[] */
	public array $smithingTransformRecipes = [];
	/** @var SmithingTrimRecipe[] */
	public array $smithingTrimRecipes = [];
	/** @var PotionTypeRecipe[] */
	public array $potionTypeRecipes = [];
	/** @var PotionContainerChangeRecipe[] */
	public array $potionContainerRecipes = [];
	/** @var MaterialReducerRecipe[] */
	public array $materialReducerRecipes = [];
	public bool $cleanRecipes = false;

	/**
	 * @generate-create-func
	 * @param ShapedRecipe[]              $shapedRecipes
	 * @param ShapelessRecipe[]           $shapelessRecipes
	 * @param MultiRecipe[]               $multiRecipes
	 * @param ShapelessRecipe[]           $shulkerBoxRecipes
	 * @param ShapelessRecipe[]           $shapelessChemistryRecipes
	 * @param ShapedRecipe[]              $shapedChemistryRecipes
	 * @param SmithingTransformRecipe[]   $smithingTransformRecipes
	 * @param SmithingTrimRecipe[]        $smithingTrimRecipes
	 * @param PotionTypeRecipe[]          $potionTypeRecipes
	 * @param PotionContainerChangeRecipe[] $potionContainerRecipes
	 * @param MaterialReducerRecipe[]     $materialReducerRecipes
	 */
	public static function create(
		array $shapedRecipes,
		array $shapelessRecipes,
		array $multiRecipes,
		array $shulkerBoxRecipes,
		array $shapelessChemistryRecipes,
		array $shapedChemistryRecipes,
		array $smithingTransformRecipes,
		array $smithingTrimRecipes,
		array $potionTypeRecipes,
		array $potionContainerRecipes,
		array $materialReducerRecipes,
		bool $cleanRecipes
	) : self{
		$result = new self;
		$result->shapedRecipes = $shapedRecipes;
		$result->shapelessRecipes = $shapelessRecipes;
		$result->multiRecipes = $multiRecipes;
		$result->shulkerBoxRecipes = $shulkerBoxRecipes;
		$result->shapelessChemistryRecipes = $shapelessChemistryRecipes;
		$result->shapedChemistryRecipes = $shapedChemistryRecipes;
		$result->smithingTransformRecipes = $smithingTransformRecipes;
		$result->smithingTrimRecipes = $smithingTrimRecipes;
		$result->potionTypeRecipes = $potionTypeRecipes;
		$result->potionContainerRecipes = $potionContainerRecipes;
		$result->materialReducerRecipes = $materialReducerRecipes;
		$result->cleanRecipes = $cleanRecipes;
		return $result;
	}

	protected function decodePayload(ByteBufferReader $in) : void{
		$this->shapedRecipes = self::decodeRecipeList($in, fn(ByteBufferReader $in) => ShapedRecipe::decode(1, $in));
		$this->shapelessRecipes = self::decodeRecipeList($in, fn(ByteBufferReader $in) => ShapelessRecipe::decode(0, $in));
		$this->multiRecipes = self::decodeRecipeList($in, fn(ByteBufferReader $in) => MultiRecipe::decode(4, $in));
		$this->shulkerBoxRecipes = self::decodeRecipeList($in, fn(ByteBufferReader $in) => ShapelessRecipe::decode(5, $in));
		$this->shapelessChemistryRecipes = self::decodeRecipeList($in, fn(ByteBufferReader $in) => ShapelessRecipe::decode(6, $in));
		$this->shapedChemistryRecipes = self::decodeRecipeList($in, fn(ByteBufferReader $in) => ShapedRecipe::decode(7, $in));
		$this->smithingTransformRecipes = self::decodeRecipeList($in, fn(ByteBufferReader $in) => SmithingTransformRecipe::decode(8, $in));
		$this->smithingTrimRecipes = self::decodeRecipeList($in, fn(ByteBufferReader $in) => SmithingTrimRecipe::decode(9, $in));

		for($i = 0, $count = VarInt::readUnsignedInt($in); $i < $count; ++$i){
			$inputId = VarInt::readSignedInt($in);
			$inputMeta = VarInt::readSignedInt($in);
			$ingredientId = VarInt::readSignedInt($in);
			$ingredientMeta = VarInt::readSignedInt($in);
			$outputId = VarInt::readSignedInt($in);
			$outputMeta = VarInt::readSignedInt($in);
			$this->potionTypeRecipes[] = new PotionTypeRecipe($inputId, $inputMeta, $ingredientId, $ingredientMeta, $outputId, $outputMeta);
		}
		for($i = 0, $count = VarInt::readUnsignedInt($in); $i < $count; ++$i){
			$input = VarInt::readSignedInt($in);
			$ingredient = VarInt::readSignedInt($in);
			$output = VarInt::readSignedInt($in);
			$this->potionContainerRecipes[] = new PotionContainerChangeRecipe($input, $ingredient, $output);
		}
		for($i = 0, $count = VarInt::readUnsignedInt($in); $i < $count; ++$i){
			$inputIdAndData = VarInt::readSignedInt($in);
			[$inputId, $inputMeta] = [$inputIdAndData >> 16, $inputIdAndData & 0x7fff];
			$outputs = [];
			for($j = 0, $outputCount = VarInt::readUnsignedInt($in); $j < $outputCount; ++$j){
				$outputItemId = VarInt::readSignedInt($in);
				$outputItemCount = VarInt::readSignedInt($in);
				$outputs[] = new MaterialReducerRecipeOutput($outputItemId, $outputItemCount);
			}
			$this->materialReducerRecipes[] = new MaterialReducerRecipe($inputId, $inputMeta, $outputs);
		}
		$this->cleanRecipes = CommonTypes::getBool($in);
	}

	/**
	 * @param callable(ByteBufferReader): mixed $decoder
	 * @return mixed[]
	 */
	private static function decodeRecipeList(ByteBufferReader $in, callable $decoder) : array{
		$count = VarInt::readUnsignedInt($in);
		$recipes = [];
		for($i = 0; $i < $count; ++$i){
			$recipes[] = $decoder($in);
		}
		return $recipes;
	}

	protected function encodePayload(ByteBufferWriter $out) : void{
		self::encodeRecipeList($out, $this->shapedRecipes);
		self::encodeRecipeList($out, $this->shapelessRecipes);
		self::encodeRecipeList($out, $this->multiRecipes);
		self::encodeRecipeList($out, $this->shulkerBoxRecipes);
		self::encodeRecipeList($out, $this->shapelessChemistryRecipes);
		self::encodeRecipeList($out, $this->shapedChemistryRecipes);
		self::encodeRecipeList($out, $this->smithingTransformRecipes);
		self::encodeRecipeList($out, $this->smithingTrimRecipes);

		VarInt::writeUnsignedInt($out, count($this->potionTypeRecipes));
		foreach($this->potionTypeRecipes as $recipe){
			VarInt::writeSignedInt($out, $recipe->getInputItemId());
			VarInt::writeSignedInt($out, $recipe->getInputItemMeta());
			VarInt::writeSignedInt($out, $recipe->getIngredientItemId());
			VarInt::writeSignedInt($out, $recipe->getIngredientItemMeta());
			VarInt::writeSignedInt($out, $recipe->getOutputItemId());
			VarInt::writeSignedInt($out, $recipe->getOutputItemMeta());
		}
		VarInt::writeUnsignedInt($out, count($this->potionContainerRecipes));
		foreach($this->potionContainerRecipes as $recipe){
			VarInt::writeSignedInt($out, $recipe->getInputItemId());
			VarInt::writeSignedInt($out, $recipe->getIngredientItemId());
			VarInt::writeSignedInt($out, $recipe->getOutputItemId());
		}
		VarInt::writeUnsignedInt($out, count($this->materialReducerRecipes));
		foreach($this->materialReducerRecipes as $recipe){
			VarInt::writeSignedInt($out, ($recipe->getInputItemId() << 16) | $recipe->getInputItemMeta());
			VarInt::writeUnsignedInt($out, count($recipe->getOutputs()));
			foreach($recipe->getOutputs() as $output){
				VarInt::writeSignedInt($out, $output->getItemId());
				VarInt::writeSignedInt($out, $output->getCount());
			}
		}
		CommonTypes::putBool($out, $this->cleanRecipes);
	}

	/**
	 * @param RecipeWithTypeId[] $recipes
	 */
	private static function encodeRecipeList(ByteBufferWriter $out, array $recipes) : void{
		VarInt::writeUnsignedInt($out, count($recipes));
		foreach($recipes as $recipe){
			$recipe->encode($out);
		}
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleCraftingData($this);
	}
}

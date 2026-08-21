<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol\types\inventory;

use pmmp\encoding\ByteBufferReader;
use pmmp\encoding\ByteBufferWriter;
use pmmp\encoding\Byte;
use pocketmine\network\mcpe\protocol\serializer\CommonTypes;

final class CreativeGroupEntry{
	public function __construct(
		private int $categoryId,
		private string $categoryName,
		private ItemStack $icon
	){}

	public function getCategoryId() : int{ return $this->categoryId; }

	public function getCategoryName() : string{ return $this->categoryName; }

	public function getIcon() : ItemStack{ return $this->icon; }

	public static function read(ByteBufferReader $in) : self{
		$categoryId = Byte::readUnsigned($in);
		$categoryName = CommonTypes::getString($in);
		$icon = CommonTypes::getItemStackWithoutStackId($in);
		return new self($categoryId, $categoryName, $icon);
	}

	public function write(ByteBufferWriter $out) : void{
		Byte::writeUnsigned($out, $this->categoryId);
		CommonTypes::putString($out, $this->categoryName);
		CommonTypes::putItemStackWithoutStackId($out, $this->icon);
	}
}

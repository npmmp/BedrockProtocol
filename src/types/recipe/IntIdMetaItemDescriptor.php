<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol\types\recipe;

use pmmp\encoding\ByteBufferReader;
use pmmp\encoding\ByteBufferWriter;
use pmmp\encoding\VarInt;
use pocketmine\network\mcpe\protocol\serializer\CommonTypes;
use pocketmine\network\mcpe\protocol\types\GetTypeIdFromConstTrait;

final class IntIdMetaItemDescriptor implements ItemDescriptor{
	use GetTypeIdFromConstTrait;

	public const ID = ItemDescriptorType::INT_ID_META;

	public function __construct(
		private string $name,
		private int $meta
	){
	}

	public function getName() : string{ return $this->name; }

	public function getMeta() : int{ return $this->meta; }

	public static function read(ByteBufferReader $in) : self{
		$name = CommonTypes::getString($in);
		$meta = VarInt::readSignedInt($in);

		return new self($name, $meta);
	}

	public function write(ByteBufferWriter $out) : void{
		CommonTypes::putString($out, $this->name);
		VarInt::writeSignedInt($out, $this->meta);
	}
}

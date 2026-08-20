<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol\types\inventory\stackresponse;

use pmmp\encoding\Byte;
use pmmp\encoding\ByteBufferReader;
use pmmp\encoding\ByteBufferWriter;
use pmmp\encoding\VarInt;
use pocketmine\network\mcpe\protocol\serializer\CommonTypes;

final class ItemStackResponseSlotInfo{
	public function __construct(
		private int $slot,
		private int $hotbarSlot,
		private int $count,
		private int $itemStackId,
		private string $customName,
		private string $filteredCustomName,
		private int $durabilityCorrection
	){}

	public function getSlot() : int{ return $this->slot; }

	public function getHotbarSlot() : int{ return $this->hotbarSlot; }

	public function getCount() : int{ return $this->count; }

	public function getItemStackId() : int{ return $this->itemStackId; }

	public function getCustomName() : string{ return $this->customName; }

	public function getFilteredCustomName() : string{ return $this->filteredCustomName; }

	public function getDurabilityCorrection() : int{ return $this->durabilityCorrection; }

	public static function read(ByteBufferReader $in) : self{
		$slot = Byte::readUnsigned($in);
		$hotbarSlot = Byte::readUnsigned($in);
		$count = Byte::readUnsigned($in);

		$itemStackId = 0;
		$hasOuter = $in->getBool();
		if($hasOuter){
			$hasInner = $in->getBool();
			if($hasInner){
				$itemStackId = VarInt::readSignedInt($in);
			}
		}

		$customName = CommonTypes::getString($in);
		$filteredCustomName = CommonTypes::getString($in);
		$durabilityCorrection = VarInt::readSignedInt($in);
		return new self($slot, $hotbarSlot, $count, $itemStackId, $customName, $filteredCustomName, $durabilityCorrection);
	}

	public function write(ByteBufferWriter $out) : void{
		Byte::writeUnsigned($out, $this->slot);
		Byte::writeUnsigned($out, $this->hotbarSlot);
		Byte::writeUnsigned($out, $this->count);

		if($this->itemStackId > 0){
			$out->putBool(true);
			$out->putBool(true);
			VarInt::writeSignedInt($out, $this->itemStackId);
		}else{
			$out->putBool(false);
		}

		CommonTypes::putString($out, $this->customName);
		CommonTypes::putString($out, $this->filteredCustomName);
		VarInt::writeSignedInt($out, $this->durabilityCorrection);
	}
}

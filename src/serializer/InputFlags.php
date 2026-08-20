<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol\serializer;

use pmmp\encoding\ByteBufferReader;
use pmmp\encoding\ByteBufferWriter;
use pmmp\encoding\VarInt;
use function array_search;
use function count;

class InputFlags{
	/** @var int[] */
	private array $ids = [];
	private bool $present = false;
	private int $size;

	public function __construct(int $size){
		$this->size = $size;
	}

	public static function read(ByteBufferReader $in, int $size) : self{
		$result = new self($size);
		$result->present = $in->getBool();
		if(!$result->present){
			return $result;
		}

		$count = VarInt::readUnsignedInt($in);
		if($count > $size){
			throw new \RuntimeException("Too many input flags: $count > $size");
		}

		$result->ids = [];
		for($i = 0; $i < $count; $i++){
			$id = VarInt::readSignedInt($in);
			if($id < 0 || $id >= $size){
				continue;
			}
			$result->ids[] = $id;
		}
		return $result;
	}

	public function write(ByteBufferWriter $out) : void{
		$out->putBool($this->present);
		if(!$this->present){
			return;
		}

		$count = count($this->ids);
		VarInt::writeUnsignedInt($out, $count);
		foreach($this->ids as $id){
			VarInt::writeSignedInt($out, $id);
		}
	}

	public function load(int $flag) : bool{
		if(!$this->present){
			return false;
		}
		return array_search($flag, $this->ids, true) !== false;
	}

	public function get(int $flag) : bool{
		return $this->load($flag);
	}

	public function set(int $flag) : void{
		$this->present = true;
		if(array_search($flag, $this->ids, true) === false){
			$this->ids[] = $flag;
		}
	}

	public function unset(int $flag) : void{
		if(!$this->present){
			return;
		}
		$key = array_search($flag, $this->ids, true);
		if($key !== false){
			unset($this->ids[$key]);
			$this->ids = array_values($this->ids);
		}
	}

	public function getIds() : array{
		return $this->ids;
	}

	public function isPresent() : bool{
		return $this->present;
	}

	public function equals(self $other) : bool{
		if($this->present !== $other->present){
			return false;
		}
		if(!$this->present){
			return true;
		}
		$a = $this->ids;
		$b = $other->ids;
		sort($a);
		sort($b);
		return $a === $b;
	}
}

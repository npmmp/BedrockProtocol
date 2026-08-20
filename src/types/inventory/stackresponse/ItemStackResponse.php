<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol\types\inventory\stackresponse;

use pmmp\encoding\Byte;
use pmmp\encoding\ByteBufferReader;
use pmmp\encoding\ByteBufferWriter;
use pmmp\encoding\VarInt;
use pocketmine\network\mcpe\protocol\serializer\CommonTypes;
use function count;

final class ItemStackResponse{

	public const RESULT_OK = 0;
	public const RESULT_ERROR = 1;

	/**
	 * @param ItemStackResponseContainerInfo[] $containerInfos
	 */
	public function __construct(
		private int $result,
		private int $requestId,
		private array $containerInfos = []
	){
	}

	public function getResult() : int{ return $this->result; }

	public function getRequestId() : int{ return $this->requestId; }

	/** @return ItemStackResponseContainerInfo[] */
	public function getContainerInfos() : array{ return $this->containerInfos; }

	public static function read(ByteBufferReader $in) : self{
		$result = Byte::readUnsigned($in);
		$requestId = CommonTypes::readItemStackRequestId($in);
		$containerInfos = [];

		$hasContainerInfoOuter = $in->getBool();
		if($hasContainerInfoOuter){
			$hasContainerInfoInner = $in->getBool();
			if($hasContainerInfoInner){
				$len = VarInt::readUnsignedInt($in);
				for($i = 0; $i < $len; ++$i){
					$containerInfos[] = ItemStackResponseContainerInfo::read($in);
				}
			}
		}

		return new self($result, $requestId, $containerInfos);
	}

	public function write(ByteBufferWriter $out) : void{
		Byte::writeUnsigned($out, $this->result);
		CommonTypes::writeItemStackRequestId($out, $this->requestId);

		if(count($this->containerInfos) > 0){
			$out->putBool(true);
			$out->putBool(true);
			VarInt::writeUnsignedInt($out, count($this->containerInfos));
			foreach($this->containerInfos as $containerInfo){
				$containerInfo->write($out);
			}
		}else{
			$out->putBool(false);
		}
	}
}

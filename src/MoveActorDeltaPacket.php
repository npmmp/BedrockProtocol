<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol;

use pmmp\encoding\ByteBufferReader;
use pmmp\encoding\ByteBufferWriter;
use pmmp\encoding\LE;
use pocketmine\network\mcpe\protocol\serializer\CommonTypes;

class MoveActorDeltaPacket extends DataPacket implements ClientboundPacket{
	public const NETWORK_ID = ProtocolInfo::MOVE_ACTOR_DELTA_PACKET;

	public int $actorRuntimeId;
	public ?float $xPos = null;
	public ?float $yPos = null;
	public ?float $zPos = null;
	public ?float $pitch = null;
	public ?float $yaw = null;
	public ?float $headYaw = null;
	public bool $onGround = false;
	public bool $forceMove = false;
	public bool $forceMoveLocalEntity = false;
	public bool $forceCompletion = false;

	protected function decodePayload(ByteBufferReader $in) : void{
		$this->actorRuntimeId = CommonTypes::getActorRuntimeId($in);
		$this->xPos = $in->getBool() ? LE::readFloat($in) : null;
		$this->yPos = $in->getBool() ? LE::readFloat($in) : null;
		$this->zPos = $in->getBool() ? LE::readFloat($in) : null;
		$this->pitch = $in->getBool() ? CommonTypes::getRotationByte($in) : null;
		$this->yaw = $in->getBool() ? CommonTypes::getRotationByte($in) : null;
		$this->headYaw = $in->getBool() ? CommonTypes::getRotationByte($in) : null;
		$this->onGround = $in->getBool();
		$this->forceMove = $in->getBool();
		$this->forceMoveLocalEntity = $in->getBool();
		$this->forceCompletion = $in->getBool();
	}

	protected function encodePayload(ByteBufferWriter $out) : void{
		CommonTypes::putActorRuntimeId($out, $this->actorRuntimeId);

		$out->putBool($this->xPos !== null);
		if($this->xPos !== null) LE::writeFloat($out, $this->xPos);

		$out->putBool($this->yPos !== null);
		if($this->yPos !== null) LE::writeFloat($out, $this->yPos);

		$out->putBool($this->zPos !== null);
		if($this->zPos !== null) LE::writeFloat($out, $this->zPos);

		$out->putBool($this->pitch !== null);
		if($this->pitch !== null) CommonTypes::putRotationByte($out, $this->pitch);

		$out->putBool($this->yaw !== null);
		if($this->yaw !== null) CommonTypes::putRotationByte($out, $this->yaw);

		$out->putBool($this->headYaw !== null);
		if($this->headYaw !== null) CommonTypes::putRotationByte($out, $this->headYaw);

		$out->putBool($this->onGround);
		$out->putBool($this->forceMove);
		$out->putBool($this->forceMoveLocalEntity);
		$out->putBool($this->forceCompletion);
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleMoveActorDelta($this);
	}
}

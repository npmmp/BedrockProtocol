<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol\types;

use pmmp\encoding\ByteBufferReader;
use pmmp\encoding\ByteBufferWriter;
use pocketmine\network\mcpe\protocol\serializer\CommonTypes;
use Ramsey\Uuid\UuidInterface;

final class GatheringJoinInfo{

	public function __construct(
		private UuidInterface $experienceId,
		private string $experienceName,
		private ?UuidInterface $experienceWorldId,
		private ?string $experienceWorldName,
		private string $creatorId,
		private ?UuidInterface $targetId,
		private ?string $scenarioId,
		private ?string $serverId,
	){}

	public function getExperienceId() : UuidInterface{ return $this->experienceId; }

	public function getExperienceName() : string{ return $this->experienceName; }

	public function getExperienceWorldId() : ?UuidInterface{ return $this->experienceWorldId; }

	public function getExperienceWorldName() : ?string{ return $this->experienceWorldName; }

	public function getCreatorId() : string{ return $this->creatorId; }

	public function getTargetId() : ?UuidInterface{ return $this->targetId; }

	public function getScenarioId() : ?string{ return $this->scenarioId; }

	public function getServerId() : ?string{ return $this->serverId; }

	public static function read(ByteBufferReader $in) : self{
		$experienceId = CommonTypes::getUUID($in);
		$experienceName = CommonTypes::getString($in);
		$experienceWorldId = CommonTypes::readOptional($in, fn(ByteBufferReader $in) => CommonTypes::getUUID($in));
		$experienceWorldName = CommonTypes::readOptional($in, fn(ByteBufferReader $in) => CommonTypes::getString($in));
		$creatorId = CommonTypes::getString($in);
		$targetId = CommonTypes::readOptional($in, fn(ByteBufferReader $in) => CommonTypes::getUUID($in));
		$scenarioId = CommonTypes::readOptional($in, fn(ByteBufferReader $in) => CommonTypes::getString($in));
		$serverId = CommonTypes::readOptional($in, fn(ByteBufferReader $in) => CommonTypes::getString($in));

		return new self(
			$experienceId,
			$experienceName,
			$experienceWorldId,
			$experienceWorldName,
			$creatorId,
			$targetId,
			$scenarioId,
			$serverId,
		);
	}

	public function write(ByteBufferWriter $out) : void{
		CommonTypes::putUUID($out, $this->experienceId);
		CommonTypes::putString($out, $this->experienceName);
		CommonTypes::writeOptional($out, $this->experienceWorldId, fn(ByteBufferWriter $out, UuidInterface $uuid) => CommonTypes::putUUID($out, $uuid));
		CommonTypes::writeOptional($out, $this->experienceWorldName, fn(ByteBufferWriter $out, string $name) => CommonTypes::putString($out, $name));
		CommonTypes::putString($out, $this->creatorId);
		CommonTypes::writeOptional($out, $this->targetId, fn(ByteBufferWriter $out, UuidInterface $uuid) => CommonTypes::putUUID($out, $uuid));
		CommonTypes::writeOptional($out, $this->scenarioId, fn(ByteBufferWriter $out, string $id) => CommonTypes::putString($out, $id));
		CommonTypes::writeOptional($out, $this->serverId, fn(ByteBufferWriter $out, string $id) => CommonTypes::putString($out, $id));
	}
}

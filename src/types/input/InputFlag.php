<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol\types\input;

/**
 * Auto-generated from Bedrock 1.26.40 protocol data.
 * Input flags for player auth input packet.
 * 
 * Changed from bitflags (varint128) encoding in 1.26.40.
 */
final class InputFlag{

	// Flag constants
	public const NONE = 0;
	public const UNSET = 1 << 0;
	public const DESCEND = 1 << 1;
	public const JUMP = 1 << 2;
	public const EMOTE = 1 << 3;
	public const USER_ACTION = 1 << 4;
	public const PERSIST_DIRECTIONS = 1 << 5;
	public const CANCEL_ALL_ACTIONS = 1 << 6;
	public const LOOKING_AHEAD = 1 << 7;
	public const BACK_WARD = 1 << 8;
	public const PITCH_TOGGLE = 1 << 9;
	public const MOVE_FORWARD = 1 << 10;
	public const MOVE_BACKWARD = 1 << 11;
	public const MOVE_LEFT = 1 << 12;
	public const MOVE_RIGHT = 1 << 13;
	public const SNEAK = 1 << 14;
	public const SPRINT = 1 << 15;
	public const START_SPINNING = 1 << 16;
	public const START_GLIDING = 1 << 17;
	public const STOP_GLIDING = 1 << 18;
	public const PERFORM_BLOCK_INTERACTION = 1 << 19;
	public const PERFORM_ITEM_INTERACTION = 1 << 20;
	public const ACKNOWLEDGE_ITEM_STACK_REQUEST = 1 << 21;
	public const REPLAY_ITEM_STACK_REQUEST = 1 << 22;
	public const BLOCK_ACTION_START = 1 << 23;
	public const BLOCK_ACTION_CANCEL = 1 << 24;
	public const ITEM_INTERACT_EARLY = 1 << 25;
	public const PERFORM_ITEM_USE_ONEntity_INTERACTION = 1 << 26;
	public const PERFORM_ITEM_USE_ON_BLOCK_INTERACTION = 1 << 27;
	public const START_ITEM_USE_ON = 1 << 28;
	public const STOP_ITEM_USE_ON = 1 << 29;
	public const HANDLED_MOB_INTERACTION = 1 << 30;
	public const PERFORM_BLOCK_ACTIONS = 1 << 31;
	public const HANDLED_BLOCK_ACTIONS = 1 << 32;
	public const CAMERA_ORIENTATION = 1 << 33;
	public const CONTINUE_INVENTORY_ACTIONS = 1 << 34;
	public const UNKNOWN_COMMAND_BLOCK_ACTION = 1 << 35;
	public const INVENTORY_ACTION = 1 << 36;
	public const UNHANDLED_INVENTORY_ACTION = 1 << 37;
	public const HOTBAR_ONLY_INTERACTION = 1 << 38;
	public const PERFORM_NON_ITEM_INTERACTION = 1 << 39;
	public const PERFORM_BLOCK_DESTRUCTION = 1 << 40;
	public const CONSUME_ITEM = 1 << 41;
	public const REVERT_ITEM_INVENTORY = 1 << 42;
	public const GRABPING = 1 << 43;
	public const SEATED_IN_BLOCK = 1 << 44;
	public const ITEM_STACK_REQUEST = 1 << 45;
	public const UPDATE_CULLING = 1 << 46;
	public const BLOCK_ACTIONS_ACK = 1 << 47;
	public const CONTAINER_ITEM_CHANGE = 1 << 48;
	public const CONTAINER_MISSED = 1 << 49;
	public const ITEM_ACTIONS_ACK = 1 << 50;
	public const HANDLED_CONTROL_ACTION = 1 << 51;
	public const UNKNOWN_AUTH_INPUT_ACTION = 1 << 52;
	public const UNKNOWN_AUTH_INPUT_ACTION2 = 1 << 53;
	public const UNKNOWN_AUTH_INPUT_ACTION3 = 1 << 54;
	public const UNKNOWN_AUTH_INPUT_ACTION4 = 1 << 55;
	public const UNKNOWN_AUTH_INPUT_ACTION5 = 1 << 56;
	public const UNKNOWN_AUTH_INPUT_ACTION6 = 1 << 57;
	public const UNKNOWN_AUTH_INPUT_ACTION7 = 1 << 58;
	public const UNKNOWN_AUTH_INPUT_ACTION8 = 1 << 59;
	public const UNKNOWN_AUTH_INPUT_ACTION9 = 1 << 60;
	public const UNKNOWN_AUTH_INPUT_ACTION10 = 1 << 61;
	public const UNKNOWN_AUTH_INPUT_ACTION11 = 1 << 62;
	public const UNKNOWN_AUTH_INPUT_ACTION12 = 1 << 63;

	private function __construct(){
		//NOOP
	}
}

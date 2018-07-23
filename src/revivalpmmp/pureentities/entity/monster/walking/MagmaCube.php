<?php

/**
 * PureEntitiesX: Mob AI Plugin for PMMP
 * Copyright (C)  2018 RevivalPMMP
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace revivalpmmp\pureentities\entity\monster\walking;

use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use revivalpmmp\pureentities\data\NBTConst;
use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use revivalpmmp\pureentities\data\Data;
use revivalpmmp\pureentities\entity\monster\WalkingMonster;
use revivalpmmp\pureentities\PluginConfiguration;
use revivalpmmp\pureentities\utils\MobDamageCalculator;

class MagmaCube extends WalkingMonster{
	const NETWORK_ID = Data::NETWORK_IDS["magma_cube"];

	private $cubeSize = -1; // 1 = Tiny, 2 = Small, 4 = Big


	public function __construct(Level $level, CompoundTag $nbt){
		$this->loadNBT($nbt);
		$this->width = 0.51;
		$this->height = 0.51;
		$this->speed = 0.8;

		$this->fireProof = true;
		$this->setDamage([0, 3, 4, 6]);
		parent::__construct($level, $nbt);
		$this->setScale($this->cubeSize);
	}

	public function saveNBT() : void{
		if(PluginConfiguration::getInstance()->getEnableNBT()){
			parent::saveNBT();
			$this->namedtag->setInt(NBTConst::NBT_KEY_CUBE_SIZE, $this->cubeSize, true);
		}
	}

	public function loadNBT(CompoundTag &$nbt){
		if(PluginConfiguration::getInstance()->getEnableNBT()){
			//parent::loadNBT();
			if($nbt->hasTag(NBTConst::NBT_KEY_CUBE_SIZE)){
				$cubeSize = $nbt->getInt(NBTConst::NBT_KEY_CUBE_SIZE, self::getRandomCubeSize());
				$this->cubeSize = $cubeSize;
			} else {
				$this->cubeSize = self::getRandomCubeSize();
				$nbt->setInt(NBTConst::NBT_KEY_CUBE_SIZE, $this->cubeSize);
			}
		}
	}

	public function getName() : string{
		return "MagmaCube";
	}

	public static function getRandomCubeSize() : int{
		($size = mt_rand(1, 3)) !== 3 ?: $size = 4;
		return $size;
	}

	/**
	 * Attack a player
	 *
	 * @param Entity $player
	 */
	public function attackEntity(Entity $player){
		if($this->attackDelay > 10 && $this->distanceSquared($player) < 1){
			$this->attackDelay = 0;
			$ev = new EntityDamageByEntityEvent($this, $player, EntityDamageEvent::CAUSE_ENTITY_ATTACK,
				MobDamageCalculator::calculateFinalDamage($player, $this->getDamage()));
			$player->attack($ev);
		}
	}

	public function getDrops() : array{
		$drops = [];
		switch(mt_rand(0, 1)){
			case 0:
				$drops[] = Item::get(Item::NETHERRACK, 0, 1);
				break;
			case 1:
				$drops[] = Item::get(Item::MAGMA_CREAM, 0, 1);
				break;
		}
		return $drops;
	}

	public function getXpDropAmount() : int{
		// normally it would be set by small/medium/big sized - but as we have it not now - i'll make it more static
		if($this->cubeSize == 2){
			return 4;
		}else if($this->cubeSize == 1){
			return 2;
		}else{
			return 1;
		}
	}


}

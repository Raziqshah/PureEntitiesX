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

namespace revivalpmmp\pureentities\entity\monster;


use pocketmine\entity\Entity;
use pocketmine\entity\Monster;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Server;
use revivalpmmp\pureentities\features\IntfBaseMob;
use revivalpmmp\pureentities\features\IntfCanPanic;
use revivalpmmp\pureentities\traits\BaseMob;

abstract class MonsterX extends Monster implements IntfBaseMob{

    use BaseMob;

    protected $attackDelay = 0;
    private $minDamage = [0, 0, 0, 0];
    private $maxDamage = [0, 0, 0, 0];
    protected $attackDistance = 2; // distance of blocks when attack can be started

    public function __construct(Level $level, CompoundTag $nbt){
        parent::__construct($level, $nbt);
        if(!$this->isFlaggedForDespawn()){
            $this->namedtag->setByte("generatedByPEX", 1, true);
            $this->baseInit($this);
        }
    }

    public function entityBaseTick(int $tickDiff = 1) : bool{
        //Timings::$timerEntityBaseTick->startTiming();
        // check if it needs to despawn

        $hasUpdate = parent::entityBaseTick($tickDiff);

        // Checking this first because there's no reason to keep going if we know
        // we're going to despawn the entity.
        if($this->checkDespawn()){
            //Timings::$timerEntityBaseTick->stopTiming();
            return false;
        }

        // check panic tick
        if($this instanceof IntfCanPanic){
            $this->panicTick($tickDiff);
        }

        //Timings::$timerEntityBaseTick->stopTiming();
        return $hasUpdate;
    }

    public function attackEntity(Entity $player){

    }

    public function getDamage(int $difficulty = null) : float{
        return mt_rand($this->getMinDamage($difficulty), $this->getMaxDamage($difficulty));
    }
    public function getMinDamage(int $difficulty = null) : float{
        if($difficulty === null or !is_numeric($difficulty) || $difficulty > 3 || $difficulty < 0){
            $difficulty = Server::getInstance()->getDifficulty();
        }
        return $this->minDamage[$difficulty];
    }
    public function getMaxDamage(int $difficulty = null) : float{
        if($difficulty === null or !is_numeric($difficulty) || $difficulty > 3 || $difficulty < 0){
            $difficulty = Server::getInstance()->getDifficulty();
        }
        return $this->maxDamage[$difficulty];
    }
    /**
     * @param float|float[] $damage
     * @param int           $difficulty
     */
    public function setDamage($damage, int $difficulty = null){
        if(is_array($damage)){
            for($i = 0; $i < 4; $i++){
                $this->minDamage[$i] = $damage[$i];
                $this->maxDamage[$i] = $damage[$i];
            }
            return;
        }elseif($difficulty === null){
            $difficulty = Server::getInstance()->getDifficulty();
        }
        if($difficulty >= 1 && $difficulty <= 3){
            $this->minDamage[$difficulty] = $damage[$difficulty];
            $this->maxDamage[$difficulty] = $damage[$difficulty];
        }
    }
    public function setMinDamage($damage, int $difficulty = null){
        if(is_array($damage)){
            for($i = 0; $i < 4; $i++){
                $this->minDamage[$i] = min($damage[$i], $this->getMaxDamage($i));
            }
            return;
        }elseif($difficulty === null){
            $difficulty = Server::getInstance()->getDifficulty();
        }
        if($difficulty >= 1 && $difficulty <= 3){
            $this->minDamage[$difficulty] = min((float) $damage, $this->getMaxDamage($difficulty));
        }
    }
    public function setMaxDamage($damage, int $difficulty = null){
        if(is_array($damage)){
            for($i = 0; $i < 4; $i++){
                $this->maxDamage[$i] = max((int) $damage[$i], $this->getMaxDamage($i));
            }
            return;
        }elseif($difficulty === null){
            $difficulty = Server::getInstance()->getDifficulty();
        }
        if($difficulty >= 1 && $difficulty <= 3){
            $this->maxDamage[$difficulty] = max((int) $damage, $this->getMaxDamage($difficulty));
        }
    }
}
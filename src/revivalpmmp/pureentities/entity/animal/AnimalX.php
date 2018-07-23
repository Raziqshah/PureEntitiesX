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

namespace revivalpmmp\pureentities\entity\animal;


use pocketmine\entity\Animal;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use revivalpmmp\pureentities\features\IntfBaseMob;
use revivalpmmp\pureentities\features\IntfCanPanic;
use revivalpmmp\pureentities\traits\BaseMob;

abstract class AnimalX extends Animal implements IntfBaseMob{

    use BaseMob;

    public function __construct(Level $level, CompoundTag $nbt){
        parent::__construct($level, $nbt);
        if(!$this->isFlaggedForDespawn()){
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
}
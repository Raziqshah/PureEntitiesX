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

namespace revivalpmmp\pureentities\features;


use pocketmine\entity\Creature;
use pocketmine\event\entity\EntityDamageEvent;
use revivalpmmp\pureentities\entity\animal\AnimalX;
use revivalpmmp\pureentities\entity\monster\MonsterX;

interface IntfBaseMob{

    /** @param AnimalX|MonsterX */
    public function baseInit($baseEntity);

    /**
     * Sets the base target for the entity. If this method is called
     * and the baseTarget is the same, nothing is set
     *
     * @param $baseTarget
     */
    public function setBaseTarget($baseTarget);

    public function getBaseTarget();

    public function getSpeed() : float;

    public function getMaxJumpHeight() : int;

    /**
     * Entity gets attacked by another entity / explosion or something similar
     *
     * @param EntityDamageEvent $source the damage event
     */
    public function attack(EntityDamageEvent $source) : void;

    public function targetOption(Creature $creature, float $distance) : bool;

    public function checkTarget(bool $checkSkip = true);

}
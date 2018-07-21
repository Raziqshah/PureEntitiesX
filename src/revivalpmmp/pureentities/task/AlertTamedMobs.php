<?php
declare(strict_types=1);
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


namespace revivalpmmp\pureentities\task;


use pocketmine\entity\Creature;
use pocketmine\Player;
use pocketmine\scheduler\AsyncTask;
use revivalpmmp\pureentities\features\IntfTameable;
use revivalpmmp\pureentities\traits\BaseMob;

class AlertTamedMobs extends AsyncTask{

    /** @var Player */
    private $victim;

    /** @var Creature */
    private $target;

    public function __construct(Player $victim, Creature $attacker){
        $this->victim = $victim;
        $this->target = $attacker;
    }

    public function onRun(){

        if($this->target instanceof Player){
            // get all tamed entities in the world and search for those belonging to the player
            foreach($this->target->getLevel()->getEntities() as $entity){
                if($entity instanceof IntfTameable and $entity->isOwner($this->victim) and !$entity->isSitting()){
                    /** @var BaseMob $entity */
                    $entity->setBaseTarget($this->target);
                }
            }
        }
    }
}
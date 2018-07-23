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

namespace revivalpmmp\pureentities\traits;


use pocketmine\entity\Creature;
use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\math\Vector3;
use pocketmine\Player;
use revivalpmmp\pureentities\components\IdlingComponent;
use revivalpmmp\pureentities\entity\animal\AnimalX;
use revivalpmmp\pureentities\entity\monster\MonsterX;
use revivalpmmp\pureentities\features\IntfTameable;
use revivalpmmp\pureentities\PluginConfiguration;
use revivalpmmp\pureentities\PureEntities;

trait BaseMob{

    private $movement = true;
    private $wallcheck = true;

    /** @var AnimalX|MonsterX */
    protected $baseEntity;
    /** @var Vector3|Entity */
    protected $baseTarget = null;
    protected $fireProof = false;
    protected $moveTime = 0;

    public $stayTime = 0;

    /**
     * Default is 1.2 blocks because entities need to be able to jump
     * just higher than the block to land on top of it.
     *
     * For Horses (and its variants) this should be 2.2
     *
     * @var float $maxJumpHeight
     */
    protected $maxJumpHeight = 1.2;
    protected $checkTargetSkipTicks = 1; // default: no skip
    public $speed = 1.0;


    /**
     * @var int
     */
    private $checkTargetSkipCounter = 0;

    /**
     * @var IdlingComponent
     */
    protected $idlingComponent;


    protected $maxAge = 0;



    /** @param AnimalX|MonsterX */
    public function baseInit($baseEntity){
        $this->baseEntity->namedtag->setByte("generatedByPEX", 1, true);
        $this->baseEntity = $baseEntity;
        $this->idlingComponent = new IdlingComponent($this->baseEntity);
        $this->checkTargetSkipTicks = PluginConfiguration::getInstance()->getCheckTargetSkipTicks();
        $this->maxAge = PluginConfiguration::getInstance()->getMaxAge();
    }

    /**
     * Sets the base target for the entity. If this method is called
     * and the baseTarget is the same, nothing is set
     *
     * @param $baseTarget
     */
    public function setBaseTarget($baseTarget){
        if($baseTarget instanceof Player and $baseTarget->getGamemode() === Player::SPECTATOR){
            return;
        }
        if($baseTarget !== $this->baseTarget){
            PureEntities::logOutput("$this: setBaseTarget to $baseTarget", PureEntities::DEBUG);
            $this->baseTarget = $baseTarget;
        }
    }

    public function getBaseTarget(){
        return $this->baseTarget;
    }

    public function getSpeed() : float{
        return $this->speed;
    }

    /**
     * @return int
     */
    public function getMaxJumpHeight() : int{
        return $this->maxJumpHeight;
    }

    /**
     * This method checks if an entity should despawn - if so, the entity is closed
     * @return bool
     */
    private function checkDespawn() : bool{
        // when entity is at least x ticks old and it's not tamed, we should remove it
        if($this->age > $this->maxAge and
            (!$this instanceof IntfTameable or ($this instanceof IntfTameable and !$this->isTamed()))
        ){
            PureEntities::logOutput("Despawn entity " . $this->getName(), PureEntities::NORM);
            $this->close();
            return true;
        }
        return false;
    }

    public function targetOption(Creature $creature, float $distance) : bool{
        return $this instanceof MonsterX && (!($creature instanceof Player) || ($creature->isSurvival() && $creature->spawned)) && $creature->isAlive() && !$creature->isClosed() && $distance <= 81;
    }

    /**
     * Checks if checkTarget can be called. If not, this method returns false
     *
     * @return bool
     */
    protected function isCheckTargetAllowedBySkip() : bool{
        if($this->checkTargetSkipCounter > $this->checkTargetSkipTicks){
            $this->checkTargetSkipCounter = 0;
            return true;
        }else{
            $this->checkTargetSkipCounter++;
            return false;
        }
    }

    /**
     * Checks if dropping loot is allowed.
     * @return bool true when allowed, false when not
     */
    protected function isLootDropAllowed() : bool{
        $lastDamageEvent = $this->getLastDamageCause();
        if($lastDamageEvent !== null and $lastDamageEvent instanceof EntityDamageByEntityEvent){
            return $lastDamageEvent->getDamager() instanceof Player;
        }
        return false;
    }

    /**
     * Checks if this entity is following a player
     *
     * @param Creature $creature the possible player
     * @return bool
     */
    protected function isFollowingPlayer(Creature $creature) : bool{
        return $this->getBaseTarget() !== null and $this->getBaseTarget() instanceof Player and $this->getBaseTarget()->getId() === $creature->getId();
    }

    public function checkTarget(bool $checkSkip = true){
        // TODO: Implement this.
    }
}
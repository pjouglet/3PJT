<?php

namespace Supinfo\CommanderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Users
 *
 * @ORM\Table(name="history")
 * @ORM\Entity(repositoryClass="Supinfo\CommanderBundle\Repository\UsersRepository")
 */
class History{

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="cost", type="integer")
     */
    private $cost;

    /**
     * @var datetime
     *
     * @ORM\Column(name="start_time", type="datetime")
     */
    private $start_time;

    /**
     * @var datetime
     *
     * @ORM\Column(name="end_time", type="datetime")
     */
    private $end_time;

    /**
     * @var string
     *
     * @ORM\Column(name="start_station", type="string", length=50)
     */
    private $start_station;

    /**
     * @var string
     *
     * @ORM\Column(name="end_station", type="string", length=50)
     */
    private $end_station;

    /**
     * @var int
     *
     * @ORM\Column(name="userid", type="integer")
     */
    private $userid;

    /**
     * @var datetime
     *
     * @ORM\Column(name="command_time", type="datetime")
     */
    private $command_time;

    public function setId($id){
        $this->id = $id;
        return $this;
    }

    public function getId(){
        return $this->id;
    }

    public function getCost(){
        return $this->cost;
    }

    public function setCost($cost){
        $this->cost = $cost;
        return $this;
    }

    public function getStart_time(){
        return $this->start_time;
    }

    public function setStart_time($start_time){
        $this->start_time = $start_time;
        return $this;
    }

    public function getEnd_time(){
        return $this->end_time;
    }

    public function setEnd_time($end_time){
        $this->end_time = $end_time;
        return $this;
    }

    public function getStart_station(){
        return $this->start_station;
    }

    public function setStart_station($start_station){
        $this->start_station = $start_station;
    }

    public function getEnd_station(){
        return $this->end_station;
    }

    public function setEnd_station($end_station){
        $this->end_station = $end_station;
    }

    public function getUserid(){
        return $this->userid;
    }

    public function setUserid($id){
        $this->userid = $id;
        return $this;
    }

    public function getCommand_time(){
        return $this->command_time;
    }

    public function setCommand_time($command_time){
        $this->command_time = $command_time;
    }
}
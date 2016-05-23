<?php

namespace Supinfo\CommanderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Users
 *
 * @ORM\Table(name="connections")
 * @ORM\Entity(repositoryClass="Supinfo\CommanderBundle\Repository\UsersRepository")
 */
class Connection{

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var datetime
     *
     * @ORM\Column(name="start_time", type="datetime")
     */
    private $start_time;

    /**
     * @var integer
     *
     * @ORM\Column(name="stationid", type="integer")
     */
    private $stationid;

    /**
     * @var integer
     *
     * @ORM\Column(name="segmentid", type="integer")
     */
    private $segmentid;

    /**
     * @var integer
     *
     * @ORM\Column(name="pathid", type="integer")
     */
    private $pathid;

    public function getId(){
        return $this->id;
    }

    public function setId($id){
        $this->id = $id;
        return $this;
    }

    public function getStartTime(){
        return $this->start_time;
    }

    public function setStartTime($startTime){
        $this->start_time = $startTime;
        return $this;
    }

    public function getStationid(){
        return $this->stationid;
    }

    public function setStationid($id){
        $this->stationid = $id;
        return $this;
    }

    public function getSegmentid(){
        return $this->segmentid;
    }

    public function setSegmentid($id){
        $this->segmentid = $id;
        return $this;
    }

    public function getPathid(){
        return $this->pathid;
    }

    public function setPathid($id){
        $this->pathid = $id;
        return $this;
    }
}
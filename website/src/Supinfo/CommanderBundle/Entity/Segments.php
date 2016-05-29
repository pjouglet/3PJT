<?php

namespace Supinfo\CommanderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Users
 *
 * @ORM\Table(name="segments")
 * @ORM\Entity(repositoryClass="Supinfo\CommanderBundle\Repository\UsersRepository")
 */
class Segments{

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var float
     *
     * @ORM\Column(name="cost", type="float")
     */
    private $cost;

    /**
     * @var integer
     *
     * @ORM\Column(name="duree", type="integer")
     */
    private $duree;

    /**
     * @var integer
     *
     * @ORM\Column(name="start_stationid", type="integer")
     */
    private $start_stationid;

    /**
     * @var integer
     *
     * @ORM\Column(name="end_stationid", type="integer")
     */
    private $end_stationid;

    public function getId(){
        return $this->id;
    }

    public function setId($id){
        $this->id = $id;
        return $this;
    }

    public function getCost(){
        return $this->cost;
    }

    public function setCost($cost){
        $this->cost = $cost;
        return $this;
    }

    public function getDuree(){
        return $this->duree;
    }

    public function setDuree($duree){
        $this->duree = $duree;
        return $this;
    }

    public function getStart_stationid(){
        return $this->start_stationid;
    }

    public function setStart_stationid($stationId){
        $this->start_stationid = $stationId;
        return $this;
    }

    public function getEnd_stationid(){
        return $this->end_stationid;
    }

    public function setEnd_stationid($stationId){
        $this->end_stationid = $stationId;
        return $this;
    }

    public function getStartStation(){
        return $this->getStart_stationid();
    }

    public function getEndStation(){
        return $this->getEnd_stationid();
    }

    public function setStartStation($stationid){
        return $this->setStart_stationid($stationid);
    }

    public function setEndStation($stationid){
        return $this->setEnd_stationid($stationid);
    }

}
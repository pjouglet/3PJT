<?php

namespace Supinfo\CommanderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Users
 *
 * @ORM\Table(name="stations")
 * @ORM\Entity(repositoryClass="Supinfo\CommanderBundle\Repository\UsersRepository")
 */
class Station{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50)
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="zoneid", type="integer")
     */
    private $zoneid;

    /**
     * @var int
     *
     * @ORM\Column(name="is_national", type="integer")
     */
    private $is_national;

    public function getId(){
        return $this->id;
    }

    public function setId($id){
        $this->id = $id;
        return $this;
    }

    public function getName(){
        return $this->name;
    }

    public function setName($name){
        $this->name = $name;
        return $this;
    }

    public function getZoneId(){
        return $this->zoneid;
    }

    public function setZoneId($zoneid){
        $this->zoneid = $zoneid;
        return $this;
    }

    public function isNational(){
        return $this->is_national;
    }

    public function setNational($value){
        $this->is_national = $value;
        return $this;
    }

}
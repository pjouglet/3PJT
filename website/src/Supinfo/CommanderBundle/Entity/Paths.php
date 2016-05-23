<?php

namespace Supinfo\CommanderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Users
 *
 * @ORM\Table(name="paths")
 * @ORM\Entity(repositoryClass="Supinfo\CommanderBundle\Repository\UsersRepository")
 */
class Path{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="is_national", type="integer")
     */
    private $is_national;

    /**
     * @var string
     *
     * @ORM\Column(name="label", type="string", length=50)
     */
    private $label;

    public function getId(){
        return $this->id;
    }

    public function setId($id){
        $this->id = $id;
        return $this;
    }

    public function isNational(){
        return $this->is_national;
    }

    public function setNational($value){
        $this->is_national = $value;
        return $this;
    }

    public function getLabel(){
        return $this->label;
    }

    public function setLabel($label){
        $this->label = $label;
        return $this;
    }
}
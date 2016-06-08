<?php

namespace Supinfo\CommanderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Users
 *
 * @ORM\Table(name="zone")
 * @ORM\Entity(repositoryClass="Supinfo\CommanderBundle\Repository\UsersRepository")
 */
class Zones{
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
     * @ORM\Column(name="label", type="string", length=50)
     */
    private $label;

    /**
     * @return string
     */
    public function __toString() {
        return $this->getLabel();
    }

    public function getId(){
        return $this->id;
    }

    public function setId($id){
        $this->id = $id;
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
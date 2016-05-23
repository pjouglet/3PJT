<?php

namespace Supinfo\CommanderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Users
 *
 * @ORM\Table(name="segments")
 * @ORM\Entity(repositoryClass="Supinfo\CommanderBundle\Repository\UsersRepository")
 */
class Segment{

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

}
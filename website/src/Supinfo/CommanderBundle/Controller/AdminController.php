<?php
/**
 * Created by PhpStorm.
 * User: Luciole
 * Date: 15/02/2016
 * Time: 10:18
 */

namespace Supinfo\CommanderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends Controller
{
    public function indexAction(){
        return $this->render('SupinfoCommanderBundle:Gestion:login.html.twig');
    }
}
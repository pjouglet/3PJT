<?php

namespace Supinfo\CommanderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('SupinfoCommanderBundle:Default:index.html.twig', array(
            'page_title' => "index"
        ));
    }

    public function loginAction(){
        return $this->render('SupinfoCommanderBundle:Default:login.html.twig', array(
            'page_title' => "login"
        ));
    }

    public function helpAction()
    {
        return $this->render('SupinfoCommanderBundle:Default:help.html.twig', array(
            'page_title' => "help"
        ));
    }
}

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
}

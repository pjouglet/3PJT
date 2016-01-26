<?php

namespace Supinfo\CommanderBundle\Controller;

use Supinfo\CommanderBundle\Entity\Users;
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
        $entityManager = $this->getDoctrine()->getManager();

        $user = new Users();
        $user->setFirstname("Pierre");
        $user->setLastname("JOUGLET");
        $user->setPassword("test");
        $user->setEmail("Pierre.JOUGLET@supinfo.com");
        $user->setNewletter(1);

        $entityManager->persist($user);
        $entityManager->flush();

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

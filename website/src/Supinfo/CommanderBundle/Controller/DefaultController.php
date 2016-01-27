<?php

namespace Supinfo\CommanderBundle\Controller;

use Supinfo\CommanderBundle\Entity\Users;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\User;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('SupinfoCommanderBundle:Default:index.html.twig', array(
            'page_title' => "index"
        ));
    }

    public function loginAction(Request $request){
        $param = array("page_title" => "login");

        //l'utilisateur se connecte
        if($request->get("login_button")){

        }

        //L'utilisateur s'enregistre
        if($request->get("signup_button")){

            if($request->get("password") == $request->get("password_confirmation")){
                $entityManager = $this->getDoctrine()->getManager();
                $user = new Users();
                $user->setFirstname($request->get("firstname"));
                $user->setLastname($request->get("lastname"));
                $user->setPassword(sha1($request->get("password")));
                $user->setEmail($request->get("email"));
                if($request->get("newsletter"))
                    $user->setNewletter(1);
                else
                    $user->setNewletter(0);
                $entityManager->persist($user);
                $entityManager->flush();
            }

        }

        return $this->render('SupinfoCommanderBundle:Default:login.html.twig', $param);
    }

    public function helpAction()
    {
        return $this->render('SupinfoCommanderBundle:Default:help.html.twig', array(
            'page_title' => "help"
        ));
    }
}

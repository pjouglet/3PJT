<?php

namespace Supinfo\CommanderBundle\Controller;

use Supinfo\CommanderBundle\Entity\Users;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
            $repo= $this->getDoctrine()->getRepository("SupinfoCommanderBundle:Users");
            $user = $repo->findOneBy(array('email' => $request->get("email_login")));

            if($user && (sha1($request->get('password_login')) == $user->getPassword())){
                //User connecté
                $session = $request->getSession();
                $session->set("email", $request->get("email_login"));

                //L'utilisateur reste connecté
                if($request->get("stay_logged")){
                    $cookie = new Cookie('commander_cookie_login', $request->get('email_login'));
                    $response = new Response();
                    $response->headers->setCookie($cookie);
                    $response->send();
                }
            }
            else{
                //Erreur de connection (L'utilisateur n'existe pas ou les mots de passes ne correspondent pas)
                $param["login_error"] = "true";
            }
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

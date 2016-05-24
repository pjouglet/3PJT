<?php

namespace Supinfo\CommanderBundle\Controller;

use Supinfo\CommanderBundle\Entity\Users;
use Supinfo\CommanderBundle\Form\LoginForm;
use Supinfo\CommanderBundle\Form\RegisterForm;
use Supinfo\CommanderBundle\SupinfoCommanderBundle;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        $this->checkCookie();
        return $this->render('SupinfoCommanderBundle:Default:index.html.twig', array(
            'page_title' => "index"
        ));
    }

    public function loginAction(Request $request){
        $this->checkCookie();
        $session = $request->getSession();

        //Si l'utilisateur est connecté, on le redirige sur la page d'accueil
        if($session->get('email')){
            return $this->redirect($this->generateUrl('supinfo_commander_homepage'));
        }

        $registerForm = $this->createForm(new RegisterForm());
        $registerForm->handleRequest($request);

        $loginForm = $this->createForm(new LoginForm());
        $loginForm->handleRequest($request);

        $param = array(
            "page_title" => "login",
            "register_form" => $registerForm->createView(),
            'login_form' => $loginForm->createView()
        );


        //l'utilisateur se connecte
        if($loginForm->isSubmitted()){
            $repo= $this->getDoctrine()->getRepository("SupinfoCommanderBundle:Users");
            /** @var $user Users*/
            $user = $repo->findOneBy(array('email' => $loginForm->get("email_login")->getData()));

            if($user && (sha1($loginForm->get('password_login')->getData()) == $user->getPassword()) && $user->getActive() == 1){
                //User connecté
                $session->set("email", $loginForm->get("email_login")->getData());

                $entityManager = $this->getDoctrine()->getManager();
                $user->setIp($_SERVER["REMOTE_ADDR"]);
                $entityManager->flush();

                //L'utilisateur reste connecté
                if($loginForm->get("stay_logged")->getData()){
                    $cookie = new Cookie('commander_cookie_login', $loginForm->get('email_login')->getData(), time() + 3600 * 24 * 365);
                    $response = new Response();
                    $response->headers->setCookie($cookie);
                    $response->send();
                }
                return $this->redirect("/");
            }
            else{
                //Erreur de connection (L'utilisateur n'existe pas ou les mots de passes ne correspondent pas)
                $param["login_error"] = "true";
            }
        }

        //L'utilisateur s'enregistre
        if($registerForm->isSubmitted()){
            if($registerForm->get("password")->getData() == $registerForm->get("password_confirmation")->getData()){
                $entityManager = $this->getDoctrine()->getManager();
                $repo= $this->getDoctrine()->getRepository("SupinfoCommanderBundle:Users");
                if(!$repo->findOneBy(array('email' => $registerForm->get('email')->getData()))){
                    $user = new Users();
                    $user->setFirstname($registerForm->get("firstname")->getData());
                    $user->setLastname($registerForm->get("lastname")->getData());
                    $user->setPassword(sha1($registerForm->get("password")->getData()));
                    $user->setEmail($registerForm->get("email")->getData());
                    $user->setActive(1);
                    $user->setIp($_SERVER["REMOTE_ADDR"]);
                    if($registerForm->get("newsletter")->getData())
                        $user->setNewletter(1);
                    else
                        $user->setNewletter(0);
                    $entityManager->persist($user);
                    $entityManager->flush();
                    $param["user_created"] = "true";
                }
                else{
                    $param["user_exist"] = "true";
                }
            }
        }
        return $this->render('SupinfoCommanderBundle:Default:login.html.twig', $param);
    }

    public function helpAction()
    {
        $this->checkCookie();
        return $this->render('SupinfoCommanderBundle:Default:help.html.twig', array(
            'page_title' => "help"
        ));
    }

    public function logoutAction(Request $request){
        $session = $request->getSession();
        $session->invalidate();

        $response = new Response();
        $response->headers->clearCookie('commander_cookie_login');
        $response->send();
        return $this->redirect("/");
    }

    public function cartAction(Request $request){
        $this->checkCookie();

        return $this->render("SupinfoCommanderBundle:Default:cart.html.twig", array(
            'page_title' => "Panier"
        ));
    }

    private function checkCookie(){
        $request = $this->get('request');
        $cookies = $request->cookies;
        if($cookies->has('commander_cookie_login')){
            $session = $this->get('session');
            $session->set("email", $cookies->get('commander_cookie_login'));
        }
    }
}

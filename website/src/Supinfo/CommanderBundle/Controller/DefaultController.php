<?php

namespace Supinfo\CommanderBundle\Controller;

use Supinfo\CommanderBundle\Entity\History;
use Supinfo\CommanderBundle\Entity\Users;
use Supinfo\CommanderBundle\Form\LoginForm;
use Supinfo\CommanderBundle\Form\ProfileForm;
use Supinfo\CommanderBundle\Form\RegisterForm;
use Supinfo\CommanderBundle\Form\SearchTravelForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        if($this->checkCookie() == "maintenance_ok"){
            return $this->render("SupinfoCommanderBundle:Default:maintenance.html.twig", array(
                'page_title' => "Maintenance"
            ));
        }

        //print_r($request);die;

        $form = $this->createForm(new SearchTravelForm());
        $form->handleRequest($request);

        $param = array(
            'page_title' => "index",
            'form' => $form->createView()
        );

        if($form->isSubmitted()){
            if($form->get('start_station')->getData() != $form->get('end_station')->getData()){
                $station_startId = $this->getDoctrine()->getRepository('SupinfoCommanderBundle:Stations')->findOneBy(array('name' => $form->get('start_station')->getData()));
                $station_endId = $this->getDoctrine()->getRepository('SupinfoCommanderBundle:Stations')->findOneBy(array('name' => $form->get('end_station')->getData()));
                /** @var \DateTime $dateStart */
                $dateStart = $form->get('start_day')->getData();
                /** @var \DateTime $timeStart */
                $timeStart = $form->get('start_time')->getData();

                $dateStart->setTime($timeStart->format('H'), $timeStart->format('i'), $timeStart->format('s'));

                /** @var \DateTime $dateEnd */
                $dateEnd = $form->get('end_day')->getData();
                /** @var \DateTime $timeEnd */
                $timeEnd = $form->get('end_time')->getData();
                $dateStart->setTime($timeStart->format('H'), $timeStart->format('i'), $timeStart->format('s'));
                $dateEnd->setTime($timeEnd->format('H'), $timeEnd->format('i'), $timeEnd->format('s'));

                $start = $dateStart->getTimestamp() + 7200;
                $end = $dateEnd->getTimestamp() + 7200;

                $result = @file_get_contents('http://notemonminou.hol.es/api/journeys/time/'.$station_startId->getId().'/'.$station_endId->getId().'/'.$start.'/'.$end);
                if($result != null){

                }
                else{
                    $param['travel_not_found'] = true;
                }
            }
            else{
                $param['same_station'] = true;
            }
        }
        return $this->render('SupinfoCommanderBundle:Default:index.html.twig', $param);
    }

    public function loginAction(Request $request){
        if($this->checkCookie() == "maintenance_ok"){
            return $this->render("SupinfoCommanderBundle:Default:maintenance.html.twig", array(
                'page_title' => "Maintenance"
            ));
        }
        $session = $request->getSession();

        //Si l'utilisateur est connecté, on le redirige sur la page d'accueil
        if($session->get('email')){
            return $this->redirect($this->generateUrl('supinfo_commander_homepage'));
        }

        //Google Auth
        $client = new \Google_Client();
        $client->setClientId($this->getParameter('client_id_google'));
        $client->setClientSecret($this->getParameter('client_id_secret_google'));
        $client->setRedirectUri("http://train-commander.dev".$this->generateUrl('supinfo_commander_login'));
        $client->addScope("email");
        $client->addScope("profile");

        if (isset($_GET['code'])) {
            $client->authenticate($_GET['code']);

            $google_auth = new \Google_Service_Oauth2($client);
            /** @var Users $user */
            $user = $this->getDoctrine()->getRepository("SupinfoCommanderBundle:Users")->findOneBy(array('email' => $google_auth->userinfo->get()->getEmail()));
            if(!$user){
                $user = new Users();
                $user->setFirstname($google_auth->userinfo->get()->getName());
                $user->setLastname($google_auth->userinfo->get()->getFamilyName());
                $user->setEmail($google_auth->userinfo->get()->getEmail());
                $user->setActive(1);
                $user->setNewletter(0);
                $user->setGoogleid($google_auth->userinfo->get()->getId());
                $user->setIp($_SERVER["REMOTE_ADDR"]);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
            }else{
                if($user->getGoogleid() != $google_auth->userinfo->get()->getId()){
                    $user->setGoogleid($google_auth->userinfo->get()->getId());
                    $this->getDoctrine()->getManager()->flush();
                }
            }
            $session->set("email", $google_auth->userinfo->get()->getEmail());
            return $this->redirect($this->generateUrl('supinfo_commander_homepage'));
        }

        $registerForm = $this->createForm(new RegisterForm());
        $registerForm->handleRequest($request);

        $loginForm = $this->createForm(new LoginForm());
        $loginForm->handleRequest($request);

        $param = array(
            "page_title" => "login",
            "register_form" => $registerForm->createView(),
            'login_form' => $loginForm->createView(),
            'google_auth_url' => $client->createAuthUrl()
        );


        //l'utilisateur se connecte
        if($loginForm->isSubmitted()){
            $repo= $this->getDoctrine()->getRepository("SupinfoCommanderBundle:Users");
            /** @var $user Users*/
            $user = $repo->findOneBy(array('email' => $loginForm->get("email_login")->getData()));
            if($user && !is_null($loginForm->get('password_login')->getData()) && (sha1($loginForm->get('password_login')->getData()) == $user->getPassword()) && $user->getActive() == 1){
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
                return $this->redirect($this->generateUrl('supinfo_commander_homepage'));
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
        if($this->checkCookie() == "maintenance_ok"){
            return $this->render("SupinfoCommanderBundle:Default:maintenance.html.twig", array(
                'page_title' => "Maintenance"
            ));
        }
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
        return $this->redirect($this->generateUrl('supinfo_commander_homepage'));
    }

    public function cartAction(Request $request){
        if($this->checkCookie() == "maintenance_ok"){
            return $this->render("SupinfoCommanderBundle:Default:maintenance.html.twig", array(
                'page_title' => "Maintenance"
            ));
        }
        $session = $this->get('session');
        if(!$session->get('email')){
            return $this->redirect($this->generateUrl('supinfo_commander_login'));
        }

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
        
        //page de maintenance
        if($this->getDoctrine()->getRepository("SupinfoCommanderBundle:Configuration")->findOneBy(array('key' => 'maintenance'))->getValue() == 1){
            $ip_list = explode(';', $this->getDoctrine()->getRepository("SupinfoCommanderBundle:Configuration")->findOneBy(array('key' => 'maintenance_ip'))->getValue());
            if(!in_array($_SERVER['REMOTE_ADDR'], $ip_list)){
                return 'maintenance_ok';
            }
        }
    }

    public function profileAction(Request $request){
        if($this->checkCookie() == "maintenance_ok"){
            return $this->render("SupinfoCommanderBundle:Default:maintenance.html.twig", array(
                'page_title' => "Maintenance"
            ));
        }

        $session = $this->get('session');
        if(!$session->get('email')){
            return $this->redirect($this->generateUrl('supinfo_commander_login'));
        }

        $repo= $this->getDoctrine()->getRepository("SupinfoCommanderBundle:Users");
        /** @var Users $user */
        $user = $repo->findOneBy(array('email' => $this->get('session')->get('email')));
        if($user){
            $password = $user->getPassword();
            $email = $user->getEmail();
        }
        $form = $this->createForm(new ProfileForm(), $user);
        $form->handleRequest($request);

        $param = array(
            'page_title' => 'Page de profil',
            'form' => $form->createView()
        );

        $history = $this->getDoctrine()->getRepository("SupinfoCommanderBundle:History")->findBy(array('userid' => $user->getId()));
        if(count($history) > 0)
            $param['history'] = $history;

        if($form->isSubmitted()){
            if(sha1($form->get('password')->getData()) == $password){
                $entityManager = $this->getDoctrine()->getManager();
                if($email == $form->get('email')->getData()){
                    $user->setFirstname($form->get('firstname')->getData());
                    $user->setLastname($form->get('lastname')->getData());
                    $user->setPassword($password);
                    $entityManager->persist($user);
                    $entityManager->flush();
                    $param['change_ok'] = true;
                }
                else{
                    /** @var Users $mail */
                    $mail = $repo->findOneBy(array('email' => $form->get('email')->getData()));
                    if(!$mail){
                        $user->setEmail($form->get('email')->getData());
                        $user->setFirstname($form->get('firstname')->getData());
                        $user->setLastname($form->get('lastname')->getData());
                        $user->setPassword($password);
                        $entityManager->persist($user);
                        $entityManager->flush();
                        $this->get('session')->set('email', $form->get('email')->getData());
                        $param['change_ok'] = true;
                    }
                    else{
                        $param['email_exist'] = true;
                    }
                }
            }
            else{
                $param['password_not_ok'] = true;
            }
        }

        return $this->render('SupinfoCommanderBundle:Default:profil.html.twig', $param);
    }

    public function printTravelAction($id, Request $request){
        if($this->checkCookie() == "maintenance_ok"){
            return $this->render("SupinfoCommanderBundle:Default:maintenance.html.twig", array(
                'page_title' => "Maintenance"
            ));
        }

        $session = $this->get('session');
        if(!$session->get('email')){
            return $this->redirect($this->generateUrl('supinfo_commander_login'));
        }

        /** @var Users $currentUser */
        $currentUser = $this->getDoctrine()->getRepository("SupinfoCommanderBundle:Users")->findOneBy(array('email' => $session->get('email')));
        /** @var History $travel */
        $travel = $this->getDoctrine()->getRepository("SupinfoCommanderBundle:History")->findOneBy(array('id' => $id, 'userid' => $currentUser->getId()));
        if($travel){
            try{
                $pdf = new \HTML2PDF('P', 'A4', 'fr', true, 'UTF-8', 0);
                $pdf->pdf->SetDisplayMode('fullpage');
                $content = $this->renderView("SupinfoCommanderBundle:Default:ticket.html.twig", array('ticket' => $travel, 'firstname' => $currentUser->getFirstname(), 'lastname' => $currentUser->getLastname()));
                $pdf->writeHTML($content, false);
                $file = $pdf->Output('commande.pdf');
                $response = new Response();
                //$response->clearHttpHeaders();
                $response->setContent(file_get_contents($file));
                $response->headers->set('Content-Type', 'application/force-download');
                $response->headers->set('Content-disposition', 'filename='. $file);

                return $response;
            }
            catch (\HTML2PDF_exception $ex){
                echo $ex;
                die;
            }
        }
    }
}

<?php

namespace Supinfo\CommanderBundle\Controller;

use Facebook\Facebook;
use PayPal\Api\Amount;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Exception\PayPalConnectionException;
use PayPal\Rest\ApiContext;
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
use Symfony\Component\Validator\Constraints\DateTime;

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

                $start = $dateStart->getTimestamp();
                $end = $dateEnd->getTimestamp();

                $result = @file_get_contents('http://notemonminou.hol.es/api/journeys/time/'.$station_startId->getId().'/'.$station_endId->getId().'/'.$start.'/'.$end);
                if($result != null){
                    $results = json_decode($result);
                    $this->get('session')->set('search_result', $result);
                    $param = array(
                        'page_title' => 'Résultat de la recherche',
                        'results' => $results
                    );
                    return $this->render('SupinfoCommanderBundle:Default:choice.html.twig', $param);
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
        if($session->get('id')){
            return $this->redirect($this->generateUrl('supinfo_commander_homepage'));
        }

        //Google Auth
        $client = new \Google_Client();
        $client->setClientId($this->getParameter('client_id_google'));
        $client->setClientSecret($this->getParameter('client_id_secret_google'));
        $client->setRedirectUri($this->getParameter('base_url').$this->generateUrl('supinfo_commander_login'));
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
                $session->set('id', $user->getId());
            }else{
                if($user->getGoogleid() != $google_auth->userinfo->get()->getId()){
                    $user->setGoogleid($google_auth->userinfo->get()->getId());
                    $this->getDoctrine()->getManager()->flush();
                    $session->set('id', $user->getId());
                }
            }
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

        //Facebook Auth
        $facebook = new Facebook([
            'app_id' => $this->getParameter('client_id_facebook'),
            'app_secret' => $this->getParameter('client_id_secret_facebook'),
            'default_graph_version' => 'v2.5',
        ]);
        $helper = $facebook->getRedirectLoginHelper();
        $param['facebook_login_url'] = $helper->getLoginUrl($this->getParameter('base_url').$this->generateUrl('supinfo_commander_facebook'));

        //l'utilisateur se connecte
        if($loginForm->isSubmitted()){
            $repo= $this->getDoctrine()->getRepository("SupinfoCommanderBundle:Users");
            /** @var $user Users*/
            $user = $repo->findOneBy(array('email' => $loginForm->get("email_login")->getData()));
            if($user && !is_null($loginForm->get('password_login')->getData()) && (sha1($loginForm->get('password_login')->getData()) == $user->getPassword()) && $user->getActive() == 1){
                //User connecté
                $session->set("id", $user->getId());

                $entityManager = $this->getDoctrine()->getManager();
                $user->setIp($_SERVER["REMOTE_ADDR"]);
                $entityManager->flush();

                //L'utilisateur reste connecté
                if($loginForm->get("stay_logged")->getData()){
                    $cookie = new Cookie('commander_cookie_login', $user->getId(), time() + 3600 * 24 * 365);
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

                    //Envoie de mail au client pour confirmer son inscription
                    $message = \Swift_Message::newInstance()
                        ->setSubject('Merci de vous être inscrit !')
                        ->setFrom($this->getParameter('mailer_from'))
                        ->setTo($user->getEmail())
                        ->setBody(
                            $this->renderView('Emails/registration.html.twig', array('user' => $user)), 'text/html'
                        );
                    $this->get('mailer')->send($message);
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

    public function cartAction($id = null, Request $request){
        if($this->checkCookie() == "maintenance_ok"){
            return $this->render("SupinfoCommanderBundle:Default:maintenance.html.twig", array(
                'page_title' => "Maintenance"
            ));
        }
        $session = $this->get('session');
        if(!$session->get('id')){
            return $this->redirect($this->generateUrl('supinfo_commander_login'));
        }

        $param = array(
            'page_title' => "Panier"
        );

        if($id == null)
            $param['no_result'] = true;
        else{
            if($session->get('search_result') != null){
                $result = json_decode($session->get('search_result'));
                $param['result'] = $result[$id];
                $session->set('result',$result[$id]);

                var_dump($session->get('result'));die;

                $context = new ApiContext(new OAuthTokenCredential(
                    'AZCKtnAXRbHuVB9TyswDNXBdSyOJMLt1eWxg-sZZqsigcD6L9eIVhKeaRB-QkPlAOF0kGpw15ubeYJu9',
                    'EG2yploW1bJI8pMqFALpgtXEDK7L6qLkJ-WS-zBekCm42Xa_HjTYmmmgKES8U15IgJdXXuX3ML4iS0_N'
                ));

                $payer = new Payer();
                $payer->setPaymentMethod('paypal');
                $item = new Item();
                $item->setName("Train-commander voyage")->setCurrency('EUR')->setQuantity(1)->setPrice($param['result']->price);

                $itemlist = new ItemList();
                $itemlist->setItems(array($item));

                $amount = new Amount();
                $amount->setCurrency("EUR")->setTotal($param['result']->price);

                $transaction = new Transaction();
                $transaction->setAmount($amount)->setItemList($itemlist)->setInvoiceNumber(uniqid());

                $redirecturl = new RedirectUrls();
                $redirecturl->setReturnUrl($this->getParameter('base_url').$this->generateUrl('supinfo_commander_paypal'))
                    ->setCancelUrl($this->getParameter('base_url').$this->generateUrl('supinfo_commander_homepage'));
                $payment = new Payment();
                $payment->setIntent('sale')->setRedirectUrls($redirecturl)->setTransactions(array($transaction))->setPayer($payer);

                try{
                    $payment->create($context);
                    $param['paypal_url'] = $payment->getApprovalLink();
                }
                catch (PayPalConnectionException $ex){
                    echo $ex->getCode();
                    echo $ex->getData();die;
                    //die($ex);
                }
            }
        }

        return $this->render("SupinfoCommanderBundle:Default:cart.html.twig", $param);
    }

    private function checkCookie(){
        $request = $this->get('request');
        $cookies = $request->cookies;
        if($cookies->has('commander_cookie_login')){
            $session = $this->get('session');
            $session->set("id", $cookies->get('commander_cookie_login'));
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
        if(!$session->get('id')){
            return $this->redirect($this->generateUrl('supinfo_commander_login'));
        }

        $repo= $this->getDoctrine()->getRepository("SupinfoCommanderBundle:Users");
        /** @var Users $user */
        $user = $repo->findOneBy(array('id' => $this->get('session')->get('id')));
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
                        $this->get('session')->set('id', $user->getId());
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
        if(!$session->get('id')){
            return $this->redirect($this->generateUrl('supinfo_commander_login'));
        }

        /** @var Users $currentUser */
        $currentUser = $this->getDoctrine()->getRepository("SupinfoCommanderBundle:Users")->findOneBy(array('id' => $session->get('id')));
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

    public function facebookAction(Request $request){

        if($this->checkCookie() == "maintenance_ok"){
            return $this->render("SupinfoCommanderBundle:Default:maintenance.html.twig", array(
                'page_title' => "Maintenance"
            ));
        }

        //Facebook Auth
        $facebook = new Facebook([
            'app_id' => $this->getParameter('client_id_facebook'),
            'app_secret' => $this->getParameter('client_id_secret_facebook'),
            'default_graph_version' => 'v2.5',
        ]);
        $helper = $facebook->getRedirectLoginHelper();

        $access_token = $helper->getAccessToken();

        if(isset($access_token)){
            $facebook->setDefaultAccessToken($access_token);
            $response = $facebook->get('/me');
            $user = $response->getGraphUser();

            /** @var Users $fbUser */
            $fbUser = $this->getDoctrine()->getRepository("SupinfoCommanderBundle:Users")->findOneBy(array('fbid' => $user->getId()));
            if($user && !$fbUser){
                $fbUser = new Users();
                $fbUser->setEmail($user->getEmail());
                $fbUser->setFirstname($user->getFirstName());
                $fbUser->setLastname($user->getLastName());
                $fbUser->setFbid($user->getId());
                $fbUser->setActive(1);
                $fbUser->setNewletter(0);
                $fbUser->setIp($_SERVER["REMOTE_ADDR"]);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($fbUser);
                $entityManager->flush();
                $this->get('session')->set("id", $fbUser->getId());
            }else{
                $this->get('session')->set("id", $fbUser->getId());
            }
            return $this->redirect($this->generateUrl('supinfo_commander_homepage'));
        }
    }

    public function deleteCartAction(Request $request){
        if($this->checkCookie() == "maintenance_ok"){
            return $this->render("SupinfoCommanderBundle:Default:maintenance.html.twig", array(
                'page_title' => "Maintenance"
            ));
        }
        $session = $this->get('session');
        if(!$session->get('id')){
            return $this->redirect($this->generateUrl('supinfo_commander_login'));
        }

        $session->set('search_result', null);
        return $this->redirect($this->generateUrl('supinfo_commander_cart'));
    }

    public function paypalAction(Request $request){
        if($this->checkCookie() == "maintenance_ok"){
            return $this->render("SupinfoCommanderBundle:Default:maintenance.html.twig", array(
                'page_title' => "Maintenance"
            ));
        }
        $session = $this->get('session');
        if(!$session->get('id')){
            return $this->redirect($this->generateUrl('supinfo_commander_login'));
        }

        $result = $session->get('result');

        $dateStart = new \DateTime();
        $dateStart->setTimestamp($result->startTimes);
        $dateEnd = new \DateTime();
        $dateEnd->setTimestamp($result->endTimes);

        $history = new History();
        $history->setCost($result->price);
        $history->setStart_time($dateStart);
        $history->setEnd_time($dateEnd);
        $history->setStart_station($result->stations[0]);
        $history->setEnd_station($result->stations[1]);
        $history->setUserid($session->get('id'));
        $history->setCommand_time(new \DateTime('now'));

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($history);
        $entityManager->flush();

        //Envoie de mail au client pour confirmer son achat
        /** @var Users $user */
        $user = $this->getDoctrine()->getRepository('SupinfoCommanderBundle:Users')->findOneBy(array('id' => $session->get('id')));
        $message = \Swift_Message::newInstance()
            ->setSubject('Merci de vous être inscrit !')
            ->setFrom($this->getParameter('mailer_from'))
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView('Emails/confirmation.html.twig', array('user' => $user, 'result' => $result)), 'text/html'
            );
        $this->get('mailer')->send($message);

        return $this->render($this->generateUrl('supinfo_commander_homepage'));
    }

    public function rebuyAction($id, Request $request){
        if($this->checkCookie() == "maintenance_ok"){
            return $this->render("SupinfoCommanderBundle:Default:maintenance.html.twig", array(
                'page_title' => "Maintenance"
            ));
        }

        $session = $this->get('session');
        if(!$session->get('id')){
            return $this->redirect($this->generateUrl('supinfo_commander_login'));
        }

        /** @var Users $currentUser */
        $currentUser = $this->getDoctrine()->getRepository("SupinfoCommanderBundle:Users")->findOneBy(array('id' => $session->get('id')));
        /** @var History $travel */
        $travel = $this->getDoctrine()->getRepository("SupinfoCommanderBundle:History")->findOneBy(array('id' => $id, 'userid' => $currentUser->getId()));
        if($travel){
            $form = $this->createForm(new SearchTravelForm());
            $form->handleRequest($request);

            $param = array(
                'page_title' => "index",
                'form' => $form->createView(),
                'start_station' => $travel->getStart_station(),
                'end_station' => $travel->getEnd_station()
            );
            return $this->render("SupinfoCommanderBundle:Default:index.html.twig", $param);
        }
        return $this->redirect($this->generateUrl('supinfo_commander_profil'));
    }
}

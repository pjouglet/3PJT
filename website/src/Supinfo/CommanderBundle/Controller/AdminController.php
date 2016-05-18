<?php
/**
 * Created by PhpStorm.
 * User: Luciole
 * Date: 15/02/2016
 * Time: 10:18
 */

namespace Supinfo\CommanderBundle\Controller;

use Supinfo\CommanderBundle\Form\AdminLoginForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends Controller
{
    public function loginAction(Request $request){
        $session = $request->getSession();

        if($session->get('email_admin')){
            return $this->redirect($this->generateUrl('supinfo_commander_admin'));
        }

        $loginForm = $this->createForm(new AdminLoginForm());
        $loginForm->handleRequest($request);


        $param = array('login_form' => $loginForm->createView());

        //Lorsqu'on se connecte
        if($loginForm->isSubmitted()){
            $repo = $this->getDoctrine()->getRepository("SupinfoCommanderBundle:Employees");
            $user = $repo->findOneBy(array('email' => $loginForm->get('email')->getData()));
            if($user && (sha1($loginForm->get('password')->getData()) == $user->getPassword())){
                $session->set("email_admin", $loginForm->get('email')->getData());
                return $this->redirect($this->generateUrl('supinfo_commander_admin'));
            }
            $param['login_error'] = true;
        }

        return $this->render('SupinfoCommanderBundle:Gestion:login.html.twig', $param);
    }

    public function indexAction(Request $request){
        $session = $request->getSession();
        if(!$session->get('email_admin')){
            return $this->redirect($this->generateUrl('supinfo_commander_admin_login'));
        }
        return $this->render('SupinfoCommanderBundle:Gestion:index.html.twig', array('page_title' => "Panneau d'administration"));
    }

    public function websiteAction(Request $request){
        $session = $request->getSession();
        $session->set("email_admin", null);
        return $this->redirect($this->generateUrl('supinfo_commander_homepage'));
    }
}
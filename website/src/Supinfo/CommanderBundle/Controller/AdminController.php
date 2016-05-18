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

class AdminController extends Controller
{
    public function loginAction(Request $request){
        $session = $request->getSession();
        if($session->get('email_admin')){
            return $this->redirect($this->generateUrl('supinfo_commander_admin'));
        }

        //Lorsqu'on se connecte
        if($request->get("login_button")){
            $repo = $this->getDoctrine()->getRepository("SupinfoCommanderBundle:Employees");
            $user = $repo->findOneBy(array('email' => $request->get('email')));

            if($user && (sha1($request->get('password')) == $user->getPassword())){
                $session->set("email_admin", $request->get("email"));
                return $this->redirect($this->generateUrl('supinfo_commander_admin'));
            }
        }
        return $this->render('SupinfoCommanderBundle:Gestion:login.html.twig');
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
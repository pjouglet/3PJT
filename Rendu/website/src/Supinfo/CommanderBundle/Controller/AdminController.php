<?php
/**
 * Created by PhpStorm.
 * User: Luciole
 * Date: 15/02/2016
 * Time: 10:18
 */

namespace Supinfo\CommanderBundle\Controller;

use Supinfo\CommanderBundle\Entity\Zones;
use Supinfo\CommanderBundle\Form\AddZoneForm;
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

        $param = array(
            'page_title' => "Panneau d'administration",
            'users' => $this->getDoctrine()->getRepository("SupinfoCommanderBundle:Users")->findAll(),
            'carts' => $this->getDoctrine()->getRepository("SupinfoCommanderBundle:History")->findAll()
        );
        return $this->render('SupinfoCommanderBundle:Gestion:index.html.twig', $param);
    }

    public function websiteAction(Request $request){
        $session = $request->getSession();
        $session->set("email_admin", null);
        return $this->redirect($this->generateUrl('supinfo_commander_homepage'));
    }

    public function viewZonesAction(Request $request){
        $session = $request->getSession();
        if(!$session->get('email_admin')){
            return $this->redirect($this->generateUrl('supinfo_commander_admin_login'));
        }

        $param = array(
            'page_title' => "Zones",
            'zones_list' =>  $this->getDoctrine()->getRepository("SupinfoCommanderBundle:Zones")->findAll()
        );

        return $this->render('SupinfoCommanderBundle:Gestion:zone/zones.html.twig', $param);
    }

    public function addZoneAction(Request $request){
        $session = $request->getSession();
        if(!$session->get('email_admin')){
            return $this->redirect($this->generateUrl('supinfo_commander_admin_login'));
        }

        $form = $this->createForm(new AddZoneForm());
        $form->handleRequest($request);
        
        if($form->isSubmitted()){
            $repo= $this->getDoctrine()->getRepository("SupinfoCommanderBundle:Zones");
            if(!$repo->findOneBy(array('label' => $form->get("label")->getData()))){
                $entityManager = $this->getDoctrine()->getManager();

                $zone = new Zones();
                $zone->setLabel($form->get('label')->getData());

                $entityManager->persist($zone);
                $entityManager->flush();
                return $this->redirect($this->generateUrl('supinfo_commander_administration_view_zones'));
            }
            else{
                $param['zone_exist'] = true;
            }
        }

        $param = array(
            'page_title' => "Ajouter une zone",
            'form' => $form->createView()
        );

        return $this->render('SupinfoCommanderBundle:Gestion:zone/add.html.twig', $param);
    }

    public function deleteZoneAction($id, Request $request){
        $session = $request->getSession();
        if(!$session->get('email_admin')){
            return $this->redirect($this->generateUrl('supinfo_commander_admin_login'));
        }

        $repo= $this->getDoctrine()->getRepository("SupinfoCommanderBundle:Zones");
        $zone = $repo->findOneBy(array('id'=> $id));
        if($zone){
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($zone);
            $entityManager->flush();
        }

        return $this->redirect($this->generateUrl('supinfo_commander_administration_view_zones'));
    }

    public function editZoneAction($id, Request $request){
        $session = $request->getSession();
        if(!$session->get('email_admin')){
            return $this->redirect($this->generateUrl('supinfo_commander_admin_login'));
        }

        $repo= $this->getDoctrine()->getRepository("SupinfoCommanderBundle:Zones");
        $zone = $repo->findOneBy(array('id'=> $id));

        if(!$zone)
            return $this->redirect($this->generateUrl("supinfo_commander_administration_view_zones"));

        $param = array(
            'page_title' => "Modifier une zone",
        );

        $form = $this->createForm(new AddZoneForm(), $zone);
        $form->handleRequest($request);

        if($form->isSubmitted()){
            $zone->setLabel($form->get('label')->getData());
            $this->getDoctrine()->getManager()->flush();
            $param['label_edited'] = 'true';
        }

        $param['form'] = $form->createView();

        return $this->render('SupinfoCommanderBundle:Gestion:zone/edit.html.twig', $param);
    }
}
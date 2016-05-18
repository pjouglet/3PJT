<?php

namespace Supinfo\CommanderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CustomerController extends Controller{
    
    public function customersAction(Request $request){
        $session = $request->getSession();
        if(!$session->get('email_admin')){
            return $this->redirect($this->generateUrl('supinfo_commander_admin_login'));
        }

        $param = array(
            'page_title' => 'Clients',
            'customers_list' => $this->getDoctrine()->getRepository("SupinfoCommanderBundle:Users")->findAll()
        );

        return $this->render('SupinfoCommanderBundle:Gestion:customer/customers.html.twig', $param);
    }

    public function deleteCustomerAction($id, Request $request){
        $session = $request->getSession();
        if(!$session->get('email_admin')){
            return $this->redirect($this->generateUrl('supinfo_commander_admin_login'));
        }

        $repo= $this->getDoctrine()->getRepository("SupinfoCommanderBundle:Users");
        $user = $repo->findOneBy(array('id'=> $id));
        if($user){
            $entityManager = $this->getDoctrine()->getManager();
            $user->setActive(0);
            $entityManager->flush();
        }

        return $this->redirect($this->generateUrl('supinfo_commander_administration_customers'));
    }

    public function activateCustomerAction($id, Request $request){
        $session = $request->getSession();
        if(!$session->get('email_admin')){
            return $this->redirect($this->generateUrl('supinfo_commander_admin_login'));
        }

        $repo= $this->getDoctrine()->getRepository("SupinfoCommanderBundle:Users");
        $user = $repo->findOneBy(array('id'=> $id));
        if($user){
            $entityManager = $this->getDoctrine()->getManager();
            $user->setActive(1);
            $entityManager->flush();
        }

        return $this->redirect($this->generateUrl('supinfo_commander_administration_customers'));
    }

    public function viewCustomerAction($id, Request $request){
        $session = $request->getSession();
        if(!$session->get('email_admin')){
            return $this->redirect($this->generateUrl('supinfo_commander_admin_login'));
        }

        $param =array(
            'page_title' => "Informations du client"
        );

        return $this->render('SupinfoCommanderBundle:Gestion:customer/view.html.twig', $param);
    }
    
}
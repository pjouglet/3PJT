<?php

namespace Supinfo\CommanderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class StationsController extends Controller{

    public function viewAllStationsAction(Request $request){
        $session = $request->getSession();
        if(!$session->get('email_admin')){
            return $this->redirect($this->generateUrl('supinfo_commander_admin_login'));
        }

        $param = array(
            'page_title' => "Gares",
        );

        return $this->render('SupinfoCommanderBundle:Gestion:stations/stations.html.twig', $param);
    }

    public function addStationAction(Request $request){
        $session = $request->getSession();
        if(!$session->get('email_admin')){
            return $this->redirect($this->generateUrl('supinfo_commander_admin_login'));
        }

        $param = array(
            'page_title' => "Ajouter une gare",
        );

        return $this->render('SupinfoCommanderBundle:Gestion:stations/add.html.twig', $param);
    }
}
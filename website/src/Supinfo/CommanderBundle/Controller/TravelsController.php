<?php

namespace Supinfo\CommanderBundle\Controller;

use Supinfo\CommanderBundle\Entity\Stations;
use Supinfo\CommanderBundle\Form\AddStationForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TravelsController extends Controller{

    public function viewTravelsAction(Request $request){
        $session = $request->getSession();
        if(!$session->get('email_admin')){
            return $this->redirect($this->generateUrl('supinfo_commander_admin_login'));
        }

        $param = array(
            'page_title' => "Trajets",
        );

        return $this->render('SupinfoCommanderBundle:Gestion:travels/travels.html.twig', $param);
    }

}
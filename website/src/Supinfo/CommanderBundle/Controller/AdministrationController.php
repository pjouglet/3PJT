<?php

namespace Supinfo\CommanderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AdministrationController extends Controller
{
    public function maintenanceAction(Request $request){
        $session = $request->getSession();
        if(!$session->get('email_admin')){
            return $this->redirect("/gestion");
        }

        return $this->render('SupinfoCommanderBundle:Gestion:maintenance.html.twig', array('page_title' => 'Maintenance'));
    }
}
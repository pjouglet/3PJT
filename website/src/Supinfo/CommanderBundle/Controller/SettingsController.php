<?php

namespace Supinfo\CommanderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SettingsController extends Controller
{
    public function informationsAction(Request $request){
        $session = $request->getSession();
        if(!$session->get('email_admin')){
            return $this->redirect("/gestion");
        }

        $param = array(
            'page_title' => 'Informations',
            'server_infos' => $_SERVER['SERVER_SOFTWARE'],
            'php' => phpversion(),
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'bdd_driver' => $this->getParameter('database_driver'),
            'bdd_server' => $this->getParameter('database_host'),
            'bdd_port' => $this->getParameter('database_port'),
            'bdd_name' => $this->getParameter('database_name'),
            'bdd_user' => $this->getParameter('database_user'),
            'mail_transport' => $this->getParameter('mailer_transport'),
            'mail_host' => $this->getParameter('mailer_host'),
            'mail_user' => $this->getParameter('mailer_user'),
            'navigateur' => $_SERVER['HTTP_USER_AGENT'],
            'user_host' => $_SERVER['REMOTE_ADDR'],
            'user_port' => $_SERVER['REMOTE_PORT']
        );
        return $this->render('SupinfoCommanderBundle:Gestion:informations.html.twig', $param);
    }
}
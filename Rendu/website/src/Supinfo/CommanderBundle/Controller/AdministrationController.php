<?php

namespace Supinfo\CommanderBundle\Controller;

use Supinfo\CommanderBundle\Entity\Configuration;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AdministrationController extends Controller
{
    public function maintenanceAction(Request $request){
        $session = $request->getSession();
        if(!$session->get('email_admin')){
            return $this->redirect($this->generateUrl('supinfo_commander_admin'));
        }

        $repo = $this->getDoctrine()->getRepository("SupinfoCommanderBundle:Configuration");
        $entitymanager = $this->getDoctrine()->getEntityManager();
        /** @var Configuration $ips */
        $ips = $repo->findOneBy(array('key' => 'maintenance_ip'));
        /** @var Configuration $activate */
        $activate = $repo->findOneBy(array('key' => 'maintenance'));


        if($request->get('ip_list')){
            $ips->setValue($request->get('ip_list'));
            $entitymanager->flush();
        }

        if($request->get('btn_activate') || $request->get('btn_desactivate')){
            $activate->setValue(($request->get('btn_activate') ? 0 : 1));
            $entitymanager->flush();
        }

        $param = array(
            'page_title' => 'Maintenance',
            'user_ip' => $_SERVER['REMOTE_ADDR'],
            'ips' => $ips->getValue(),
            'maintenance' =>$activate->getValue()
        );

        return $this->render('SupinfoCommanderBundle:Gestion:maintenance.html.twig', $param);
    }
}
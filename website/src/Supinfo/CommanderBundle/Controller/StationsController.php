<?php

namespace Supinfo\CommanderBundle\Controller;

use Supinfo\CommanderBundle\Entity\Stations;
use Supinfo\CommanderBundle\Form\AddStationForm;
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
            'stations_list' =>  $this->getDoctrine()->getRepository("SupinfoCommanderBundle:Stations")->findAll(),
            'zones_list' => $this->getDoctrine()->getRepository("SupinfoCommanderBundle:Zones")->findAll()
        );

        return $this->render('SupinfoCommanderBundle:Gestion:stations/stations.html.twig', $param);
    }

    public function addStationAction(Request $request){
        $session = $request->getSession();
        if(!$session->get('email_admin')){
            return $this->redirect($this->generateUrl('supinfo_commander_admin_login'));
        }
        
        $form = $this->createForm(new AddStationForm());
        $form->handleRequest($request);

        if($form->isSubmitted()){
            $repo= $this->getDoctrine()->getRepository("SupinfoCommanderBundle:Stations");
            if(!$repo->findOneBy(array('name' => $form->get("name")->getData()))){
                $entityManager = $this->getDoctrine()->getManager();

                $station = new Stations();
                $station->setName($form->get('name')->getData());
                $station->setNational(($form->get('national')->getData() == 'Oui') ? 1 : 0);
                $station->setZoneId($form->get('zone')->getData()->getId());

                $entityManager->persist($station);
                $entityManager->flush();
                return $this->redirect($this->generateUrl('supinfo_commander_administration_view_stations'));
            }
            else{
                $param['zone_exist'] = true;
            }
        }

        $param = array(
            'page_title' => "Ajouter une gare",
            'form' => $form->createView()
        );

        return $this->render('SupinfoCommanderBundle:Gestion:stations/add.html.twig', $param);
    }

    public function deleteStationAction($id, Request $request){
        $session = $request->getSession();
        if(!$session->get('email_admin')){
            return $this->redirect($this->generateUrl('supinfo_commander_admin_login'));
        }

        $repo= $this->getDoctrine()->getRepository("SupinfoCommanderBundle:Stations");
        $station = $repo->findOneBy(array('id'=> $id));
        if($station){
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($station);
            $entityManager->flush();
        }

        return $this->redirect($this->generateUrl('supinfo_commander_administration_view_stations'));
    }

    public function editStationAction($id, Request $request){
        $session = $request->getSession();
        if(!$session->get('email_admin')){
            return $this->redirect($this->generateUrl('supinfo_commander_admin_login'));
        }

        $repo= $this->getDoctrine()->getRepository("SupinfoCommanderBundle:Stations");
        $station = $repo->findOneBy(array('id'=> $id));

        if(!$station)
            return $this->redirect($this->generateUrl("supinfo_commander_administration_view_Stations"));

        $param = array(
            'page_title' => "Modifier une gare",
        );

        $form = $this->createForm(new AddStationForm(), $station);
        $form->handleRequest($request);

        if($form->isSubmitted()){
            $station->setName($form->get('name')->getData());
            $station->setNational(($form->get('national')->getData() == 'Oui') ? 1 : 0);
            $station->setZoneId($form->get('zone')->getData()->getId());
            $this->getDoctrine()->getManager()->flush();
            $param['station_edited'] = 'true';
        }

        $param['form'] = $form->createView();

        return $this->render('SupinfoCommanderBundle:Gestion:stations/edit.html.twig', $param);
    }
}
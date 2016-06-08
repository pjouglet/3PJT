<?php

namespace Supinfo\CommanderBundle\Controller;

use Supinfo\CommanderBundle\Entity\Connection;
use Supinfo\CommanderBundle\Entity\Connections;
use Supinfo\CommanderBundle\Entity\Paths;
use Supinfo\CommanderBundle\Entity\Segments;
use Supinfo\CommanderBundle\Entity\Stations;
use Supinfo\CommanderBundle\Form\AddStationForm;
use Supinfo\CommanderBundle\Form\AddTrajetForm;
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
            'travels_list' => $this->getDoctrine()->getRepository("SupinfoCommanderBundle:Paths")->findAll(),
        );

        return $this->render('SupinfoCommanderBundle:Gestion:travels/travels.html.twig', $param);
    }

    public function addTravelAction(Request $request){
        $session = $request->getSession();
        if(!$session->get('email_admin')){
            return $this->redirect($this->generateUrl('supinfo_commander_admin_login'));
        }
        
        $form = $this->createForm(new AddTrajetForm());
        $form->handleRequest($request);

        if($form->isSubmitted()){
            $repo= $this->getDoctrine()->getRepository("SupinfoCommanderBundle:Paths");
            $entityManager = $this->getDoctrine()->getManager();
            if(!$repo->findOneBy(array('label' => $form->get('label')->getData()))){
                //Ajout du trajet
                $travel = new Paths();
                $travel->setLabel($form->get('label')->getData());
                $travel->setNational(($form->get('national')->getData() == 'Oui') ? 1 : 0);
                $entityManager->persist($travel);
                $entityManager->flush();

                //Ajout de la connection
                /** @var \DateTime $date */
                $date = $form->get('start_time')->getData();
                $stations = explode(';', $form->get('stations')->getData());
                unset($stations[count($stations)-1]);
                foreach ($stations as $key => $value) {
                    if($key != count($stations)-1){
                        $connection = new Connections();
                        $connection->setStartTime(new \DateTime());
                        $connection->setStationid($value);
                        $connection->setPathid($travel->getId());
                        $segment_repo = $this->getDoctrine()->getRepository("SupinfoCommanderBundle:Segments");
                        /** @var Segments $segment */
                        $segment = $segment_repo->findOneBy(array('start_stationid' => $value, 'end_stationid' => $stations[$key +1]));
                        $connection->setSegmentid($segment->getId());
                        if($key == 0){
                            $connection->setStartTime($date);
                        }
                        else{
                            /** @var Segments $stationDown */
                            $stationDown = $segment_repo->findOneBy(array('start_stationid' => $stations[$key -1], 'end_stationid' => $value));
                            $date->add(\DateInterval::createFromDateString($stationDown->getDuree().' seconds'));
                            $connection->setStartTime($date);
                        }

                        $entityManager->persist($connection);
                        $entityManager->flush();
                    }
                }
                return $this->redirect($this->generateUrl('supinfo_commander_administration_view_travels'));
            }
            else{
                $param['travel_exist'] = true;
            }
        }
        $param = array(
            'page_title' => 'Ajouter un trajet',
            'form' => $form->createView(),
            'stations_list' => $this->getDoctrine()->getRepository("SupinfoCommanderBundle:Stations")->findAll()
        );

        return $this->render('SupinfoCommanderBundle:Gestion:travels/add.html.twig', $param);
    }

    public function deleteTravelAction($id, Request $request){
        $session = $request->getSession();
        if(!$session->get('email_admin')){
            return $this->redirect($this->generateUrl('supinfo_commander_admin_login'));
        }

        $repo= $this->getDoctrine()->getRepository("SupinfoCommanderBundle:Paths");
        $travel = $repo->findOneBy(array('id'=> $id));
        if($travel){
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($travel);
            $entityManager->flush();
        }

        return $this->redirect($this->generateUrl('supinfo_commander_administration_view_travels'));
    }

    public function editTravelAction($id, Request $request){
        $session = $request->getSession();
        if(!$session->get('email_admin')){
            return $this->redirect($this->generateUrl('supinfo_commander_admin_login'));
        }

        $repo= $this->getDoctrine()->getRepository("SupinfoCommanderBundle:Paths");
        /** @var Paths $travel */
        $travel = $repo->findOneBy(array('id'=> $id));

        if(!$travel)
            return $this->redirect($this->generateUrl("supinfo_commander_administration_view_travels"));

        $param = array(
            'page_title' => "Modifier un trajet",
        );

        $form = $this->createForm(new AddTrajetForm(), $travel);
        $form->handleRequest($request);

        if($form->isSubmitted()){
            $travel->setLabel($form->get('label')->getData());
            $travel->setNational(($form->get('national')->getData() == 'Oui') ? 1 : 0);
            $this->getDoctrine()->getManager()->flush();
            $param['travel_edited'] = 'true';
        }

        $param['form'] = $form->createView();

        return $this->render('SupinfoCommanderBundle:Gestion:travels/edit.html.twig', $param);
    }

}
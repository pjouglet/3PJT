<?php

namespace Supinfo\CommanderBundle\Controller;

use Supinfo\CommanderBundle\Entity\Employees;
use Supinfo\CommanderBundle\Entity\Segments;
use Supinfo\CommanderBundle\Form\AddEmployeeForm;
use Supinfo\CommanderBundle\Form\AddSegmentForm;
use Supinfo\CommanderBundle\SupinfoCommanderBundle;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SegmentsController extends Controller{

    public function viewSegmentsAction(Request $request){
        $session = $request->getSession();
        if(!$session->get('email_admin')){
            return $this->redirect($this->generateUrl('supinfo_commander_admin_login'));
        }

        $param = array(
            'page_title' => "Segments",
            'segments_list' => $this->getDoctrine()->getRepository("SupinfoCommanderBundle:Segments")->findAll(),
            'stations_list' => $this->getDoctrine()->getRepository("SupinfoCommanderBundle:Stations")->findAll()
        );

        return $this->render('SupinfoCommanderBundle:Gestion:segments/segments.html.twig', $param);
    }

    public function addSegmentAction(Request $request){
        $session = $request->getSession();
        if(!$session->get('email_admin')){
            return $this->redirect($this->generateUrl('supinfo_commander_admin_login'));
        }

        $form = $this->createForm(new AddSegmentForm());
        $form->handleRequest($request);

        $param = array(
            'page_title' => "Ajouter un segment",
            'form' => $form->createView()
        );

        if($form->isSubmitted()){
            $repo= $this->getDoctrine()->getRepository("SupinfoCommanderBundle:Segments");
            $entityManager = $this->getDoctrine()->getManager();
            if(!$repo->findOneBy(array('start_stationid' => $form->get('start_station')->getData()->getId(), 'end_stationid' => $form->get('end_station')->getData()->getId()))){
                $segment = new Segments();
                $segment->setCost($form->get('cost')->getData());
                $segment->setDuree($form->get('duree')->getData());
                $segment->setStart_stationid($form->get('start_station')->getData()->getId());
                $segment->setEnd_stationid($form->get('end_station')->getData()->getId());

                $entityManager->persist($segment);
                $entityManager->flush();
                return $this->redirect($this->generateUrl('supinfo_commander_administration_view_segments'));
            }
            else{
                $param['segment_exist'] = true;
            }
        }

        return $this->render('SupinfoCommanderBundle:Gestion:segments/add.html.twig', $param);
    }

    public function deleteSegmentAction($id, Request $request){
        $session = $request->getSession();
        if(!$session->get('email_admin')){
            return $this->redirect($this->generateUrl('supinfo_commander_admin_login'));
        }

        $repo= $this->getDoctrine()->getRepository("SupinfoCommanderBundle:Segments");
        $station = $repo->findOneBy(array('id'=> $id));
        if($station){
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($station);
            $entityManager->flush();
        }

        return $this->redirect($this->generateUrl('supinfo_commander_administration_view_segments'));
    }

    public function editSegmentAction($id, Request $request){
        $session = $request->getSession();
        if(!$session->get('email_admin')){
            return $this->redirect($this->generateUrl('supinfo_commander_admin_login'));
        }

        $param = array(
            'page_title' => 'Edition d\'un segment'
        );

        $repo= $this->getDoctrine()->getRepository("SupinfoCommanderBundle:Segments");
        /** @var Segments $segment */
        $segment = $repo->findOneBy(array('id'=> $id));

        if(!$segment)
            return $this->redirect($this->generateUrl("supinfo_commander_administration_view_segments"));

        $form = $this->createForm(new AddSegmentForm(), $segment);
        $form->handleRequest($request);

        if($form->isSubmitted()){
            $segment->setCost($form->get('cost')->getData());
            $segment->setDuree($form->get('duree')->getData());
            $segment->setStart_stationid($form->get('start_station')->getData()->getId());
            $segment->setEnd_stationid($form->get('end_station')->getData()->getId());
            $this->getDoctrine()->getManager()->flush();
            $param['segment_edited'] = 'true';
        }

        $param['form'] = $form->createView();

        return $this->render('SupinfoCommanderBundle:Gestion:segments/edit.html.twig', $param);
    }
}
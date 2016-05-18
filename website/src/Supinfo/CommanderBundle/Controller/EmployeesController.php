<?php

namespace Supinfo\CommanderBundle\Controller;

use Supinfo\CommanderBundle\Entity\Employees;
use Supinfo\CommanderBundle\Form\AddEmployeeForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class EmployeesController extends Controller{

    public function employeesAction(Request $request){
        $session = $request->getSession();
        if(!$session->get('email_admin')){
            return $this->redirect($this->generateUrl('supinfo_commander_admin_login'));
        }

        $param = array(
            'page_title' => "Employés",
            'employees_list' => $this->getDoctrine()->getRepository("SupinfoCommanderBundle:Employees")->findAll()
        );

        return $this->render('SupinfoCommanderBundle:Gestion:employee/employees.html.twig', $param);
    }

    public function addEmployeeAction(Request $request){
        $session = $request->getSession();
        if(!$session->get('email_admin')){
            return $this->redirect($this->generateUrl('supinfo_commander_admin_login'));
        }

        $form = $this->createForm(new AddEmployeeForm());
        $form->handleRequest($request);

        $param = array(
            'page_title' => "Ajouter un employé",
        );

        if($form->isSubmitted()){
            $repo= $this->getDoctrine()->getRepository("SupinfoCommanderBundle:Employees");
            if(!$repo->findOneBy(array('email' => $form->get("email")->getData()))){
                $entityManager = $this->getDoctrine()->getManager();

                $employee = new Employees();
                $employee->setFirstname($form->get('firstname')->getData());
                $employee->setLastname($form->get('lastname')->getData());
                $employee->setEmail($form->get('email')->getData());
                $employee->setPassword(sha1($form->get('password')->getData()));

                $entityManager->persist($employee);
                $entityManager->flush();
                return $this->redirect($this->generateUrl('supinfo_commander_administration_employees'));
            }
            else{
                $param['user_exist'] = true;

            }
        }

        $param['form'] = $form->createView();

        return $this->render('SupinfoCommanderBundle:Gestion:employee/add.html.twig', $param);
    }

    public function deleteEmployeeAction($id, Request $request){
        $session = $request->getSession();
        if(!$session->get('email_admin')){
            return $this->redirect($this->generateUrl('supinfo_commander_admin_login'));
        }

        $repo= $this->getDoctrine()->getRepository("SupinfoCommanderBundle:Employees");
        $user = $repo->findOneBy(array('id_employee'=> $id));
        if($user){
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirect($this->generateUrl('supinfo_commander_administration_employees'));
    }
    
    public function editEmployeeAction($id, Request $request){
        $session = $request->getSession();
        if(!$session->get('email_admin')){
            return $this->redirect($this->generateUrl('supinfo_commander_admin_login'));
        }

        $repo= $this->getDoctrine()->getRepository("SupinfoCommanderBundle:Employees");
        $user = $repo->findOneBy(array('id_employee'=> $id));

        if(!$user)
            return $this->redirect($this->generateUrl("supinfo_commander_administration_employees"));

        $param = array(
            'page_title' => "Ajouter un employé",
        );
        $password = $user->getpassword();
        $form = $this->createForm(new AddEmployeeForm(), $user);
        $form->handleRequest($request);

        if($form->isSubmitted()){
            $user->setFirstname($form->get('firstname')->getData());
            $user->setLastname($form->get('lastname')->getData());
            $user->setEmail($form->get('email')->getData());
            $user->setPassword($password);
            $this->getDoctrine()->getManager()->flush();
            $param['user_edited'] = 'true';
        }

        $param['form'] = $form->createView();

        return $this->render('SupinfoCommanderBundle:Gestion:employee/edit.html.twig', $param);
    }
}
<?php

namespace Supinfo\CommanderBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AddEmployeeForm extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       $builder->add('firstname', 'text', array(
           'block_name' => 'firstname',
           'attr' => array(
               'class' => 'form-control'
           )
       ))->add('lastname', 'text', array(
           'block_name' => 'lastname',
           'attr' => array(
               'class' => 'form-control'
           )
       ))->add('email', 'email', array(
           'block_name' => 'email',
           'attr' => array(
               'class' => 'form-control'
           )
       ))->add('password', 'password', array(
           'block_name' => 'password',
           'attr' => array(
               'class' => 'form-control'
           )
       ));
    }
}


<?php

namespace Supinfo\CommanderBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdminLoginForm extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('email', 'email', array(
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
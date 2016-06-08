<?php
namespace Supinfo\CommanderBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class LoginForm extends AbstractType {
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('email_login', 'email', array(
            'block_name' => 'email_login',
            'attr' => array(
                'class' => 'form-control'
            )
        ))->add('password_login', 'password', array(
            'block_name' => 'password_login',
            'attr' => array(
                'class' => 'form-control'
            )
        ))->add('stay_logged', 'checkbox', array(
            'block_name' => 'stay_logged',
            'required' => false
        ));
    }

    /**
     * @return String
     */
    public function getName()
    {
        return "Supinfo_CommanderBundle_Form_LoginForm";
    }
}
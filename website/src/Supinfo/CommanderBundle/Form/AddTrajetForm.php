<?php

namespace Supinfo\CommanderBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AddTrajetForm extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('label', 'text', array(
            'block_name' => 'label',
            'attr' => array(
                'class' => 'form-control'
            )
        ))->add('national', 'choice', array(
            'block_name' => 'national',
            'choices' => array(
                'Oui' => 'Oui',
                'Non' => 'Non'
            ),
            'attr' => array(
                'class' => 'form-control'
            )
        ));
    }
}


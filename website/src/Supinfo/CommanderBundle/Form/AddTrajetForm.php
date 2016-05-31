<?php

namespace Supinfo\CommanderBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
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
        ))->add('start_time', DateTimeType::class, array(
            'block_name' => 'start_time',

        ))->add('stations', 'text', array(
            'block_name' => 'stations',
            'attr' => array(
                'class' => 'form-control hidden'
            )
        ));
    }
}


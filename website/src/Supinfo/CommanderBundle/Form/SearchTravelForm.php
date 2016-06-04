<?php

namespace Supinfo\CommanderBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;

class SearchTravelForm extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('start_station', 'text', array(
            'block_name' => 'start_station',
            'attr' => array(
                'class' => 'form-control'
            )
        ))->add('end_station', 'text', array(
            'block_name' => 'end_station',
            'attr' => array(
                'class' => 'form-control'
            )
        ))->add('start_day', DateType::class, array(
            'block_name' => 'start_day',
            'model_timezone' => "Europe/Paris"
        ))->add('end_day', DateType::class, array(
            'block_name' => 'end_day',
            'model_timezone' => "Europe/Paris"
        ))->add('start_time', TimeType::class, array(
            'block_name' => 'start_time',
            'model_timezone' => "Europe/Paris"
        ))->add('end_time', TimeType::class, array(
            'block_name' => 'end_time',
            'model_timezone' => "Europe/Paris"
        ));
    }
}
<?php

namespace Supinfo\CommanderBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AddZoneForm extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('label', 'text', array(
            'block_name' => 'label',
            'attr' => array(
                'class' => 'form-control'
            )
        ));
    }
}


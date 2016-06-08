<?php

namespace Supinfo\CommanderBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AddSegmentForm extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('cost', 'text', array(
            'block_name' => 'cost',
            'attr' => array(
                'class' => 'form-control'
            )
        ))->add('duree', 'text', array(
            'block_name' => 'duree',
            'attr' => array(
                'class' => 'form-control'
            )
        ))->add('start_station', 'entity', array(
            'block_name' => 'start_station',
            'class' => 'Supinfo\CommanderBundle\Entity\Stations',
            'query_builder' => function(EntityRepository $er){
                $queryBuilder = $er->createQueryBuilder('z');
                return $queryBuilder->orderBy('z.id', 'ASC');
            },
            'attr' => array(
                'class' => 'form-control'
            )
        ))->add('end_station', 'entity', array(
            'block_name' => 'end_station',
            'class' => 'Supinfo\CommanderBundle\Entity\Stations',
            'query_builder' => function(EntityRepository $er){
                $queryBuilder = $er->createQueryBuilder('z');
                return $queryBuilder->orderBy('z.id', 'ASC');
            },
            'attr' => array(
                'class' => 'form-control'
            )
        ));
    }
}


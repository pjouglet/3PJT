<?php

namespace Supinfo\CommanderBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class AddStationForm extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', array(
            'block_name' => 'name',
            'attr' => array(
                'class' => 'form-control'
            )
        ))->add('zone', 'entity', array(
            'block_name' => 'zone',
            'class' => 'Supinfo\CommanderBundle\Entity\Zones',
            'query_builder' => function(EntityRepository $er){
                $queryBuilder = $er->createQueryBuilder('z');
                return $queryBuilder->orderBy('z.id', 'ASC');
            },
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


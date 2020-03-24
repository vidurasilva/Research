<?php

namespace AdminBundle\Form\Type;

use AppBundle\Entity\Milestone;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Created by PhpStorm.
 * User: ngeleedst
 * Date: 05/09/16
 * Time: 14:15
 */
class MilestoneType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', ['required' => true])
            ->add('number', 'text')
            ->add('recurrence', ChoiceType::class, array(
            	'choices' => Milestone::getRecurrences()
            ))
            ->add('duration', 'integer', array(
            	'label' => 'Number of weeks/months',
                'required' => true
            ))
//	        ->add('startDate', 'date', array(
//            	'label' => 'Number of weeks/months',
//                'required' => true
//            ))
//	        ->add('iterations', 'integer', array(
//		        'label' => 'Tasks per milestone',
//                'required' => true
//	        ))
            ->add('tasks', 'collection', array(
                'entry_type' => new TaskType(),
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => true,
                'required'       => true,
                'prototype_name' => '__title_task__',
                'options'        =>
                    [
                        'label' => 'Task',
                    ],
                'attr' => array(
                    'class' => 'child-collection',
                ),
                'label' => 'Tasks'
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Milestone',
            'allow_extra_fields' => true
        ));
    }

    public function getBlockPrefix()
    {
        return 'milestone';
    }
}
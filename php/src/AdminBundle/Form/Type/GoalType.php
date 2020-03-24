<?php

namespace AdminBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\DataTransformer\IntegerToLocalizedStringTransformer;

/**
 * Created by PhpStorm.
 * User: ngeleedst
 * Date: 05/09/16
 * Time: 14:15
 */
class GoalType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class)
			->add('duration', IntegerType::class, array(
				'attr' => array(
					'min' => 1,
				)
			))
			->add('durationUnit', ChoiceType::class, array(
				'choices' => array(
					'week' => 'Week',
					'month' => 'Month'
				)
			))
            ->add('file', FileType::class, array(
				'required'   => false,
				'data_class' => NULL,
				'mapped'     => false
			))
			->add('removeFile', CheckboxType::class, array(
				'required'		=> false,
				'data_class'	=> null,
				'mapped'		=> false
			))
//	        ->add('predefined')
            ->add('category', EntityType::class, [
                'required'      => true,
                'class'         => 'AppBundle\Entity\GoalCategory',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.title', 'ASC');
                },
                'choice_label'  => 'title',
            ])
            ->add('communityCategory', 'entity',
                [
                    'class'    => 'AppBundle\Entity\CommunityCategory',
                    'property' => 'title'
                ])
            ->add('milestones', 'collection', [
                'entry_type'     => new MilestoneType(),
                'allow_add'      => true,
                'allow_delete'   => true,
                'by_reference'   => false,
                'prototype'      => true,
                'required'       => true,
                'prototype_name' => '__title_milestone__',
                'options'        =>
                    [
                        'label' => 'Milestone',
                    ],
                'attr'           => [
                    'class' => 'parent-collection',
                ],
                'label'          => 'Milestones'
            ])
            ->add('description', 'textarea',
                [
                    'required' => false,
                    'attr'       => [
                        'class' => 'wysiwyg',
                    ],
                ])
	        ->add('additionalDescription', 'textarea',
		        [
			        'required' => false,
			        'attr'       => [
				        'class' => 'wysiwyg',
			        ],
		        ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => 'AppBundle\Entity\Goal',
            'allow_extra_fields' => true
        ]);
    }
}
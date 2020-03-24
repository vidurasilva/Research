<?php
/**
 * Created by PhpStorm.
 * User: Matthijs Overboom
 * Date: 5-1-17
 * Time: 9:40
 */

namespace AdminBundle\Form\Type;


use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\CommunityCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\DataTransformer\IntegerToLocalizedStringTransformer;

class RepetitiveGoalType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class)
            ->add('description', TextareaType::class, [
                'required'   => false,
//                'data_class' => NULL,
//                'mapped'     => false,
                'attr'       => [
                    'class' => 'wysiwyg',
                ],
            ])
            ->add('duration', IntegerType::class, [
                'rounding_mode' => IntegerToLocalizedStringTransformer::ROUND_DOWN,
                'attr'          => [
                    'min' => 1,
                ]
            ])
            ->add('durationUnit', ChoiceType::class, [
                'choices' => [
                    'week'  => 'Week',
                    'month' => 'Month'
                ]
            ])
            ->add('recurrence', ChoiceType::class, [
                'choices' => [
                    'day'   => 'Daily',
                    'week'  => 'Weekly',
                    'month' => 'Monthly'
                ]
            ])
            ->add('iteration', ChoiceType::class, [
                'choices' => [
                    '1'  => '1x',
                    '2'  => '2x',
                    '3'  => '3x',
                    '4'  => '4x',
                    '5'  => '5x',
                    '6'  => '6x',
                    '7'  => '7x',
                    '8'  => '8x',
                    '9'  => '9x',
                    '10' => '10x',
                    '11' => '11x',
                    '12' => '12x',
                    '13' => '13x',
                    '14' => '14x',
                    '15' => '15x',
                    '16' => '16x',
                    '17' => '17x',
                    '18' => '18x',
                    '19' => '19x',
                    '20' => '20x',
                    '21' => '21x',
                    '22' => '22x',
                    '23' => '23x',
                    '24' => '24x',
                    '25' => '25x',
                    '26' => '26x',
                    '27' => '27x',
                    '28' => '28x',
                ]
            ])
            ->add('file', FileType::class,
                [
                    'required'   => false,
                    'data_class' => NULL,
                    'mapped'     => false
                ])
            ->add('category', EntityType::class, [
                'required'      => true,
                'class'         => 'AppBundle\Entity\GoalCategory',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.title', 'ASC');
                },
                'choice_label'  => 'title',
            ])
            ->add('communityCategory', EntityType::class,
                [
                    'class'    => CommunityCategory::class,
                    'property' => 'title'
                ])
            ->add('taskTitle', TextType::class, [
                'required' => true,
                'mapped'   => false
            ])
            ->add('taskPoints', TextType::class, [
                'required' => true,
                'mapped'   => false
            ])
            ->add('taskVideo', TextType::class, [
                'required' => false,
                'mapped'   => false
            ])
            ->add('taskImage', FileType::class, [
                'required'   => false,
                'data_class' => NULL,
                'mapped'     => false
            ])
            ->add('taskDescription', TextareaType::class, [
                'required'   => false,
                'data_class' => NULL,
                'mapped'     => false,
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
            'data_class' => 'AppBundle\Entity\Goal'
        ]);
    }
}
<?php

namespace AdminBundle\Form\Type;

use AppBundle\Entity\UserGoal;
use AppBundle\Model\Status;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Created by PhpStorm.
 * User: ngeleedst
 * Date: 05/09/16
 * Time: 14:15
 */
class UserGoalType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startDate')
            ->add('endDate')
//            ->add('status')
            ->add('finished')
//            ->add('nickName', TextType::class)
//            ->add('email', EmailType::class, ['label' => 'Email'])
//           // ->add('password', 'password')
//            ->add('locked', CheckboxType::class,
//                [
//                    'required' => false
//                ]);
            ->add(
                'status',
                ChoiceType::class,
                array(
                    'label' => 'Status',
                    'choices' => Status::getStatus(),
                    'multiple' => false,
                    'expanded' => false
                ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => UserGoal::class
        ));
    }
}
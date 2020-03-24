<?php

namespace AppBundle\Form\Type;

use AdminBundle\Form\Type\MilestoneType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

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
            ->add('recurrence', TextType::class)
            ->add('iteration', TextType::class)
            ->add('checkinDescription', TextType::class)
            ->add('deadline', TextType::class);
    }

	/**
	 * @param OptionsResolver $resolver
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'csrf_protection'    => false,
			'allow_extra_fields' => true
		));
	}

	public function getName()
	{
		return 'customGoal';
	}

}
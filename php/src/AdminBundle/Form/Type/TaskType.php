<?php

namespace AdminBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Created by PhpStorm.
 * User: ngeleedst
 * Date: 05/09/16
 * Time: 14:15
 */
class TaskType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, ['required' => true])
            ->add('points', TextType::class, ['required' => false])
            ->add('video', TextType::class, ['required' => false])
            ->add('image', FileType::class, [
                'required'   => false,
                'data_class' => NULL,
            ])
			->add('removeImage', CheckboxType::class, array(
				'required' => false,
			))
//            ->add('imagePreview', 'text',
//                [
//                    'data_class' => null,
//                ])
            ->add('description', TextareaType::class, ['attr' => ['class' => 'wysiwyg'], 'required' => false])
            ->add('position', TextType::class);

        $formFactory = $builder->getFormFactory();
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($formFactory) {
            $form = $event->getForm();
            $data = $event->getData();


            if(!empty($data)) {

                $data->setImage($data->getImage());

                $event->setData($data);
            }

//            $form->add($formFactory->createNamed('image', $data->getImage(), array(
//                'label' => $data->getSettingsLabel(),
//            )));
        });
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Task',
        ]);
    }

    public function getName()
    {
        return 'task';
    }
}
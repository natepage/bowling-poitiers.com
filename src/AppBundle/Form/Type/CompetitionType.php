<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompetitionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, array(
                'label' => 'Nom de la compétition'
            ))
            ->add('bowling')
            ->add('partners', NumberType::class, array(
                'label' => 'Partenaires recherché(e)s'
            ))
            ->add('start', DateTimeType::class, array(
                'label' => 'Débute le',
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy HH:mm',
                'attr' => array(
                    'class' => 'date-time-picker'
                )
            ))
            ->add('end', DateTimeType::class, array(
                'label' => 'Finis le',
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy HH:mm',
                'attr' => array(
                    'class' => 'date-time-picker'
                )
            ))
            ->add('description', TextareaType::class, array(
                'attr' => array(
                    'class' => 'ckeditor'
                )
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Competition',
        ));
    }
}

<?php

namespace UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\ProfileFormType as BaseProfileFormType;

class ProfileFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('licence', TextType::class, array(
                'required' => false,
                'label' => 'N° de licence'
            ))
            ->add('newsletter', CheckboxType::class, array(
                'required' => false,
                'label' => 'J\'accepte de recevoir des mails de la part de l\'équipe du BCP pour m\'informer des nouveautés.'
            ))
            ->add('emailOnCompetitionCreated', CheckboxType::class, array(
                'required' => false,
                'label' => 'Recevoir un email quand une compétition est créée'
            ))
            ->add('emailOnCompetitionMessage', CheckboxType::class, array(
                'required' => false,
                'label' => 'Recevoir un email quand un message est ajouté aux compétitions propriétaires'
            ))
        ;
    }

    public function getParent()
    {
        return BaseProfileFormType::class;
    }

    public function getBlockPrefix()
    {
        return 'bcp_user_profile';
    }
}
<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class ReportFeedbackType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, array(
                'constraints' => array(
                    new Email()
                )
            ))
            ->add('content', TextareaType::class, array(
                'attr' => array(
                    'class' => 'ckeditor'
                ),
                'constraints' => array(
                    new NotBlank()
                )
            ))
        ;
    }
}
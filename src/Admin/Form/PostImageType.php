<?php
declare(strict_types=1);

namespace App\Admin\Form;

use App\Entity\PostImage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Vich\UploaderBundle\Form\Type\VichFileType;

final class PostImageType extends AbstractType
{
    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('underlyingFile', VichFileType::class, [
            'allow_delete' => false,
            'download_link' => false,
            'label' => false,
        ]);

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event): void {
            $postImage = $event->getData();
            $underlyingFile = $postImage?->getUnderlyingFile();

            if (null === $underlyingFile) {
                return;
            }

            if (\in_array($underlyingFile->getMimeType(), PostImage::SUPPORTED_MIME_TYPES, true) === false) {
                $event->getForm()->addError(new FormError($this->translator->trans(
                    'admin.form.errors.post_image.unsupported_mime_type',
                    [
                        'mimeTypes' => \implode(', ', PostImage::SUPPORTED_MIME_TYPES),
                    ]
                )));
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PostImage::class,
        ]);
    }
}

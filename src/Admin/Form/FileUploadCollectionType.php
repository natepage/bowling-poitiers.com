<?php
declare(strict_types=1);

namespace App\Admin\Form;

use EasyCorp\Bundle\EasyAdminBundle\Form\Type\FileUploadType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;

final class FileUploadCollectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('files', CollectionType::class, [
            'allow_add' => true,
            'entry_type' => FileUploadType::class,
            'entry_options' => [
                'upload_dir' => 'var/cache/images',
            ],
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'ea_fileupload_collection';
    }
}

<?php
declare(strict_types=1);

namespace App\Admin\Field\Configurator;

use App\Entity\Post;
use AsyncAws\S3\S3Client;
use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldConfiguratorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\FileUploadType;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class PostFilesUploadFieldsConfigurator implements FieldConfiguratorInterface
{
    public function configure(FieldDto $field, EntityDto $entityDto, AdminContext $context): void
    {
        return;

        $field->addJsAsset(Asset::fromEasyAdminAssetPackage('field-file-upload.js')->getAsDto());
        $field->addJsAsset(Asset::new('assets/js/file-upload-fix.js')->getAsDto());

        $field->setFormTypeOption('entry_type', FileUploadType::class);
        $field->setFormTypeOption('entry_options', $this->getFileUploadTypeOptions());
    }

    public function supports(FieldDto $field, EntityDto $entityDto): bool
    {
        return $field->getFieldFqcn() === CollectionField::class
            && \in_array($field->getProperty(), ['images', 'pdfs'], true)
            && $entityDto->getFqcn() === Post::class;
    }

    private function getFileUploadTypeOptions(): array
    {
        $s3 = new S3Client([
            'accessKeyId' => 'AKIA5KDI4VIJD7RNKFDZ',
            'accessKeySecret' => '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',
            'region' => 'ap-southeast-2',
        ]);

        $uploadNew = static function (UploadedFile $file, string $uploadDir, string $fileName) use ($s3) {
            $resource = \fopen($file->getRealPath(), 'r');

            $s3->putObject([
                'Bucket' => 'bowling-poitiers-uploads',
                'Key' => $fileName,
                'Body' => $resource,
            ]);
        };

        $uploadDelete = static function (File $file) {
            \dd($file);

            unlink($file->getPathname());
        };

        return [
            'upload_filename' => 'images/[timestamp]_[slug].[extension]',
            'upload_dir' => 'var/cache/images',
            'upload_new' => $uploadNew,
            'upload_delete' => $uploadDelete,
        ];
    }
}

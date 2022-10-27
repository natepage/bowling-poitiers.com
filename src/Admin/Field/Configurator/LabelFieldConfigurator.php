<?php
declare(strict_types=1);

namespace App\Admin\Field\Configurator;

use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldConfiguratorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;
use Symfony\Contracts\Translation\TranslatableInterface;

final class LabelFieldConfigurator implements FieldConfiguratorInterface
{
    public function configure(FieldDto $field, EntityDto $entityDto, AdminContext $context): void
    {
        $label = $field->getLabel();
        $property = $field->getProperty();

        if ($label === false || \strtolower((string)$label) !== \strtolower($property)) {
            return;
        }

        $entityName = \strtolower($entityDto->getName());
        $newLabel = \sprintf('admin.field.%s.%s', $entityName, $property);

        $field->setLabel($newLabel);
        $field->setFormTypeOption('label', $newLabel);
    }

    public function supports(FieldDto $field, EntityDto $entityDto): bool
    {
        return true;
    }
}

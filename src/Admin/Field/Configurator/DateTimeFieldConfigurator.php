<?php
declare(strict_types=1);

namespace App\Admin\Field\Configurator;

use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldConfiguratorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;

final class DateTimeFieldConfigurator implements FieldConfiguratorInterface
{
    public function configure(FieldDto $field, EntityDto $entityDto, AdminContext $context): void
    {
        $field->setCustomOption(DateTimeField::OPTION_TIMEZONE, 'Australia/Melbourne');
        $field->setFormTypeOptionIfNotSet('model_timezone', 'UTC');
        $field->setFormTypeOptionIfNotSet('view_timezone', 'Australia/Melbourne');
    }

    public function supports(FieldDto $field, EntityDto $entityDto): bool
    {
        return $field->getFieldFqcn() === DateTimeField::class;
    }
}

<?php
declare(strict_types=1);

namespace App\Admin\Field\Configurator;

use App\Admin\Field\StatusField;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldConfiguratorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;
use Symfony\Contracts\Translation\TranslatorInterface;

final class StatusFieldConfigurator implements FieldConfiguratorInterface
{
    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    public function configure(FieldDto $field, EntityDto $entityDto, AdminContext $context): void
    {
        $mapping = $field->getCustomOption(StatusField::OPTION_MAPPING);
        $formChoices = [];

        foreach ($mapping as $status => $config) {
            $config = \is_array($config) ? $config : [$config];
            $formChoices[$config[0]] = $status;

            if ($status === $field->getFormattedValue()) {
                $formattedValue = \sprintf(
                    '<span class="badge badge-%s">%s</span>',
                    $config[1] ?? null,
                    $this->translator->trans($config[0] ?? $status)
                );

                $field->setFormattedValue($formattedValue);
            }
        }

        $field->setFormTypeOption('choices', $formChoices);
    }

    public function supports(FieldDto $field, EntityDto $entityDto): bool
    {
        return $field->getFieldFqcn() === StatusField::class;
    }
}

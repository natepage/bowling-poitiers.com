<?php
declare(strict_types=1);

namespace App\Admin\Field;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Contracts\Translation\TranslatableInterface;

final class StatusField implements FieldInterface
{
    use FieldTrait;

    public const OPTION_MAPPING = 'mapping';

    /**
     * @param TranslatableInterface|string|false|null $label
     */
    public static function new(string $propertyName, ?string $label = null): self
    {
        return (new self())
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setTemplateName('crud/field/text')
            ->setFormType(ChoiceType::class)
            ->addCssClass('field-text')
            ->setDefaultColumns('col-md-6 col-xxl-5');
    }

    /**
     * @param array $mapping as ['status' => 'label' or ['label', 'cssClass']]
     */
    public function mapping(array $mapping): self
    {
        $this->setCustomOption(self::OPTION_MAPPING, $mapping);

        return $this;
    }
}

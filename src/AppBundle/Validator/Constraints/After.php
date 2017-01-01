<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class After extends Constraint
{
    public $message = 'Cette date ne doit pas être avant celle de début d\'évènement.';

    public function validatedBy()
    {
        return get_class($this).'Validator';
    }
}

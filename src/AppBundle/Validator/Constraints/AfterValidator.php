<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\InvalidArgumentException;

class AfterValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if(!$value instanceof \DateTime){
            throw new InvalidArgumentException(sprintf('Value must be a \DateTime object, %s given.', get_class($value)));
        }

        $start = $this->context->getRoot()->get('start')->getData();

        if($start > $value){
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}

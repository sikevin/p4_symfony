<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CheckDateValidator extends ConstraintValidator
{
	public function validate($value, Constraint $constraint)
	{
		// TODO: Implement validate() method.
		$dayFormat = $value->format('D');
		$dayAndMonthFormat = $value->format('d/n');

		//	Musée fermé le mardi
		if ($dayFormat === 'Tue')
		{
			$this->context->buildViolation('Le musée est fermé le mardi')
				->addViolation();
		}
		//	Musée fermé le dimanche
		else if ($dayFormat === "Sun")
		{
			$this->context->buildViolation('Le musée est fermé le dimanche')
				->addViolation();
		}
		//	Musée fermé le 1er Janvier
		if ($dayAndMonthFormat === '01/1')
		{
			$this->context->buildViolation('Le musée est fermé le 1er janvier')
				->addViolation();
		}
		//	Musée fermé le 1er mai
		else if ($dayAndMonthFormat === '01/5')
		{
			$this->context->buildViolation('Le musée est fermé le 1er mai')
				->addViolation();
		}
		//	Musée fermé le 8 mai
		else if ($dayAndMonthFormat === '08/5')
		{
			$this->context->buildViolation('Le musée est fermé le 8 mai')
				->addViolation();
		}
		//	Musée fermé le 14 Juillet
		else if ($dayAndMonthFormat == '14/7')
		{
			$this->context->buildViolation('Le musée est fermé le 14 Juillet')
				->addViolation();
		}
		//	Musée fermé le 15 Août
		else if ($dayAndMonthFormat === '15/8')
		{
			$this->context->buildViolation('Le musée est fermé le 15 août')
				->addViolation();
		}
		//	Musée fermé le 1er Novembre
		else if ($dayAndMonthFormat === '01/11')
		{
			$this->context->buildViolation('Le musée est fermé le 1er Novembre')
				->addViolation();
		}
		//	Musée fermé le 25 décembre
		else if ($dayAndMonthFormat === '25/12')
		{
			$this->context->buildViolation('Le musée est fermé le 25 Décembre')
				->addViolation();
		}
	}
}
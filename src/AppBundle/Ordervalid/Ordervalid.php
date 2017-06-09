<?php
/**
 * Created by PhpStorm.
 * User: KEVIN
 * Date: 03/06/2017
 * Time: 09:58
 */

namespace AppBundle\Ordervalid;


use Symfony\Component\Validator\Constraints\DateTime;

class Ordervalid
{
	public function ticketPrice($birthdates, $ticketType)
	{
		$age = $this->getAge($birthdates);

		foreach ($age as $indivAge)
		{
			if($indivAge >= 4 && $indivAge < 12) {

				if ($ticketType == false)
				{
					$price[] = 4;
				}
				else{
					$price[] = 8; //€
				}
			}
			else if($indivAge >= 60) {

				if ($ticketType == false)
				{
					$price[] = 6;
				}
				else{$price[] = 12; //€
				}
			}
			else if($indivAge >= 12) {

				if ($ticketType == false)
				{
					$price[] = 8;
				}else{$price[] = 16; //€
				}
			}
			else{
				$price[] = 0; //€‡
			}
		}


		return $price;
	}

	public function getAge($birthdates)
	{
		$datetime = new \DateTime('NOW');
		$year 		=	date_format($datetime, 'Y');
		foreach ($birthdates as $key => $value)
		{
			$birthdateY = date_format($value, 'Y');
			$age[] = $year - $birthdateY;
		}
		return $age;
	}

	public function reservationCode()
	{

	}

}
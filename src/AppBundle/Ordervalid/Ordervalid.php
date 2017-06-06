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
	public function ticketPrice($tariffs, $birthdates)
	{
		$age = $this->getAge($birthdates);
		dump($age);

		foreach ($age as $indivAge)
		{
			if($indivAge >= 4 && $indivAge < 12) {
				$price[] = 8; //€
			}
			else if($indivAge >= 60) {
				$price[] = 12; //€
			}
			else if($indivAge >= 12) {
				$price[] = 16; //€
			}
			else{
				$price[] = 0; //€
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
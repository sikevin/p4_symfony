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

		foreach ($tariffs as $key => $value)
		{
			if($tariffs[$key] == 1){

				$message[] = 10;
			}
			else if ($tariffs[$key] == 0)
			{
				unset($message);
				foreach ($age as $indivAge)
				{
					if($indivAge >= 4 && $indivAge <= 12) {
						$message[] = 8;
					}
					else if($indivAge >= 60) {
						$message[] = 12;
					}
					else if($indivAge > 12) {
						$message[] = 16;
					}
					else{
						$message[] = 0;
					}
				}
			}
		}

		return $message;
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
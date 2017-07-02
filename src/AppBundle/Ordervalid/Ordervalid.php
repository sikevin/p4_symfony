<?php
/**
 * Created by PhpStorm.
 * User: KEVIN
 * Date: 03/06/2017
 * Time: 09:58
 */

namespace AppBundle\Ordervalid;


class Ordervalid
{
	public function countVisitors($bookingList)
	{
		$nbVisitors = 0;
		foreach($bookingList as $visitors)
		{
			$nbVisitors =  $nbVisitors + $visitors->getVisitors();
		}

		return $nbVisitors;
	}
	public function ticketPrice($birthdates, $ticketType, $tariffs, $prices)
	{
		$age = $this->getAge($birthdates);

		foreach ($age as $indivAge)
		{
			if($indivAge >= 4 && $indivAge < 12) {

				if ($ticketType == false)
				{
					$price[] = $prices['child']/2;
				}
				else{
					$price[] = $prices['child']; //€
				}
			}
			else if($indivAge >= 60) {

				if ($ticketType == false)
				{
					$price[] = $prices['old']/2;
				}
				else{$price[] = $prices['old']; //€
				}
			}
			else if($indivAge >= 12) {

				if ($ticketType == false)
				{
					$price[] = $prices['adult']/2;
				} else{
					$price[] = $prices['adult']; //€
				}
			}
			else{
				$price[] = 0; //€
			}
		}

		//	Vérification du tarif réduit
		foreach ($tariffs as $key => $value)
		{
			//	Vérification tarif réduit
			if($tariffs[$key] == true && $ticketType == true)
			{
				$price[$key] = $prices['reduced'];
			}
			else if ($tariffs[$key] == true && $ticketType == false)
			{
				$price[$key] = $prices['reduced']/2;
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
		$characters    = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
		$reservationCode      = '';

		for($i=0;$i < 10;$i++)    //10 est le nombre de caractères
		{
			$reservationCode .= substr($characters,rand()%(strlen($characters)),2);
		}
		return $reservationCode;
	}

	public function total($ticketPrice)
	{
		//	Prix total de la commande
		$total = 0;
		foreach ($ticketPrice as $value)
		{
			$total += $value;
		}
		return $total;
	}
}
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
	//Prix du billet visiteur
	// entre 4 et 12 ans
	private $childPrice = 8;
	// entre 12 et 60 ans
	private $adultePrice = 16;
	// à partir de 60 ans
	private $oldPrice = 12;
	// Si le visiteur a coché tariff réduit
	private $reducedPrice = 10;

	public function ticketPrice($birthdates, $ticketType, $tariffs)
	{
		$age = $this->getAge($birthdates);

		foreach ($age as $indivAge)
		{
			if($indivAge >= 4 && $indivAge < 12) {

				if ($ticketType == false)
				{
					$price[] = $this->childPrice/2;
				}
				else{
					$price[] = $this->childPrice; //€
				}
			}
			else if($indivAge >= 60) {

				if ($ticketType == false)
				{
					$price[] = $this->oldPrice/2;
				}
				else{$price[] = $this->oldPrice; //€
				}
			}
			else if($indivAge >= 12) {

				if ($ticketType == false)
				{
					$price[] = $this->adultePrice/2;
				} else{
					$price[] = $this->adultePrice; //€
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
				$price[$key] = $this->reducedPrice;
			}
			else if ($tariffs[$key] == true && $ticketType == false)
			{
				$price[$key] = $this->reducedPrice/2;
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
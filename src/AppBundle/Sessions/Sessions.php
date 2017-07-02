<?php
/**
 * Created by PhpStorm.
 * User: KEVIN
 * Date: 30/06/2017
 * Time: 14:43
 */

namespace AppBundle\Sessions;

use AppBundle\Entity\Reservation;
use AppBundle\Entity\Visitor;

class Sessions
{
	//	Mettre en session le formulaire de réservation
	public function setInSessionFormReserv($request, $reservationEntity)
	{
		$session = $request->getSession();
		$session->set('reservation', $reservationEntity);

		return $session;
	}

	//	Mettre en session le formulaire des informations sur les visiteurs
	public function setInSessionFormVisitor($request, $visitor, $em)
	{
		$session = $request->getSession();

		foreach ($visitor as $key => $value)
		{
			$em->persist($visitor[$key]);
		}
		$session->set('visitors', $visitor);

		return $session;
	}

	//	Récupérer le formulaire de réservation mis en session
	public function getInSessionFormReserv($request)
	{
		$session = $request->getSession();
		$reservation		= $session->get('reservation');
		$email				= $reservation->getEmail();
		$reservationDate 	= $reservation->getReservationDate();
		$ticketType			= $reservation->getTicketType();
		$visitors			= $reservation->getVisitors();

		$form_reserv =
		[
			'email' 			=>	$email,
			'reservationDate' 	=>	$reservationDate,
			'ticketType' 		=>	$ticketType,
			'visitors' 			=>	$visitors
		];

		return $form_reserv;
	}

	//	Persiste les données dans les entités
	public function persistFormReserv($email, $reservationDate, $ticketType, $visitors, $reservationCode, $em)
	{
		$reservationEntity = new Reservation();
		$reservationEntity->setEmail($email);
		$reservationEntity->setReservationDate($reservationDate);
		$reservationEntity->setTicketType($ticketType);
		$reservationEntity->setVisitors($visitors);
		$reservationEntity->setReservationCode($reservationCode);
		$em->persist($reservationEntity);

		return $reservationEntity;
	}

	//	Persiste les données dans les entités
	public function persistFormVisitor($visitors, $visitorsData, $reservationEntity, $em)
	{
		for ($i=0; $i < $visitors; $i++) {
			$lastnames[] = $visitorsData[$i]->getLastname();
			$firstnames[] = $visitorsData[$i]->getFirstname();
			$countries[] = $visitorsData[$i]->getCountry();
			$birthdates[] = $visitorsData[$i]->getBirthdate();
			$tariffs[] = $visitorsData[$i]->getTariff();

			$visitorEntity = new Visitor();
			$visitorEntity->setReservation($reservationEntity);
			$visitorEntity->setLastname($lastnames[$i]);
			$visitorEntity->setFirstname($firstnames[$i]);
			$visitorEntity->setCountry($countries[$i]);
			$visitorEntity->setBirthdate($birthdates[$i]);
			$visitorEntity->setTariff($tariffs[$i]);
			$em->persist($visitorEntity);
		}
	}

	public function getReservationData($request)
	{
		$session = $request->getSession();
		$visitorsData		= $session->get('visitors');
		$reservation		= $session->get('reservation');

		$ticketType			= $reservation->getTicketType();
		$visitors			= $reservation->getVisitors();

		for ($i=0; $i < $visitors; $i++)
		{
			$lastnames[]	= $visitorsData[$i]->getLastname();
			$firstnames[]	= $visitorsData[$i]->getFirstname();
			$countries[]	= $visitorsData[$i]->getCountry();
			$birthdates[]	= $visitorsData[$i]->getBirthdate();
			$tariffs[]		= $visitorsData[$i]->getTariff();
		}

		$reservationData = [
			'visitorsData'	=>	$visitorsData,
			'reservation'	=>	$reservation,
			'ticketType'	=>	$ticketType,
			'visitors'		=>	$visitors,
			'lastnames'		=>	$lastnames,
			'firstnames'	=>	$firstnames,
			'countries'		=>	$countries,
			'birthdates'	=>	$birthdates,
			'tariffs'		=>	$tariffs,
		];
		return $reservationData;
	}

	public function setInSessionPrice($request, $ticketPrice, $total)
	{
		$session = $request->getSession();
		// Garde en session le prix des billets
		$session->set('ticketPrice', $ticketPrice);
		// Mis en session pour le récupérer dans le controller checkoutAction
		$session->set('total', $total);
	}

	//	Détruire les sessions
	public function destroySessions($request)
	{
		$session = $request->getSession();
		$session->set('visitors', '');
		$session->set('reservation', '');
		if($session->get('reservationCode') != '')
		{
			$session->set('reservationCode', '');
		}
		if($session->get('ticketPrice') != '')
		{
			$session->set('ticketPrice', '');
		}
	}
}
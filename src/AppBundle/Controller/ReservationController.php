<?php
/**
 * Created by PhpStorm.
 * User: KEVIN
 * Date: 20/05/2017
 * Time: 11:36
 */

namespace AppBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Reservation;

use AppBundle\Form\ReservationType;


class ReservationController extends Controller
{
	/**
	 * @Route("/")
	 */
	public function showAction()
	{
		$reservationId = 1;
		$reservation1 = $this->getDoctrine()
			->getRepository('AppBundle:Reservation')
			->find($reservationId);

		if (!$reservation1)
		{
			throw $this->createNotFoundException(
				'No product found for id '. $reservationId);
		}


		$reservation = new Reservation();
		$form = $this->createForm(ReservationType::class, $reservation);

		return $this->render('reservation/form1.html.twig',[
			'reservation' => $reservation1,
			'form' 		  => $form
		]);
	}

	/**
	 * @Route("/add/")
	 */
	public function createAction()
	{
		// TEST réussi: add a reservation to DB
		$reservation = new Reservation();
		$reservation->setLastname("SI");
		$reservation->setFirstname("Kevin");
		$reservation->setBirthdate(new \DateTime("1995-06-20"));
		$reservation->setCountry("France");
		$reservation->setReservationDate(new \DateTime('NOW'));
		$reservation->setTariff(false);
		$reservation->setTicketType(true);

		$em = $this->getDoctrine()->getManager();

		$em->persist($reservation);

		$em->flush();

		return new Response('Saved new reservation with id' . $reservation->getId());
	}
}
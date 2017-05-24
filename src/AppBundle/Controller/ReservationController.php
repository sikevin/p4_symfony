<?php
/**
 * Created by PhpStorm.
 * User: KEVIN
 * Date: 20/05/2017
 * Time: 11:36
 */

namespace AppBundle\Controller;


use AppBundle\Form\VisitorType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Reservation;
use AppBundle\Entity\Visitor;
use AppBundle\Form\ReservationType;



class ReservationController extends Controller
{
	/**
	 * @Route("/")
	 */
	public function formReservationAction()
	{
		$em = $this->getDoctrine()->getManager();

		$reservationId = 2;
		$reservation = $em
			->getRepository('AppBundle:Reservation')
			->find($reservationId)
		;

		if (!$reservation)
		{
			throw $this->createNotFoundException(
				'No product found for id '. $reservationId);
		}

		$listVisitors = $em
			->getRepository('AppBundle:Visitor')
			->findBy(array('reservation' => $reservation))
		;

		// Formulaire réservation
		$reservationEntity = new Reservation();
		$reservationForm = $this
			->get('form.factory')
			->create(ReservationType::class, $reservationEntity)
		;


		return $this->render('reservation/form1.html.twig',[
			'reservation'		=> $reservation,
			'listVisitors'		=> $listVisitors,
			'reservationForm'	=> $reservationForm->createView(),
		]);
	}

	/**
	 * @Route("/visitors/")
	 */
	public function formVisitorAction()
	{
		$visitorEntity = new Visitor();
		$visitorForm = $this
			->get('form.factory')
			->create(VisitorType::class, $visitorEntity)
		;

		return $this->render('reservation/form2.html.twig',[
			'visitorForm'	=> $visitorForm->createView(),
		]);
	}

	/**
	 * @Route("/add/")
	 */
	public function createAction()
	{
		// TEST réussi: add a reservation to DB
		$reservation = new Reservation();
		$reservation->setReservationDate(new \DateTime('NOW'));
		$reservation->setTicketType(true);
		$reservation->setReservationCode("3kEp495Cl");

		$visitor1 = new Visitor();
		$visitor1->setLastname("SI");
		$visitor1->setFirstname("Kevin");
		$visitor1->setCountry("France");
		$visitor1->setBirthdate(new \DateTime('1995-06-20'));
		$visitor1->setTariff(false);

		$visitor2 = new Visitor();
		$visitor2->setLastname("SI");
		$visitor2->setFirstname("Denis");
		$visitor2->setCountry("France");
		$visitor2->setBirthdate(new \DateTime('1997-04-18'));
		$visitor2->setTariff(false);

		$visitor3 = new Visitor();
		$visitor3->setLastname("SI");
		$visitor3->setFirstname("Thierry");
		$visitor3->setCountry("France");
		$visitor3->setBirthdate(new \DateTime('2002-01-11'));
		$visitor3->setTariff(false);

		$visitor1->setReservation($reservation);
		$visitor2->setReservation($reservation);
		$visitor3->setReservation($reservation);

		$em = $this->getDoctrine()->getManager();

		$em->persist($visitor1);
		$em->persist($visitor2);
		$em->persist($visitor3);

		$em->flush();

		return new Response('Saved new reservation with id' . $reservation->getId());
	}
}
<?php
/**
 * Created by PhpStorm.
 * User: KEVIN
 * Date: 20/05/2017
 * Time: 11:36
 */

namespace AppBundle\Controller;

use Doctrine\DBAL\Types\DateType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

use AppBundle\Entity\Reservation;
use AppBundle\Entity\Visitor;
use AppBundle\Ordervalid\Ordervalid;

use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use AppBundle\Form\VisitorType;
use AppBundle\Form\ReservationType;


class ReservationController extends Controller
{
	/**
	 * @Route("/", name="form_reserv")
	 */
	public function formReservationAction(Request $request)
	{
		// Formulaire réservation
		$reservationEntity = new Reservation();
		$reservationForm = $this
			->get('form.factory')
			->create(ReservationType::class, $reservationEntity)
		;

		// If form submit
		if ($request->isMethod('POST') && $reservationForm->handleRequest($request)->isValid())
		{
			$em = $this->getDoctrine()->getManager();

			$em->persist($reservationEntity);

			$session = $request->getSession();
			$session->set('reservation', $reservationEntity);

			$repository = $em->getRepository('AppBundle:Reservation');
			$bookingList = $repository->findByReservationDate($session->get('reservation')->getReservationDate());

			$nbVisitors = 0;
			foreach($bookingList as $visitors)
			{
				$nbVisitors =  $nbVisitors + $visitors->getVisitors();
			}

			if($nbVisitors >= 1000)
			{
				// add flash messages
				$this->get('session')->getFlashBag()->add('error', 'Le nombre maximal de billet vendu pour ce jour a été atteint. Veuillez sélectionner une autre date.');
				return $this->redirectToRoute('form_reserv');
			}
			else
			{
				return $this->redirectToRoute('form_visitor');
			}
		}

		return $this->render('reservation/reservationForm.html.twig',[
			'reservationForm'	=> $reservationForm->createView(),
		]);
	}

	/**
	 * @Route("/visitors/", name="form_visitor")
	 */
	public function formVisitorAction(Request $request)
	{
		$session = $request->getSession();
		$reservation		= $session->get('reservation');
		$email				= $reservation->getEmail();
		$reservationDate 	= $reservation->getReservationDate();
		$ticketType			= $reservation->getTicketType();
		$visitors			= $reservation->getVisitors();

		for ($i = 1; $i <= $visitors; $i++)
		{
			$visitor[] = new Visitor();
		}

		$visitorForm = $this
			->get('form.factory')
			->create(CollectionType::class, $visitor,
				['entry_type' => VisitorType::class])
			->add('submit',		SubmitType::class, array(
				'label'		=> 'Suivant',
				'attr' 		=> array('class' => 'save')));

		if($email == null)
		{
			return $this->redirectToRoute('form_reserv');
		}

		if ($request->isMethod('POST') && $visitorForm->handleRequest($request)->isValid())
		{
			$em = $this->getDoctrine()->getManager();
			$session = $request->getSession();
			foreach ($visitor as $key => $value)
			{
				$em->persist($visitor[$key]);
			}
			$session->set('visitors', $visitor);

			$this->addFlash('notice', 'visiteurs ajouté');

			return $this->redirectToRoute('summary');
		}

		return $this->render('reservation/visitorForm.html.twig',[
			'visitorForm'		=> $visitorForm->createView(),

			'email'				=> $email,
			'reservationDate'	=> $reservationDate,
			'ticketType'		=> $ticketType,
			'visitors'			=> $visitors,
		]);
	}

	/**
	 * @Route("/summary/", name="summary")
	 */
	public function summaryAction(Request $request)
	{
		$session = $request->getSession();
		$visitorsData		= $session->get('visitors');
		$reservation		= $session->get('reservation');

		$ticketType			= $reservation->getTicketType();
		$visitors			= $reservation->getVisitors();

		for ($i=0; $i < $visitors; $i++)
		{
			$lastnames[]	= $visitorsData[$i]->getLastname();
			$firstnames[] 	= $visitorsData[$i]->getFirstname();
			$countries[]	= $visitorsData[$i]->getCountry();
			$birthdates[]	= $visitorsData[$i]->getBirthdate();
			$tariffs[]		= $visitorsData[$i]->getTariff();
		}

		$ordervalid = new Ordervalid();
		$ticketPrice = $ordervalid->ticketPrice( $birthdates, $ticketType);

		foreach ($tariffs as $key => $value)
		{
			//	Vérification tarif réduit
			if($tariffs[$key] == true && $ticketType == true){
				$ticketPrice[$key] = 10;
			}
			else if ($tariffs[$key] == true && $ticketType == false)
			{
				$ticketPrice[$key] = 5;
			}
		}

		//	Prix total de la commande
		$total = 0;
		foreach ($ticketPrice as $value)
		{
			$total += $value;
		}

		// Mis en session pour le récupérer dans le controller checkoutAction
		$session->set('total', $total);

		return $this->render('reservation/summary.html.twig', [
			'lastnames'		=>	$lastnames,
			'firstnames'	=>	$firstnames,
			'countries'		=>	$countries,
			'birthdates'	=>	$birthdates,
			'tariffs'		=>	$tariffs,
			'visitors'		=>	$visitors,
			'ticketPrice'	=>	$ticketPrice,
			'ticketType'	=>	$ticketType,
			'total'			=>	$total,
		]);
	}

	/**
	 * @Route(
	 *     "/checkout",
	 *     name="order_checkout",
	 *     methods="POST"
	 * )
	 */
	public function checkoutAction(Request $request)
	{
		$session = $request->getSession();
		$total = $session->get('total');

		// Set your secret key: remember to change this to your live secret key in production
		// See your keys here: https://dashboard.stripe.com/account/apikeys
		\Stripe\Stripe::setApiKey("sk_test_WcieSviH58jVYnWyqBr4CCCY");

		// Token is created using Stripe.js or Checkout!
		//// Get the payment token submitted by the form:
		$token = $_POST['stripeToken'];
		try {
			// Charge the user's card:
			$charge = \Stripe\Charge::create(array(
				"amount" => $total * 100,
				"currency" => "eur",
				"description" => "Réservation de billets pour le musée du Louvre",
				"source" => $token,
			));
			$this->addFlash("success","Réservation confirmé !");
			return $this->redirectToRoute("booking_validated");
		}
		catch (\Stripe\Error\Card $e)
		{
			$this->addFlash("error","Un erreur est survenu lors du paiement, veuillez retenter.");
			return $this->redirectToRoute("summary");
			// The card has been declined
		}
	}

	/**
	 * @Route(
	 *     "/booking-validated",
	 *     name="booking_validated"
	 * )
	 */
	public function bookingValAction(Request $request)
	{
		$session = $request->getSession();
		$email = $session->get('reservation')->getEmail();

		return $this->render('reservation/validated.html.twig', [
			'email' => $email
		]);
	}
}
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
		//	Si la session reservation n'existe pas
		if($request->getSession()->get('reservation') == null)
		{
			return $this->redirectToRoute('form_reserv');
		}

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
		//	Si les sessions n'existe pas
		if($request->getSession()->get('reservation') == null || $request->getSession()->get('visitors') == null )
		{
			return $this->redirectToRoute('form_reserv');
		}

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
		// Garde en session le prix des billets
		$session->set('ticketPrice', $ticketPrice);

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

			//	Insérer la réservation dans la bdd
			$em = $this->getDoctrine()->getManager();
			$ordervalid = new Ordervalid();
			$reservationCode = $ordervalid->reservationCode();

			//	Appelle des données en session
			$session = $request->getSession();
			//Enregistre le code de réservation dans une session
			$session->set('reservationCode', $reservationCode);
			//	Formulaire information du visiteur
			$visitorsData		= $session->get('visitors');
			//	Formulaire information sur la réservation
			$reservation		= $session->get('reservation');

			$email				= $reservation->getEmail();
			$reservationDate 	= $reservation->getReservationDate();
			$ticketType			= $reservation->getTicketType();
			$visitors			= $reservation->getVisitors();

			// Met dans l'entité Réservation les données du formulaire de réservation
			$reservationEntity = new Reservation();
			$reservationEntity->setEmail($email);
			$reservationEntity->setReservationDate($reservationDate);
			$reservationEntity->setTicketType($ticketType);
			$reservationEntity->setVisitors($visitors);
			$reservationEntity->setReservationCode($reservationCode);
			$em->persist($reservationEntity);

			for ($i=0; $i < $visitors; $i++)
			{
				$lastnames[]	= $visitorsData[$i]->getLastname();
				$firstnames[] 	= $visitorsData[$i]->getFirstname();
				$countries[]	= $visitorsData[$i]->getCountry();
				$birthdates[]	= $visitorsData[$i]->getBirthdate();
				$tariffs[]		= $visitorsData[$i]->getTariff();

				// Met dans l'entitié visitor les données du formulaire visiteur
				$visitorEntity = new Visitor();
				$visitorEntity->setReservation($reservationEntity);
				$visitorEntity->setLastname($lastnames[$i]);
				$visitorEntity->setFirstname($firstnames[$i]);
				$visitorEntity->setCountry($countries[$i]);
				$visitorEntity->setBirthdate($birthdates[$i]);
				$visitorEntity->setTariff($tariffs[$i]);
				$em->persist($visitorEntity);
			}

			$em->flush();

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
		if($request->getSession()->get('reservation') == null || $request->getSession()->get('visitors') == null )
		{
			return $this->redirectToRoute('form_reserv');
		}

		$session = $request->getSession();
		$visitorsData		= $session->get('visitors');
		$reservation 		= $session->get('reservation');
		$reservationCode 	= $session->get('reservationCode');
		$ticketPrice		= $session->get('ticketPrice');

		$email				= $reservation->getEmail();
		$reservationDate 	= $reservation->getReservationDate()->format('d-m-Y');
		$ticketType			= $reservation->getTicketType();
		$visitors			= $reservation->getVisitors();

		$mailBodyHTML = $this->render('reservation/mailView.html.twig', [
			'reservationDate' 	=>	$reservationDate,
			'visitorData'		=>	$visitorsData,
			'visitors'			=>	$visitors,
			'ticketType'		=>	$ticketType,
			'reservationCode'	=>	$reservationCode,
			'ticketPrice'		=>	$ticketPrice,
			])->getContent();

		//	Envoie d'email
		$message = \Swift_Message::newInstance();
		$message->setSubject("Votre réservation pour le musée du Louvre");
		$message->setFrom('confirmation@museedulouvre.com');
		$message->setTo($email);
		// pour envoyer le message en HTML
		$message->setBody(
			$mailBodyHTML,
			'text/html');
		//envoi du message
		$this->get('mailer')->send($message);

		$session->set('visitors', '');
		$session->set('reservation', '');
		$session->set('reservationCode', '');
		$session->set('ticketPrice', '');

		return $this->render('reservation/validated.html.twig', [
			'email' => $email,
		]);
	}
}
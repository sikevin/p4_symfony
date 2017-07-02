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

use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\Reservation;
use AppBundle\Entity\Visitor;
use AppBundle\Ordervalid\Ordervalid;
use AppBundle\Sessions\Sessions;

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
			//	Garde en session le formulaire de réservation
			$session = new Sessions();
			$session = $session->setInSessionFormReserv($request, $reservationEntity);

			$repository = $em->getRepository('AppBundle:Reservation');
			$bookingList = $repository->findByReservationDate($session->get('reservation')->getReservationDate());

			//	Vérification du nombre de billet max vendu ce jour là
			$orderValid = new Ordervalid();
			$nbVisitors = $orderValid->countVisitors($bookingList);

			if($nbVisitors >= $this->getParameter('max_visitors'))
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
		if ($request->getSession()->get('reservation') == null)
		{
			return $this->redirectToRoute('form_reserv');
		}

		// Récupérer la session du formulaire de réservation
		$session = new Sessions();
		$reservation = $session->getInSessionFormReserv($request);

		for ($i = 1; $i <= $reservation['visitors']; $i++)
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

		if ($request->isMethod('POST') && $visitorForm->handleRequest($request)->isValid())
		{
			$em = $this->getDoctrine()->getManager();

			 $session->setInSessionFormVisitor($request, $visitor, $em);

			$this->addFlash('notice', 'visiteurs ajouté');

			return $this->redirectToRoute('summary');
		}

		return $this->render('reservation/visitorForm.html.twig',[
			'visitorForm'		=> $visitorForm->createView(),

			'email'				=> $reservation['email'],
			'reservationDate'	=> $reservation['reservationDate'],
			'ticketType'		=> $reservation['ticketType'],
			'visitors'			=> $reservation['visitors'],
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

		$session = new Sessions();
		//appel des données du formulaire info des visiteurs et du formulaire de reservation
		$reservationData = $session->getReservationData($request);

		$tariffs	=	$reservationData['tariffs'];
		$ticketType =	$reservationData['tariffs'];
		$birthdates = 	$reservationData['birthdates'];

		$ordervalid = new Ordervalid();
		//	Prix par billet
		$prices = $this->getParameter('price');
		$ticketPrice = $ordervalid->ticketPrice( $birthdates, $ticketType, $tariffs, $prices);
		//	Total de tous les billets
		$total = $ordervalid->total($ticketPrice);

		//Garde en session le prix par billet et le total
		$session->setInSessionPrice($request, $ticketPrice, $total);

		return $this->render('reservation/summary.html.twig', [
			'lastnames'		=>	$reservationData['lastnames'],
			'firstnames'	=>	$reservationData['firstnames'],
			'countries'		=>	$reservationData['countries'],
			'birthdates'	=>	$birthdates,
			'tariffs'		=>	$tariffs,
			'visitors'		=>	$reservationData['visitors'],
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
		// Set your secret key: remember to change this to your live secret key in production
		// See your keys here: https://dashboard.stripe.com/account/apikeys
		\Stripe\Stripe::setApiKey("sk_test_WcieSviH58jVYnWyqBr4CCCY");

		// Token is created using Stripe.js or Checkout!
		//// Get the payment token submitted by the form:
		$token = $request->request->get('stripeToken');
		try {
			$session = $request->getSession();
			$total = $session->get('total');
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

			//Enregistre le code de réservation dans une session
			$session->set('reservationCode', $reservationCode);

			$sessions = new Sessions();
			$reservationData = $sessions->getReservationData($request);

			// Met dans l'entité Réservation les données du formulaire de réservation & persist
			$reservationEntity =
				$sessions->persistFormReserv($reservationData['reservation']->getEmail(), $reservationData['reservation']->getReservationDate(), $reservationData['reservation']->getTicketType(), $reservationData['reservation']->getVisitors(), $reservationCode, $em);

			// Met dans l'entitié Visitor les données du formulaire visiteur & persist
			$sessions->persistFormVisitor( $reservationData['reservation']->getVisitors(), $reservationData['visitorsData'], $reservationEntity, $em);

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
		$sessions = new Sessions();
		$reservationData	= $sessions->getReservationData($request);
		$reservationCode 	= $session->get('reservationCode');
		$ticketPrice		= $session->get('ticketPrice');

		$mailBodyHTML = $this->render('reservation/mailView.html.twig', [
			'reservationDate' 	=>	$reservationData['reservation']->getReservationDate()->format('d-m-Y'),
			'visitorData'		=>	$reservationData['visitorsData'],
			'visitors'			=>	$reservationData['reservation']->getVisitors(),
			'ticketType'		=>	$reservationData['reservation']->getTicketType(),
			'reservationCode'	=>	$reservationCode,
			'ticketPrice'		=>	$ticketPrice,
			])->getContent();

		//	Envoie d'email
		$message = \Swift_Message::newInstance();
		$message->setSubject("Votre réservation pour le musée du Louvre");
		$message->setFrom('confirmation@museedulouvre.com');
		$message->setTo($reservationData['reservation']->getEmail());
		// pour envoyer le message en HTML
		$message->setBody(
			$mailBodyHTML,
			'text/html');
		//envoi du message
		$this->get('mailer')->send($message);

		$sessions->destroySessions($request);

		return $this->render('reservation/validated.html.twig', [
			'email' => $reservationData['reservation']->getEmail(),
		]);
	}

	/**
	 * @Route("/cancel", name="cancel")
	 */
	public function cancelAction(Request $request)
	{
		$session = new Sessions();
		$session->destroySessions($request);

		return $this->redirectToRoute('form_reserv');
	}
}
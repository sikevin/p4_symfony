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

class ReservationController extends Controller
{
	/**
	 * @Route("/")
	 */
	public function showAction()
	{
		return $this->render('reservation/form1.html.twig');
	}
}
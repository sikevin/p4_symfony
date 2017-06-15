<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\Entity\Reservation;
use AppBundle\Entity\Visitor;
use Symfony\Component\Validator\Constraints\DateTime;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

	public function testReservation()
	{
		$reservation = new Reservation();
		$reservation->setEmail('sikevin.sk@gmail.com');
		$reservation->setReservationDate(new \DateTime('2017/07/22'));
		$reservation->setTicketType(1);
		$reservation->setReservationCode('chdgh545dFD');
		$reservation->setVisitors(2);

		$i = 0;
		while ($i < 3){

			$ticket = new Visitor();
			$ticket->setReservation($reservation);
			$ticket->setLastname('SI');
			$ticket->setFirstname('Kevin');
			$ticket->setBirthdate(new \DateTime('1995/06/20'));
			$ticket->setCountry('France');
			$ticket->setTariff(FALSE);
			$i++;
		}

		$this->assertContainsOnly('datetime', [$reservation->getReservationDate()]);
		$this->assertContainsOnly('int', [$reservation->getTicketType(), $reservation->getVisitors()]);
		$this->assertContainsOnly('string', [$reservation->getEmail(), $reservation->getReservationCode()]);
	}

	public function testReservationCode()
	{
		$characters    = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
		$reservationCode      = '';

		for($i=0;$i < 10;$i++)    //10 est le nombre de caractères
		{
			$reservationCode .= substr($characters,rand()%(strlen($characters)),2);
		}
		$this->assertContainsOnly('string', [$reservationCode]);
	}

	public function testGetAge()
	{
		$birthdate = new \DateTime('1995-06-20');
		$datetime = new \DateTime('NOW');
		$year 		=	date_format($datetime, 'Y');
		$birthdateY = date_format($birthdate, 'Y');
		$age = $year - $birthdateY;

		$this->assertContainsOnly('int', [$age]);
		return $age;
	}


}

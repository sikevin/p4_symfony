<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="reservation")
 */
class Reservation
{
    /**
     * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

	/**
	 * @ORM\Column(type="date")
	 * @Assert\DateTime()
	 */
    private $reservationDate;

	/**
	 * @ORM\Column(type="boolean")
	 */
    private $ticketType;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
    private $reservationCode;

	/**
	 * @ORM\Column(type="string", length=255)
	 * @Assert\Email(
	 *     message = "L'adresse email '{{ value }}' n'est pas valide.",
	 *     checkMX = true
	 *   )
	 */
    private $email;

	/**
	 * @ORM\Column(type="integer")
	 * @Assert\Range(
	 *     min = 1,
	 *     max = 20,
	 *     minMessage = "Vous devez prendre au moins 1 billet pour commander",
	 *     maxMessage = "Vous de pouvez pas prendre plus de 20 billets"
	 * )
	 */
	private $visitors;

    public function __construct()
	{
		$this->reservationDate = new \DateTime();
	}

	/**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set reservationDate
     *
     * @param \DateTime $reservationDate
     *
     * @return Reservation
     */
    public function setReservationDate($reservationDate)
    {
        $this->reservationDate = $reservationDate;

        return $this;
    }

    /**
     * Get reservationDate
     *
     * @return \DateTime
     */
    public function getReservationDate()
    {
        return $this->reservationDate;
    }

    /**
     * Set ticketType
     *
     * @param boolean $ticketType
     *
     * @return Reservation
     */
    public function setTicketType($ticketType)
    {
        $this->ticketType = $ticketType;

        return $this;
    }

    /**
     * Get ticketType
     *
     * @return boolean
     */
    public function getTicketType()
    {
        return $this->ticketType;
    }

    /**
     * Set reservationCode
     *
     * @param string $reservationCode
     *
     * @return Reservation
     */
    public function setReservationCode($reservationCode)
    {
        $this->reservationCode = $reservationCode;

        return $this;
    }

    /**
     * Get reservationCode
     *
     * @return string
     */
    public function getReservationCode()
    {
        return $this->reservationCode;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Reservation
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set visitors
     *
     * @param integer $visitors
     *
     * @return Reservation
     */
    public function setVisitors($visitors)
    {
        $this->visitors = $visitors;

        return $this;
    }

    /**
     * Get visitors
     *
     * @return integer
     */
    public function getVisitors()
    {
        return $this->visitors;
    }
}

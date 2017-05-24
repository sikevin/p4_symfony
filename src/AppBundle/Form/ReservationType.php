<?php

namespace AppBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class ReservationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
			->add('email', EmailType::class)
			->add('visitors', NumberType::class)
			->add('reservationDate',	DateType::class, [
				'format' => 'dd-MM-yyyy',
				'years'	 => range(2017, 2037)
			])
			->add('ticketType',		ChoiceType::class, array(
				"choices" => array(
					'Journée' 		=> 'true',
					'Demi-journée'	=> 'false'
				))
			)
        	->add('next',			SubmitType::class, array(
				'attr' => array('class' => 'save'),
			));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Reservation'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_reservation';
    }


}

<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Range;

use AppBundle\Validator\Constraints;

class ReservationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
			->add('email', EmailType::class, array(
				'label'			=> 'Adresse Email',
				'constraints' 	=> new Length(
					array(
						'min' 			=> 5,
						'max' 			=> 255,
						'minMessage' 	=> 'Minimum de 5 caractères',
						'maxMessage' 	=> 'Maximum de 255 caractères'
					))
				)
			)
			->add('reservationDate',	DateType::class, array(
				'label'			=> 'Date de réservation',
				'format'	 	=> 'dd-MM-yyyy',
				'widget' 		=> 'single_text',
				'html5' 		=> false,
				'attr'			=> ['class' => 'datepicker'],
				'constraints'	=> [
					new GreaterThanOrEqual(
						array(
							"value" => "today",
							"message" => "La date de visite doit être au moins celle d'aujourd'hui."
						)
					),
					new Constraints\CheckDate(),
				]
				)
			)
			->add('ticketType',		ChoiceType::class, array(
				'label'			=> 'Type de billet',
				"choices" => array(
					'Journée' 		=> true,
					'Demi-journée'	=> false
				),
				'choice_attr' => function($val, $key, $index) {
						// adds a class like attending_yes, attending_no, etc
						return ['class' => strtolower($key)];
				},
				"constraints" => [
					new Type (
						array(
							"type" => 'bool',
							"message" => "La valeur doit être un boolean"
						)
					)
				]
				)
			)
			->add('visitors', ChoiceType::class, array(
				'label'			=> 'Nombre de visiteurs',
				"choices" 		=> range(0, 20),
				"constraints" 	=>
					new Range(
						array(
							'min' 				=> '1',
							'minMessage' 		=> "Il doit y avoir au moins 1 visiteur pour réserver.",
							'invalidMessage'	=> "Vous devez entrer un nombre."
						)
					),
				)
			)
			->add('submit', SubmitType::class, array(
				'label'	=> 'Suivant'
			))
        	;
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

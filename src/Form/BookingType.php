<?php

namespace App\Form;

use App\Entity\Booking;
use App\Entity\Ticket;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('bookingEmail', null, [
                'required' => false,
                'attr' => ['placeholder' => 'Introdu email*'],])
            ->add('fullName')
            ->add('paidAmount')
            ->add('ticket', EntityType::class, [
                'class' => ticket::class,
                'choice_label' => 'type',
            ])
            ->add('user', EntityType::class, [
                'class' => user::class,
                'choice_label' => 'email',
                'placeholder' => 'Selecteaza un utilizator*',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}

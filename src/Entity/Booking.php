<?php

namespace App\Entity;

use App\Repository\BookingRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints;


#[ORM\Entity(repositoryClass: BookingRepository::class)]
class Booking
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Constraints\Email(
        message: 'Emailul {{ value }} nu este valid!',
    )]
    private ?string $bookingEmail = null;

    #[ORM\Column(length: 255)]
    #[Constraints\NotBlank(message: 'Introdu un nume!')]
    #[Constraints\Length(
        min: 5,
        max: 50,
        maxMessage: 'Numele are lungimea de maxim {{ limit }} caractere',
        minMessage: 'Numele are lungimea de minim {{ limit }} caractere',
    )]
    private ?string $fullName = null;

    #[ORM\Column]
    #[Assert\NotNull(message: 'Suma platita in format invalid')]
    #[Assert\Positive(message: 'Suma platita trebuie sa fie pozitiva')]
    private ?float $paidAmount = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?ticket $ticket = null;

    #[ORM\ManyToOne(inversedBy: 'bookings')]
    #[ORM\JoinColumn(nullable: true)]
    private ?user $user = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBookingEmail(): ?string
    {
        return $this->bookingEmail;
    }

    public function setBookingEmail(string $bookingEmail): static
    {
        $this->bookingEmail = $bookingEmail;

        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): static
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getPaidAmount(): ?float
    {
        return $this->paidAmount;
    }

    public function setPaidAmount(float $paidAmount): static
    {
        $this->paidAmount = $paidAmount;

        return $this;
    }

    public function getTicket(): ?ticket
    {
        return $this->ticket;
    }

    public function setTicket(?ticket $ticket): static
    {
        $this->ticket = $ticket;

        return $this;
    }

    public function getUser(): ?user
    {
        return $this->user;
    }

    public function setUser(?user $user): static
    {
        $this->user = $user;

        return $this;
    }
}

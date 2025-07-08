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

    #[ORM\ManyToOne(inversedBy: 'bookings')]
    #[ORM\JoinColumn(nullable: false)]
    #[Constraints\NotNull(message: 'Festival invalid!')]
    private ?festival $festival = null;

    #[ORM\Column(length: 255)]
    #[Constraints\NotBlank(message: 'Introdu o adresa de mail!')]
    #[Constraints\Email(
        message: 'Emailul {{ value }} nu este valid!',
    )]
    private ?string $email = null;

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
    private ?float $paidAmount = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?ticket $tickets = null;

    #[ORM\ManyToOne(inversedBy: 'bookings')]
    private ?user $user = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFestival(): ?festival
    {
        return $this->festival;
    }

    public function setFestival(?festival $festival): static
    {
        $this->festival = $festival;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

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

    public function getTickets(): ?ticket
    {
        return $this->tickets;
    }

    public function setTickets(?ticket $tickets): static
    {
        $this->tickets = $tickets;

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

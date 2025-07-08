<?php

namespace App\Entity;

use App\Repository\FestivalRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints;


#[ORM\Entity(repositoryClass: FestivalRepository::class)]
class Festival
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Constraints\NotBlank(message: 'Introdu un nume!')]
    #[Constraints\Length(
        min: 5,
        max: 50,
        maxMessage: 'Numele trupei are lungimea de maxim {{ limit }} caractere',
        minMessage: 'Numele trupei are lungimea de minim {{ limit }} caractere',
    )]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Constraints\NotBlank(message: 'Introdu o locatie!')]
    #[Constraints\Length(
        min: 5,
        max: 50,
        maxMessage: 'Numele locatiei are lungimea de maxim {{ limit }} caractere',
        minMessage: 'Numele locatiei are lungimea de minim {{ limit }} caractere',
    )]
    private ?string $location = null;

    /**
     * @var Collection<int, band>
     */
    #[ORM\ManyToMany(targetEntity: Band::class, inversedBy: 'festivals')]
    private Collection $bands;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Constraints\NotNull(message: 'Alege o data!')]
    private ?\DateTime $startDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Constraints\NotNull(message: 'Alegere o data!')]
    #[Constraints\GreaterThan(propertyPath: 'startDate', message: 'start date < end date!')]
    private ?\DateTime $endDate = null;

    /**
     * @var Collection<int, Booking>
     */
    #[ORM\OneToMany(targetEntity: Booking::class, mappedBy: 'festival')]
    private Collection $bookings;

    /**
     * @var Collection<int, Ticket>
     */
    #[ORM\OneToMany(targetEntity: Ticket::class, mappedBy: 'festivals')]
    private Collection $tickets;

    #[ORM\Column(length: 255)]
    private ?string $photo_path = null;

    /**
     * @var Collection<int, Code>
     */
    #[ORM\ManyToMany(targetEntity: Code::class, mappedBy: 'festivals')]
    private Collection $codes;

    public function __construct()
    {
        $this->bands = new ArrayCollection();
        $this->bookings = new ArrayCollection();
        $this->startDate = new \DateTime();
        $this->endDate = new \DateTime('+1 day');
        $this->tickets = new ArrayCollection();
        $this->codes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): static
    {
        $this->location = $location;

        return $this;
    }

    /**
     * @return Collection<int, band>
     */
    public function getBands(): Collection
    {
        return $this->bands;
    }

    public function addBand(band $band): static
    {
        if (!$this->bands->contains($band)) {
            $this->bands->add($band);
        }

        return $this;
    }

    public function removeBand(band $band): static
    {
        $this->bands->removeElement($band);

        return $this;
    }

    public function getStartDate(): ?\DateTime
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTime $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTime
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTime $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * @return Collection<int, Booking>
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): static
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings->add($booking);
            $booking->setFestival($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): static
    {
        if ($this->bookings->removeElement($booking)) {
            // set the owning side to null (unless already changed)
            if ($booking->getFestival() === $this) {
                $booking->setFestival(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Ticket>
     */
    public function getTickets(): Collection
    {
        return $this->tickets;
    }

    public function addTicket(Ticket $ticket): static
    {
        if (!$this->tickets->contains($ticket)) {
            $this->tickets->add($ticket);
            $ticket->setFestivals($this);
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): static
    {
        if ($this->tickets->removeElement($ticket)) {
            // set the owning side to null (unless already changed)
            if ($ticket->getFestivals() === $this) {
                $ticket->setFestivals(null);
            }
        }

        return $this;
    }

    public function getPhotoPath(): ?string
    {
        return $this->photo_path;
    }

    public function setPhotoPath(string $photo_path): static
    {
        $this->photo_path = $photo_path;

        return $this;
    }

    /**
     * @return Collection<int, Code>
     */
    public function getCodes(): Collection
    {
        return $this->codes;
    }

    public function addCode(Code $code): static
    {
        if (!$this->codes->contains($code)) {
            $this->codes->add($code);
            $code->addFestival($this);
        }

        return $this;
    }

    public function removeCode(Code $code): static
    {
        if ($this->codes->removeElement($code)) {
            $code->removeFestival($this);
        }

        return $this;
    }
}

<?php

namespace App\Entity;

use App\Enum\MusicGenre;
use App\Repository\BandRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints;

#[ORM\Entity(repositoryClass: BandRepository::class)]
class Band
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

    #[ORM\Column(enumType: MusicGenre::class)]
    #[Constraints\NotNull(message: 'Alege un gen muzical!')]
    private ?MusicGenre $musicGenre = null;

    /**
     * @var Collection<int, Festival>
     */
    #[ORM\ManyToMany(targetEntity: Festival::class, mappedBy: 'bands')]
    private Collection $festivals;

    /**
     * @var Collection<int, user>
     */
    #[ORM\ManyToMany(targetEntity: user::class, inversedBy: 'bands')]
    private Collection $users;

    public function __construct()
    {
        $this->festivals = new ArrayCollection();
        $this->users = new ArrayCollection();
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

    public function getMusicGenre(): ?MusicGenre
    {
        return $this->musicGenre;
    }

    public function setMusicGenre(MusicGenre $musicGenre): static
    {
        $this->musicGenre = $musicGenre;

        return $this;
    }

    /**
     * @return Collection<int, Festival>
     */
    public function getFestivals(): Collection
    {
        return $this->festivals;
    }

    public function addFestival(Festival $festival): static
    {
        if (!$this->festivals->contains($festival)) {
            $this->festivals->add($festival);
            $festival->addBand($this);
        }

        return $this;
    }

    public function removeFestival(Festival $festival): static
    {
        if ($this->festivals->removeElement($festival)) {
            $festival->removeBand($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, user>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(user $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
        }

        return $this;
    }

    public function removeUser(user $user): static
    {
        $this->users->removeElement($user);

        return $this;
    }
}

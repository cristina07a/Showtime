<?php

namespace App\Entity;

use App\Repository\CodeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints;

#[ORM\Entity(repositoryClass: CodeRepository::class)]
class Code
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Constraints\NotNull(message: 'Introdu un nume!')]
    private ?string $name = null;

    #[ORM\Column]
    #[Constraints\NotNull(message: 'Alege disponibilitatea')]
    private ?bool $isAvailable = null;

    #[ORM\Column]
    #[Constraints\NotNull(message: 'Stabileste procentajul')]
    #[Constraints\Range(
        min: 0,
        max: 100,
        notInRangeMessage: 'Procentajul are valori cuprinse intre {{ min }} si {{ max }}.'
    )]
    private ?int $percentage = null;

    /**
     * @var Collection<int, festival>
     */
    #[ORM\ManyToMany(targetEntity: festival::class, inversedBy: 'codes')]
    private Collection $festivals;

    public function __construct()
    {
        $this->festivals = new ArrayCollection();
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

    public function isAvailable(): ?bool
    {
        return $this->isAvailable;
    }

    public function setIsAvailable(bool $isAvailable): static
    {
        $this->isAvailable = $isAvailable;

        return $this;
    }

    public function getPercentage(): ?int
    {
        return $this->percentage;
    }

    public function setPercentage(int $percentage): static
    {
        $this->percentage = $percentage;

        return $this;
    }

    /**
     * @return Collection<int, festival>
     */
    public function getFestivals(): Collection
    {
        return $this->festivals;
    }

    public function addFestival(festival $festival): static
    {
        if (!$this->festivals->contains($festival)) {
            $this->festivals->add($festival);
        }

        return $this;
    }

    public function removeFestival(festival $festival): static
    {
        $this->festivals->removeElement($festival);

        return $this;
    }
}

<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Constraints as Assert;

#[MongoDB\Document(collection: 'sessions')]
class Session
{
    #[MongoDB\Id]
    private ?string $id = null;

    #[MongoDB\Field(type: 'string')]
    #[Assert\NotBlank]
    private ?string $langue = null;

    #[MongoDB\Field(type: 'date')]
    #[Assert\NotBlank]
    private ?\DateTime $date = null;

    #[MongoDB\Field(type: 'string')]
    #[Assert\NotBlank]
    private ?string $heure = null;

    #[MongoDB\Field(type: 'string')]
    #[Assert\NotBlank]
    private ?string $lieu = null;

    #[MongoDB\Field(type: 'int')]
    #[Assert\NotBlank]
    #[Assert\GreaterThanOrEqual(0)]
    private ?int $places = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getLangue(): ?string
    {
        return $this->langue;
    }

    public function setLangue(string $langue): self
    {
        $this->langue = $langue;
        return $this;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function getHeure(): ?string
    {
        return $this->heure;
    }

    public function setHeure(string $heure): self
    {
        $this->heure = $heure;
        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(string $lieu): self
    {
        $this->lieu = $lieu;
        return $this;
    }

    public function getPlaces(): ?int
    {
        return $this->places;
    }

    public function setPlaces(int $places): self
    {
        $this->places = $places;
        return $this;
    }

    public function decrementPlaces(): self
    {
        if ($this->places > 0) {
            $this->places--;
        }
        return $this;
    }

    public function incrementPlaces(): self
    {
        $this->places++;
        return $this;
    }

    public function hasAvailablePlaces(): bool
    {
        return $this->places > 0;
    }
}
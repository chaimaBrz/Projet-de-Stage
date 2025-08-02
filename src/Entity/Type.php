<?php

namespace App\Entity;

use App\Repository\TypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TypeRepository::class)]
class Type
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    /**
     * @var Collection<int, Equipement>
     */
    #[ORM\OneToMany(targetEntity: Equipement::class, mappedBy: 'type')]
    private Collection $equipements;

    /**
     * @var Collection<int, EvenementHistorique>
     */
    #[ORM\OneToMany(targetEntity: EvenementHistorique::class, mappedBy: 'Type')]
    private Collection $evenementHistoriques;

    public function __construct()
    {
        $this->equipements = new ArrayCollection();
        $this->evenementHistoriques = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return Collection<int, Equipement>
     */
    public function getEquipements(): Collection
    {
        return $this->equipements;
    }

    public function addEquipement(Equipement $equipement): static
    {
        if (!$this->equipements->contains($equipement)) {
            $this->equipements->add($equipement);
            $equipement->setType($this);
        }

        return $this;
    }

    public function removeEquipement(Equipement $equipement): static
    {
        if ($this->equipements->removeElement($equipement)) {
            // set the owning side to null (unless already changed)
            if ($equipement->getType() === $this) {
                $equipement->setType(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, EvenementHistorique>
     */
    public function getEvenementHistoriques(): Collection
    {
        return $this->evenementHistoriques;
    }

    public function addEvenementHistorique(EvenementHistorique $evenementHistorique): static
    {
        if (!$this->evenementHistoriques->contains($evenementHistorique)) {
            $this->evenementHistoriques->add($evenementHistorique);
            $evenementHistorique->setType($this);
        }

        return $this;
    }

    public function removeEvenementHistorique(EvenementHistorique $evenementHistorique): static
    {
        if ($this->evenementHistoriques->removeElement($evenementHistorique)) {
            // set the owning side to null (unless already changed)
            if ($evenementHistorique->getType() === $this) {
                $evenementHistorique->setType(null);
            }
        }

        return $this;
    }
}

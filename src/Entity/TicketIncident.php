<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
class TicketIncident
{
    #[Groups(['read'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type:"integer")]
    private ?int $id = null;

    #[Groups(['read'])]
    #[ORM\Column(type:"string", length:255)]
    private ?string $titre = null;

    #[Groups(['read'])]
    #[ORM\Column(type:"text")]
    private ?string $description = null;

    #[Groups(['read'])]
    #[ORM\Column(type:"datetime")]
    private ?\DateTimeInterface $dateCreation = null;

    #[Groups(['read'])]
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable:false)]
    private ?User $userCreateur = null;

    #[Groups(['read'])]
    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $userAssigne = null;

    #[Groups(['read'])]
    #[ORM\Column(type:"string", length:50)]
    private ?string $statut = null;

    #[ORM\ManyToOne(inversedBy: 'ticketIncidents')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Equipement $equipement = null;

    #[Groups(['read'])]
    #[ORM\OneToMany(mappedBy: "ticketIncident", targetEntity: EvenementHistorique::class, cascade:["persist", "remove"])]
    private Collection $evenementsHistoriques;

    public function __construct()
    {
        $this->evenementsHistoriques = new ArrayCollection();
        $this->dateCreation = new \DateTime();
    }

    // Getters and setters...

    public function getId(): ?int { return $this->id; }

    public function getTitre(): ?string { return $this->titre; }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;
        return $this;
    }

    public function getDescription(): ?string { return $this->description; }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface { return $this->dateCreation; }

    public function setDateCreation(\DateTimeInterface $dateCreation): self
    {
        $this->dateCreation = $dateCreation;
        return $this;
    }

    public function getUserCreateur(): ?User { return $this->userCreateur; }

    public function setUserCreateur(?User $userCreateur): self
    {
        $this->userCreateur = $userCreateur;
        return $this;
    }

    public function getUserAssigne(): ?User { return $this->userAssigne; }

    public function setUserAssigne(?User $userAssigne): self
    {
        $this->userAssigne = $userAssigne;
        return $this;
    }

    public function getStatut(): ?string { return $this->statut; }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;
        return $this;
    }

    public function getEvenementsHistoriques(): Collection
    {
        return $this->evenementsHistoriques;
    }

    public function addEvenementHistorique(EvenementHistorique $evenementHistorique): self
    {
        if (!$this->evenementsHistoriques->contains($evenementHistorique)) {
            $this->evenementsHistoriques[] = $evenementHistorique;
            $evenementHistorique->setTicketIncident($this);
        }
        return $this;
    }

    public function removeEvenementHistorique(EvenementHistorique $evenementHistorique): self
    {
        if ($this->evenementsHistoriques->removeElement($evenementHistorique)) {
            if ($evenementHistorique->getTicketIncident() === $this) {
                $evenementHistorique->setTicketIncident(null);
            }
        }
        return $this;
    }
    public function getEquipement(): ?Equipement
    {
    return $this->equipement;
   }

    public function setEquipement(?Equipement $equipement): self
    {
    $this->equipement = $equipement;
    return $this;
    }

}

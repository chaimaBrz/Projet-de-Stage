<?php

namespace App\Entity;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\EquipementRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\Alerte;
use App\Entity\TicketIncident;
use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Serializer\Annotation\SerializedName;


#[ORM\Entity(repositoryClass: EquipementRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['read']],
    denormalizationContext: ['groups' => ['write']]
)]
class Equipement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['read'])]
    private ?int $id = null;


   #[Groups(['read'])]
#[ORM\Column(type: 'string', length: 255)]
private ?string $nom = null;

    
    #[ORM\Column(type: 'integer')]
    private ?int $etat = null;

   #[Groups(['read'])]
#[SerializedName('date_installation')]
#[ORM\Column(type: 'datetime')]
private ?\DateTime $dateinstallation = null;

    #[ORM\ManyToOne(inversedBy: 'equipements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Type $type = null;

    #[ORM\ManyToOne(inversedBy: 'equipements')]
    private ?Fournisseur $fournisseur = null;

    #[ORM\OneToMany(mappedBy: 'equipement', targetEntity: Alerte::class, orphanRemoval: true)]
    private Collection $alertes;

#[ORM\OneToMany(mappedBy: 'equipement', targetEntity: TicketIncident::class)]
private Collection $ticketIncidents;

    #[ORM\OneToMany(mappedBy: 'equipement', targetEntity: EvenementHistorique::class, fetch: 'EAGER')]

private Collection $evenements;


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

    public function getEtat(): ?int
    {
        return $this->etat;
    }

    public function setEtat(int $etat): static
    {
        $this->etat = $etat;
        return $this;
    }

    public function getDateinstallation(): ?\DateTime
{
    return $this->dateinstallation;  // d minuscule
}

public function setDateinstallation(\DateTime $dateinstallation): static
{
    $this->dateinstallation = $dateinstallation;  // d minuscule
    return $this;
}


    public function getType(): ?Type
    {
        return $this->type;
    }

    public function setType(?Type $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getFournisseur(): ?Fournisseur
    {
        return $this->fournisseur;
    }

    public function setFournisseur(?Fournisseur $fournisseur): static
    {
        $this->fournisseur = $fournisseur;
        return $this;
    }

    public function getAlertes(): Collection
    {
        return $this->alertes;
    }

    public function addAlerte(Alerte $alerte): static
    {
        if (!$this->alertes->contains($alerte)) {
            $this->alertes[] = $alerte;
            $alerte->setEquipement($this);
        }

        return $this;
    }

    public function removeAlerte(Alerte $alerte): static
    {
        if ($this->alertes->removeElement($alerte)) {
            if ($alerte->getEquipement() === $this) {
                $alerte->setEquipement(null);
            }
        }

        return $this;
    }

   public function __construct()
{
    $this->alertes = new ArrayCollection();
    $this->ticketIncidents = new ArrayCollection();
    $this->evenements = new ArrayCollection();
}

 /**
     * @return Collection|EvenementHistorique[]
     */
    public function getEvenements(): Collection
    {
        return $this->evenements;
    }

    public function addEvenement(EvenementHistorique $evenement): static
    {
        if (!$this->evenements->contains($evenement)) {
            $this->evenements[] = $evenement;
            $evenement->setEquipement($this);
        }

        return $this;
    }

    public function removeEvenement(EvenementHistorique $evenement): static
    {
        if ($this->evenements->removeElement($evenement)) {
            if ($evenement->getEquipement() === $this) {
                $evenement->setEquipement(null);
            }
        }

        return $this;
    }
public function getTicketIncidents(): Collection
{
    return $this->ticketIncidents;
}


    public function addTicketIncident(TicketIncident $ticket): static
    {
        if (!$this->ticketIncidents->contains($ticket)) {
            $this->ticketIncidents[] = $ticket;
            $ticket->setEquipement($this);
        }

        return $this;
    }

    public function removeTicketIncident(TicketIncident $ticket): static
    {
        if ($this->ticketIncidents->removeElement($ticket)) {
            if ($ticket->getEquipement() === $this) {
                $ticket->setEquipement(null);
            }
        }

        return $this;
    }


}

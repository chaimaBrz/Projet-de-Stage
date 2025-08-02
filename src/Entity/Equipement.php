<?php

namespace App\Entity;

use App\Repository\EquipementRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\Alerte;
use App\Entity\TicketIncident;

#[ORM\Entity(repositoryClass: EquipementRepository::class)]
class Equipement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: 'integer')]
    private ?int $etat = null;

    #[ORM\Column]
    private ?\DateTime $Dateinstallation = null;

    #[ORM\ManyToOne(inversedBy: 'equipements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Type $type = null;

    #[ORM\ManyToOne(inversedBy: 'equipements')]
    private ?Fournisseur $fournisseur = null;

    #[ORM\OneToMany(mappedBy: 'equipement', targetEntity: Alerte::class, orphanRemoval: true)]
    private Collection $alertes;

    // 👇 Nouvelle relation pour accéder aux tickets liés à cet équipement
    #[ORM\OneToMany(mappedBy: 'equipement', targetEntity: TicketIncident::class)]
    private Collection $ticketIncidents;

    #[ORM\OneToMany(mappedBy: 'equipement', targetEntity: EvenementHistorique::class)]
    private Collection $evenementsHistoriques;

    public function __construct()
    {
        $this->alertes = new ArrayCollection();
        $this->ticketIncidents = new ArrayCollection();
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
        return $this->Dateinstallation;
    }

    public function setDateinstallation(\DateTime $Dateinstallation): static
    {
        $this->Dateinstallation = $Dateinstallation;
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

    // ✅ Nouveaux getters/setters pour ticketIncidents

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

<?php

namespace App\Entity;

use App\Repository\EvenementHistoriqueRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;


#[ORM\Entity(repositoryClass: EvenementHistoriqueRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['read']],
    denormalizationContext: ['groups' => ['write']]
)]

class EvenementHistorique
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read'])]
    private ?int $id = null;

    #[Groups(['read'])]
    #[ORM\Column]
    private ?\DateTime $date = null;

    #[Groups(['read'])]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'evenementHistoriques')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Type $type = null;

    // ❌ PAS de groupe ici pour éviter la boucle
    #[ORM\ManyToOne(targetEntity: TicketIncident::class, inversedBy: "evenementsHistoriques")]
    #[ORM\JoinColumn(nullable: false)]
    private ?TicketIncident $ticketIncident = null;

    // ✅ OK pour lire l’équipement
    #[Groups(['read'])]
    #[ORM\ManyToOne(targetEntity: Equipement::class, inversedBy: 'evenements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Equipement $equipement = null;


public function getEquipement(): ?Equipement
{
    return $this->equipement;
}

public function setEquipement(?Equipement $equipement): self
{
    $this->equipement = $equipement;
    return $this;
}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): static
    {
        $this->date = $date;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
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

    public function getTicketIncident(): ?TicketIncident
    {
        return $this->ticketIncident;
    }

    public function setTicketIncident(?TicketIncident $ticketIncident): static
    {
        $this->ticketIncident = $ticketIncident;
        return $this;
    }
}

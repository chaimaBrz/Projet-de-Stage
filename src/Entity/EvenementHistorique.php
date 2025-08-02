<?php

namespace App\Entity;

use App\Repository\EvenementHistoriqueRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: EvenementHistoriqueRepository::class)]
class EvenementHistorique
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
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

    #[ORM\ManyToOne(targetEntity: TicketIncident::class, inversedBy: "evenementsHistoriques")]
    #[ORM\JoinColumn(nullable: false)]
    private ?TicketIncident $ticketIncident = null;

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

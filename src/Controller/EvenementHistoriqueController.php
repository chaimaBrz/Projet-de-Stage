<?php

namespace App\Controller;

use App\Entity\EvenementHistorique;
use App\Entity\Type;
use App\Entity\Equipement;
use App\Entity\TicketIncident;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'EvenementHistoriques')]
#[Route('/api/evenement-historiques', name: 'api_evenement_historiques_')]
class EvenementHistoriqueController extends AbstractController
{
    

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id, EntityManagerInterface $em): JsonResponse
    {
        $event = $em->getRepository(EvenementHistorique::class)->find($id);

        if (!$event) {
            return new JsonResponse(['message' => 'Événement non trouvé'], 404);
        }

        $data = [
            'id' => $event->getId(),
            'date' => $event->getDate()?->format('Y-m-d'),
            'description' => $event->getDescription(),
            'type_id' => $event->getType()?->getId(),
            'ticket_incident_id' => $event->getTicketIncident()?->getId(),
        ];

        return new JsonResponse($data);
    }

    #[Route('/create', name: 'create', methods: ['POST'])]
    #[OA\Post(
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                required: ['date', 'description', 'type_id', 'ticket_incident_id'],
                properties: [
                    new OA\Property(property: 'date', type: 'string', format: 'date', example: '2025-07-29'),
                    new OA\Property(property: 'description', type: 'string', example: 'Description de l\'événement'),
                    new OA\Property(property: 'type_id', type: 'integer', example: 1),
                    new OA\Property(property: 'ticket_incident_id', type: 'integer', example: 2)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Événement créé avec succès'),
            new OA\Response(response: 400, description: 'Données invalides ou manquantes'),
            new OA\Response(response: 404, description: 'Type ou ticket incident introuvable'),
        ]
    )]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['date'], $data['description'], $data['type_id'], $data['ticket_incident_id'])) {
            return new JsonResponse(['error' => 'Champs manquants'], 400);
        }

        $type = $em->getRepository(Type::class)->find($data['type_id']);
        if (!$type) {
            return new JsonResponse(['error' => 'Type introuvable'], 404);
        }

        $ticket = $em->getRepository(TicketIncident::class)->find($data['ticket_incident_id']);
        if (!$ticket) {
            return new JsonResponse(['error' => 'Ticket incident introuvable'], 404);
        }

        $event = new EvenementHistorique();
        try {
            $event->setDate(new \DateTime($data['date']));
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Date invalide'], 400);
        }
        $event->setDescription($data['description']);
        $event->setType($type);
        $event->setTicketIncident($ticket);

        $em->persist($event);
        $em->flush();

        return new JsonResponse(['message' => 'Événement créé avec succès', 'id' => $event->getId()], 201);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(int $id, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $event = $em->getRepository(EvenementHistorique::class)->find($id);
        if (!$event) {
            return new JsonResponse(['message' => 'Événement non trouvé'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['date'])) {
            try {
                $event->setDate(new \DateTime($data['date']));
            } catch (\Exception $e) {
                return new JsonResponse(['error' => 'Date invalide'], 400);
            }
        }
        if (isset($data['description'])) {
            $event->setDescription($data['description']);
        }
        if (isset($data['type_id'])) {
            $type = $em->getRepository(Type::class)->find($data['type_id']);
            if (!$type) {
                return new JsonResponse(['error' => 'Type introuvable'], 404);
            }
            $event->setType($type);
        }
        if (isset($data['ticket_incident_id'])) {
            $ticket = $em->getRepository(TicketIncident::class)->find($data['ticket_incident_id']);
            if (!$ticket) {
                return new JsonResponse(['error' => 'Ticket incident introuvable'], 404);
            }
            $event->setTicketIncident($ticket);
        }

        $em->flush();

        return new JsonResponse(['message' => 'Événement mis à jour']);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $em): JsonResponse
    {
        $event = $em->getRepository(EvenementHistorique::class)->find($id);
        if (!$event) {
            return new JsonResponse(['message' => 'Événement non trouvé'], 404);
        }

        $em->remove($event);
        $em->flush();

        return new JsonResponse(['message' => 'Événement supprimé']);
    }
   


#[Route('/', name: 'index', methods: ['GET'])] // ✅ nouvelle annotation correcte

public function index(EntityManagerInterface $em): JsonResponse
{
    $equipements = $em->getRepository(Equipement::class)->findAll();
    $data = [];

    foreach ($equipements as $equipement) {
        $evenements = [];

        foreach ($equipement->getTicketIncidents() as $ticket) {
            foreach ($ticket->getEvenementsHistoriques() as $evenement) {
                $evenements[] = [
                    'id' => $evenement->getId(),
                    'date' => $evenement->getDate()->format('Y-m-d'),
                    'description' => $evenement->getDescription(),
                    'ticket' => [
    'id' => $ticket->getId(),
    'titre' => $ticket->getTitre(),
    'description' => $ticket->getDescription()
]

                ];
            }
        }

        if (!empty($evenements)) {
            $data[] = [
                'equipement' => [
                    'id' => $equipement->getId(),
                    'nom' => $equipement->getNom(),
                ],
                'evenements' => $evenements
            ];
        }
    }

    return new JsonResponse($data);
}


}

<?php

namespace App\Controller\Api;
use App\Entity\EvenenemntHistorique;
use App\Entity\TicketIncident;
use App\Entity\User;
use App\Repository\TicketIncidentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Attributes as OA;
#[OA\Tag(name: 'TicketIncident')]
#[Route('/api/ticket_incidents')]
class TicketIncidentController extends AbstractController
{
    #[Route('', name: 'ticket_incident_index', methods: ['GET'])]
    #[OA\Get(description: 'Liste des tickets incidents')]
    #[OA\Response(response: 200, description: 'Liste des tickets incidents')]
    public function index(TicketIncidentRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $tickets = $repository->findAll();
        $json = $serializer->serialize($tickets, 'json', ['groups' => 'read']);
        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/{id}', name: 'ticket_incident_show', methods: ['GET'])]
    #[OA\Get(description: 'Afficher un ticket par ID')]
    #[OA\Response(response: 200, description: 'Ticket trouvé')]
    #[OA\Response(response: 404, description: 'Ticket non trouvé')]
    public function show(TicketIncident $ticket, SerializerInterface $serializer): JsonResponse
    {
        $json = $serializer->serialize($ticket, 'json', ['groups' => 'read']);
        return new JsonResponse($json, 200, [], true);
    }

    #[Route('', name: 'ticket_incident_create', methods: ['POST'])]
    #[OA\Post(description: 'Créer un ticket')]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'titre', type: 'string'),
                new OA\Property(property: 'description', type: 'string'),
                new OA\Property(property: 'userCreateurId', type: 'integer'),
                new OA\Property(property: 'userAssigneId', type: 'integer'),
                new OA\Property(property: 'statut', type: 'string')
            ]
        )
    )]
    #[OA\Response(response: 201, description: 'Ticket créé')]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $ticket = new TicketIncident();
        $ticket->setTitre($data['titre'] ?? '');
        $ticket->setDescription($data['description'] ?? '');
        $ticket->setStatut($data['statut'] ?? 'ouvert');
        $ticket->setDateCreation(new \DateTime());

        $createur = $em->getRepository(User::class)->find($data['userCreateurId'] ?? 0);
        if (!$createur) {
            return new JsonResponse(['error' => 'User createur non trouvé'], 400);
        }
        $ticket->setUserCreateur($createur);

        if (!empty($data['userAssigneId'])) {
            $assigne = $em->getRepository(User::class)->find($data['userAssigneId']);
            if ($assigne) {
                $ticket->setUserAssigne($assigne);
            }
        }

        $evenement = new EvenementHistorique();
        $evenement->setDescription('Création du ticket : ' . $ticket->getTitre());
        $evenement->setDate(new \DateTime());
        $evenement->setType($em->getRepository(Type::class)->find(1)); // adapte l’ID ou fais une logique dynamique
        $evenement->setTicketIncident($ticket); // relation directe

        $ticket->addEvenementHistorique($evenement); // facultatif mais propre

        $em->persist($ticket); // ça va persister aussi l’événement grâce au cascade
        $em->flush();

        return new JsonResponse(['message' => 'Ticket créé'], 201);
    }

    #[Route('/{id}', name: 'ticket_incident_update', methods: ['PUT'])]
    #[OA\Put(description: 'Mettre à jour un ticket')]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'titre', type: 'string'),
                new OA\Property(property: 'description', type: 'string'),
                new OA\Property(property: 'userCreateurId', type: 'integer'),
                new OA\Property(property: 'userAssigneId', type: 'integer'),
                new OA\Property(property: 'statut', type: 'string')
            ]
        )
    )]
    #[OA\Response(response: 200, description: 'Ticket mis à jour')]
    #[OA\Response(response: 404, description: 'Ticket non trouvé')]
    public function update(int $id, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $ticket = $em->getRepository(TicketIncident::class)->find($id);
        if (!$ticket) {
            return new JsonResponse(['error' => 'Ticket non trouvé'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['titre'])) {
            $ticket->setTitre($data['titre']);
        }
        if (isset($data['description'])) {
            $ticket->setDescription($data['description']);
        }
        if (isset($data['statut'])) {
            $ticket->setStatut($data['statut']);
        }

        if (isset($data['userCreateurId'])) {
            $createur = $em->getRepository(User::class)->find($data['userCreateurId']);
            if ($createur) {
                $ticket->setUserCreateur($createur);
            }
        }

        if (isset($data['userAssigneId'])) {
            $assigne = $em->getRepository(User::class)->find($data['userAssigneId']);
            if ($assigne) {
                $ticket->setUserAssigne($assigne);
            }
        }

        $em->flush();

        return new JsonResponse(['message' => 'Ticket mis à jour'], 200);
    }

    #[Route('/{id}', name: 'ticket_incident_delete', methods: ['DELETE'])]
    #[OA\Delete(description: 'Supprimer un ticket')]
    #[OA\Response(response: 204, description: 'Ticket supprimé')]
    #[OA\Response(response: 404, description: 'Ticket non trouvé')]
    public function delete(int $id, EntityManagerInterface $em): JsonResponse
    {
        $ticket = $em->getRepository(TicketIncident::class)->find($id);
        if (!$ticket) {
            return new JsonResponse(['error' => 'Ticket non trouvé'], 404);
        }

        $em->remove($ticket);
        $em->flush();

        return new JsonResponse(null, 204);
    }
}

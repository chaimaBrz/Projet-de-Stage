<?php

namespace App\Controller;

use App\Entity\Equipement;
use App\Repository\EquipementRepository;
use App\Repository\TypeRepository;
use App\Repository\FournisseurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Equipements')]
#[Route('/api/equipements', name: 'api_equipements_')]
class EquipementController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly EquipementRepository $equipementRepo,
        private readonly TypeRepository $typeRepo,
        private readonly FournisseurRepository $fournisseurRepo,
    ) {}

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $equipements = $this->equipementRepo->createQueryBuilder('e')
    ->leftJoin('e.alertes', 'a')
    ->addSelect('a')
    ->getQuery()
    ->getResult();


        $data = [];
        foreach ($equipements as $e) {
            $alerts = [];
            foreach ($e->getAlertes() as $a) {
                $alerts[] = [
                    'id' => $a->getId(),
                    'titre' => $a->getTitre(),
                ];
            }

            $data[] = [
                'id' => $e->getId(),
                'nom' => $e->getNom(),
                'alerts' => $alerts,
                'etat' => $e->getEtat(),
                'date_installation' => $e->getDateInstallation()?->format('Y-m-d'),
                'type_nom' => $e->getType()?->getNom(),
                'fournisseur_id' => $e->getFournisseur()?->getId(),
            ];
        }

        return $this->json($data);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $equipement = $this->equipementRepo->find($id);

        if (!$equipement) {
            return $this->json(['message' => 'Équipement non trouvé'], 404);
        }

        $evenements = [];
        foreach ($equipement->getEvenements() as $evenement) {
            $evenements[] = [
                'id' => $evenement->getId(),
                'date' => $evenement->getDate()->format('Y-m-d H:i'),
                'description' => $evenement->getDescription(),
                'type' => $evenement->getType()?->getNom(),
                'ticket_description' => $evenement->getTicketIncident()?->getDescription(),


            ];
        }

        $tickets = [];
        foreach ($equipement->getTicketIncidents() as $ticket) {
            $evenementsTicket = [];
            foreach ($ticket->getEvenementsHistoriques() as $evt) {
                $evenementsTicket[] = [
                    'id' => $evt->getId(),
                    'date' => $evt->getDate()->format('Y-m-d H:i'),
                    'description' => $evt->getDescription(),
                    'type' => $evt->getType()?->getNom(),
                ];
            }

            $tickets[] = [
                'id' => $ticket->getId(),
                'titre' => $ticket->getTitre(),
                'description' => $ticket->getDescription(),
                'date_creation' => $ticket->getDateCreation()->format('Y-m-d H:i'),
                'statut' => $ticket->getStatut(),
                'user_createur' => $ticket->getUserCreateur()?->getId(),
                'user_assigne' => $ticket->getUserAssigne()?->getId(),
               
            ];
        }

        $data = [
            'id' => $equipement->getId(),
            'nom' => $equipement->getNom(),
            'etat' => $equipement->getEtat(),
            'date_installation' => $equipement->getDateInstallation()?->format('Y-m-d'),
            'type_nom' => $equipement->getType()?->getNom(),
            'fournisseur_id' => $equipement->getFournisseur()?->getId(),
            'evenements' => $evenements,
            'tickets' => $tickets,
        ];

        return $this->json($data);
    }

    #[OA\Post(
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: "object",
            properties: [
                new OA\Property(property: "nom", type: "string"),
                new OA\Property(property: "etat", type: "integer"),
                new OA\Property(property: "date_installation", type: "string", format: "date"),
                new OA\Property(property: "type_id", type: "integer"),
                new OA\Property(property: "fournisseur_id", type: "integer"),
            ]
        )
    ),
    responses: [
        new OA\Response(response: 201, description: "Équipement créé"),
        new OA\Response(response: 400, description: "Données invalides")
    ]
)]
#[Route(path: '', name: 'create', methods: ['POST'])]
#[Route(path: '/', name: 'create_slash', methods: ['POST'])]

public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['nom'], $data['etat'], $data['date_installation'], $data['type_id'])) {
            return $this->json(['message' => 'Données manquantes'], 400);
        }

        $type = $this->typeRepo->find($data['type_id']);
        if (!$type) {
            return $this->json(['message' => 'Type invalide'], 400);
        }

        $equipement = new Equipement();
        $equipement->setNom($data['nom']);
        $equipement->setEtat((int) $data['etat']);
        $equipement->setDateInstallation(new \DateTime($data['date_installation']));
        $equipement->setType($type);

        if (!empty($data['fournisseur_id'])) {
            $fournisseur = $this->fournisseurRepo->find($data['fournisseur_id']);
            if ($fournisseur) {
                $equipement->setFournisseur($fournisseur);
            }
        }

        $this->em->persist($equipement);
        $this->em->flush();

        return $this->json(['message' => 'Équipement créé avec succès', 'id' => $equipement->getId()], 201);
    }#[Route('/{id}', name: 'update', methods: ['PUT'])]
#[OA\RequestBody(
    required: true,
    content: new OA\JsonContent(
        type: "object",
        properties: [
            new OA\Property(property: "nom", type: "string", example: "Switch Cisco 2960"),
            new OA\Property(property: "etat", type: "integer", example: 1),
            new OA\Property(property: "date_installation", type: "string", format: "date", example: "2025-08-05"),
            new OA\Property(property: "type_nom", type: "string", example: "Switch"),
            new OA\Property(property: "fournisseur_id", type: "integer", example: 4),
        ]
    )
)]#[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $equipement = $this->equipementRepo->find($id);
        if (!$equipement) {
            return $this->json(['message' => 'Équipement non trouvé'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['nom'])) {
            $equipement->setNom($data['nom']);
        }
        if (isset($data['etat'])) {
            $equipement->setEtat((int) $data['etat']);
        }
        if (isset($data['date_installation'])) {
            $equipement->setDateInstallation(new \DateTime($data['date_installation']));
        }
        if (isset($data['type_nom'])) {
            $type = $this->typeRepo->find($data['type_nom']);
            if ($type) {
                $equipement->setType($type);
            }
        }
        if (isset($data['fournisseur_id'])) {
            $fournisseur = $this->fournisseurRepo->find($data['fournisseur_id']);
            if ($fournisseur) {
                $equipement->setFournisseur($fournisseur);
            }
        }

        $this->em->flush();

        return $this->json(['message' => 'Équipement mis à jour']);
    }
    

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $equipement = $this->equipementRepo->find($id);
        if (!$equipement) {
            return $this->json(['message' => 'Équipement non trouvé'], 404);
        }

        $this->em->remove($equipement);
        $this->em->flush();

        return $this->json(['message' => 'Équipement supprimé']);
    }
}

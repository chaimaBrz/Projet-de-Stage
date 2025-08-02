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
    #[OA\Get(
        summary: "Liste de tous les équipements",
        responses: [
            new OA\Response(
                response: 200,
                description: "Liste des équipements",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: "id", type: "integer"),
                            new OA\Property(property: "nom", type: "string"),
                            new OA\Property(property: "etat", type: "integer"),
                            new OA\Property(property: "date_installation", type: "string", format: "date"),
                            new OA\Property(property: "type_id", type: "integer"),
                            new OA\Property(property: "fournisseur_id", type: "integer", nullable: true),
                        ]
                    )
                )
            )
        ]
    )]
    public function index(): JsonResponse
    {
        $equipements = $this->equipementRepo->findAll();

        $data = [];
        foreach ($equipements as $e) {
            $alerts = [];
            foreach ($e->getAlertes() as $a) {
                $alerts[] = [
                    'id' => $a->getId(),
                    'nom' => $e->getNom(),
                ];
            }

            $data[] = [
                'id' => $e->getId(),
                'nom' => $e->getNom(),
                'alerts' => $alerts,
                'etat' => $e->getEtat(),
                'date_installation' => $e->getDateinstallation()?->format('Y-m-d'),
                'type_nom' => $e->getType()?->getNom(),
                'fournisseur_id' => $e->getFournisseur()?->getId(),
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    #[OA\Get(
        summary: "Afficher un équipement par ID",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID de l'équipement",
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Détails de l'équipement",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "id", type: "integer"),
                        new OA\Property(property: "nom", type: "string"),
                        new OA\Property(property: "etat", type: "integer"),
                        new OA\Property(property: "date_installation", type: "string", format: "date"),
                        new OA\Property(property: "type_id", type: "integer"),
                        new OA\Property(property: "fournisseur_id", type: "integer", nullable: true),
                    ]
                )
            ),
            new OA\Response(response: 404, description: "Équipement non trouvé")
        ]
    )]
    public function show(int $id): JsonResponse
    {
        $equipement = $this->equipementRepo->find($id);

        if (!$equipement) {
            return new JsonResponse(['message' => 'Équipement non trouvé'], 404);
        }

        $data = [
            'id' => $equipement->getId(),
            'nom' => $equipement->getNom(),
            'etat' => $equipement->getEtat(),
            'date_installation' => $equipement->getDateinstallation()?->format('Y-m-d'),
            'type_nom' => $equipement->getType()?->getNom(),
            'fournisseur_nom' => $equipement->getFournisseur()?->getNom(),

        ];

        return new JsonResponse($data);
    }

    #[Route('/', name: 'create', methods: ['POST'])]
    #[OA\Post(
        summary: "Créer un nouvel équipement",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: "object",
                required: ["nom", "etat", "date_installation", "type_id"],
                properties: [
                    new OA\Property(property: "nom", type: "string", example: "Serveur HP"),
                    new OA\Property(property: "etat", type: "integer", example: 1),
                    new OA\Property(property: "date_installation", type: "string", format: "date", example: "2024-07-01"),
                    new OA\Property(property: "type_id", type: "integer", example: 2),
                    new OA\Property(property: "fournisseur_id", type: "integer", example: 1, nullable: true),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Équipement créé avec succès"),
            new OA\Response(response: 400, description: "Erreur de validation")
        ]
    )]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $equipement = new Equipement();
        $equipement->setNom($data['nom'] ?? '');
        $equipement->setEtat((int) ($data['etat'] ?? 0));
        $equipement->setDateinstallation(new \DateTime($data['date_installation'] ?? 'now'));

        $type = $this->typeRepo->find($data['type_id'] ?? null);
        if (!$type) {
            return new JsonResponse(['message' => 'Type invalide'], 400);
        }
        $equipement->setType($type);

        if (isset($data['fournisseur_id'])) {
            $fournisseur = $this->fournisseurRepo->find($data['fournisseur_id']);
            if ($fournisseur) {
                $equipement->setFournisseur($fournisseur);
            }
        }

        $this->em->persist($equipement);
        $this->em->flush();

        return new JsonResponse(['message' => 'Équipement créé avec succès', 'id' => $equipement->getId()], 201);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    #[OA\Put(
        summary: "Mettre à jour un équipement",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID de l'équipement à mettre à jour",
                schema: new OA\Schema(type: "integer")
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "nom", type: "string", example: "Serveur Dell"),
                    new OA\Property(property: "etat", type: "integer", example: 0),
                    new OA\Property(property: "date_installation", type: "string", format: "date", example: "2024-08-01"),
                    new OA\Property(property: "type_id", type: "integer", example: 3),
                    new OA\Property(property: "fournisseur_id", type: "integer", example: 2, nullable: true),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Équipement mis à jour"),
            new OA\Response(response: 404, description: "Équipement non trouvé"),
            new OA\Response(response: 400, description: "Données invalides")
        ]
    )]
    public function update(int $id, Request $request): JsonResponse
    {
        $equipement = $this->equipementRepo->find($id);
        if (!$equipement) {
            return new JsonResponse(['message' => 'Équipement non trouvé'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['nom'])) {
            $equipement->setNom($data['nom']);
        }
        if (isset($data['etat'])) {
            $equipement->setEtat((int)$data['etat']);
        }
        if (isset($data['date_installation'])) {
            $equipement->setDateinstallation(new \DateTime($data['date_installation']));
        }
        if (isset($data['type_id'])) {
            $type = $this->typeRepo->find($data['type_id']);
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

        return new JsonResponse(['message' => 'Équipement mis à jour']);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[OA\Delete(
        summary: "Supprimer un équipement",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID de l'équipement à supprimer",
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Équipement supprimé"),
            new OA\Response(response: 404, description: "Équipement non trouvé")
        ]
    )]
    public function delete(int $id): JsonResponse
    {
        $equipement = $this->equipementRepo->find($id);
        if (!$equipement) {
            return new JsonResponse(['message' => 'Équipement non trouvé'], 404);
        }

        $this->em->remove($equipement);
        $this->em->flush();

        return new JsonResponse(['message' => 'Équipement supprimé']);
    }
    #[Route('/{id}/evenements', name: 'equipement_evenements', methods: ['GET'])]
public function getEvenementsHistorique(int $id): JsonResponse
{
    $equipement = $this->equipementRepo->find($id);
    if (!$equipement) {
        return new JsonResponse(['error' => 'Équipement non trouvé'], 404);
    }

    $evenements = [];
    foreach ($equipement->getTicketIncidents() as $ticket) {
        foreach ($ticket->getEvenementsHistoriques() as $evenement) {
            $evenements[] = [
                'id' => $evenement->getId(),
                'date' => $evenement->getDate()->format('Y-m-d H:i'),
                'description' => $evenement->getDescription(),
                'type' => $evenement->getType()->getNom()
            ];
        }
    }

    return new JsonResponse($evenements);
}

}

<?php

namespace App\Controller;

use App\Entity\Fournisseur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use OpenApi\Attributes as OA;

#[Route('/api/fournisseurs', name: 'api_fournisseurs_')]
#[OA\Tag(name: "Fournisseurs")]
class FournisseurController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    #[OA\Get(
        summary: "Liste tous les fournisseurs",
        responses: [
            new OA\Response(response: 200, description: "Liste des fournisseurs")
        ]
    )]
    public function index(EntityManagerInterface $em): JsonResponse
    {
        $fournisseurs = $em->getRepository(Fournisseur::class)->findAll();

        $data = [];
        foreach ($fournisseurs as $f) {
            $data[] = [
                'id' => $f->getId(),
                'nom' => $f->getNom(),
                'adresse' => $f->getAdresse(),
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    #[OA\Get(
        summary: "Affiche un fournisseur",
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Détails du fournisseur"),
            new OA\Response(response: 404, description: "Fournisseur non trouvé"),
        ]
    )]
    public function show(int $id, EntityManagerInterface $em): JsonResponse
    {
        $fournisseur = $em->getRepository(Fournisseur::class)->find($id);

        if (!$fournisseur) {
            return new JsonResponse(['message' => 'Fournisseur non trouvé'], 404);
        }

        $data = [
            'id' => $fournisseur->getId(),
            'nom' => $fournisseur->getNom(),
            'adresse' => $fournisseur->getAdresse(),
        ];

        return new JsonResponse($data);
    }

    #[Route('/create', name: 'create', methods: ['POST'])]
    #[OA\Post(
        summary: "Crée un nouveau fournisseur",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                required: ['nom', 'adresse'],
                properties: [
                    new OA\Property(property: 'nom', type: 'string', example: 'Fournisseur ABC'),
                    new OA\Property(property: 'adresse', type: 'string', example: '123 rue Exemple')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Fournisseur créé avec succès'),
            new OA\Response(response: 400, description: 'Champs manquants'),
        ]
    )]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['nom'], $data['adresse'])) {
            return new JsonResponse(['error' => 'Champs manquants'], 400);
        }

        $fournisseur = new Fournisseur();
        $fournisseur->setNom($data['nom']);
        $fournisseur->setAdresse($data['adresse']);

        $em->persist($fournisseur);
        $em->flush();

        return new JsonResponse(['message' => 'Fournisseur créé avec succès', 'id' => $fournisseur->getId()], 201);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    #[OA\Put(
        summary: "Met à jour un fournisseur existant",
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                properties: [
                    new OA\Property(property: 'nom', type: 'string', example: 'Nouveau nom'),
                    new OA\Property(property: 'adresse', type: 'string', example: 'Nouvelle adresse')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Fournisseur mis à jour'),
            new OA\Response(response: 404, description: 'Fournisseur non trouvé'),
        ]
    )]
    public function update(int $id, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $fournisseur = $em->getRepository(Fournisseur::class)->find($id);
        if (!$fournisseur) {
            return new JsonResponse(['message' => 'Fournisseur non trouvé'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['nom'])) {
            $fournisseur->setNom($data['nom']);
        }
        if (isset($data['adresse'])) {
            $fournisseur->setAdresse($data['adresse']);
        }

        $em->flush();

        return new JsonResponse(['message' => 'Fournisseur mis à jour']);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[OA\Delete(
        summary: "Supprime un fournisseur",
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Fournisseur supprimé'),
            new OA\Response(response: 404, description: 'Fournisseur non trouvé'),
        ]
    )]
    public function delete(int $id, EntityManagerInterface $em): JsonResponse
    {
        $fournisseur = $em->getRepository(Fournisseur::class)->find($id);
        if (!$fournisseur) {
            return new JsonResponse(['message' => 'Fournisseur non trouvé'], 404);
        }

        $em->remove($fournisseur);
        $em->flush();

        return new JsonResponse(['message' => 'Fournisseur supprimé']);
    }
}

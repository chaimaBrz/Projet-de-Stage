<?php

namespace App\Controller\Api;

use App\Entity\Inventaire;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use OpenApi\Attributes as OA;

#[Route('/api/inventaires', name: 'api_inventaires_')]
class InventaireController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    #[OA\Tag(name: 'Inventaire')]
    public function index(EntityManagerInterface $em): JsonResponse
    {
        $inventaires = $em->getRepository(Inventaire::class)->findAll();
        $data = [];

        foreach ($inventaires as $inventaire) {
            $data[] = [
                'id' => $inventaire->getId(),
                'etat' => $inventaire->getEtat(),
                'date' => $inventaire->getDate()->format('Y-m-d'),
                'reference' => $inventaire->getReference(),
                'statut' => $inventaire->getStatut(),
                'commentaire' => $inventaire->getCommentaire(),
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    #[OA\Tag(name: 'Inventaire')]
    public function show(int $id, EntityManagerInterface $em): JsonResponse
    {
        $inventaire = $em->getRepository(Inventaire::class)->find($id);

        if (!$inventaire) {
            return new JsonResponse(['error' => 'Inventaire non trouvé'], 404);
        }

        return new JsonResponse([
            'id' => $inventaire->getId(),
            'etat' => $inventaire->getEtat(),
            'date' => $inventaire->getDate()->format('Y-m-d'),
            'reference' => $inventaire->getReference(),
            'statut' => $inventaire->getStatut(),
            'commentaire' => $inventaire->getCommentaire(),
        ]);
    }

    #[Route('/create', name: 'create', methods: ['POST'])]
    #[OA\Tag(name: 'Inventaire')]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'etat', type: 'string'),
                new OA\Property(property: 'date', type: 'string', format: 'date'),
                new OA\Property(property: 'reference', type: 'string'),
                new OA\Property(property: 'statut', type: 'string'),
                new OA\Property(property: 'commentaire', type: 'string', nullable: true),
            ]
        )
    )]
    #[OA\Response(response: 201, description: 'Inventaire créé')]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['etat'], $data['date'], $data['reference'], $data['statut'])) {
            throw new BadRequestHttpException('Champs requis : etat, date, reference, statut');
        }

        $inventaire = new Inventaire();
        $inventaire->setEtat($data['etat']);
        $inventaire->setDate(new \DateTime($data['date']));
        $inventaire->setReference($data['reference']);
        $inventaire->setStatut($data['statut']);
        $inventaire->setCommentaire($data['commentaire'] ?? null);

        $em->persist($inventaire);
        $em->flush();

        return new JsonResponse(['message' => 'Inventaire créé'], 201);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    #[OA\Tag(name: 'Inventaire')]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'etat', type: 'string'),
                new OA\Property(property: 'date', type: 'string', format: 'date'),
                new OA\Property(property: 'reference', type: 'string'),
                new OA\Property(property: 'statut', type: 'string'),
                new OA\Property(property: 'commentaire', type: 'string', nullable: true),
            ]
        )
    )]
    public function update(int $id, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $inventaire = $em->getRepository(Inventaire::class)->find($id);

        if (!$inventaire) {
            return new JsonResponse(['error' => 'Inventaire non trouvé'], 404);
        }

        $data = json_decode($request->getContent(), true);

        $inventaire->setEtat($data['etat']);
        $inventaire->setDate(new \DateTime($data['date']));
        $inventaire->setReference($data['reference']);
        $inventaire->setStatut($data['statut']);
        $inventaire->setCommentaire($data['commentaire'] ?? null);

        $em->flush();

        return new JsonResponse(['message' => 'Inventaire mis à jour']);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[OA\Tag(name: 'Inventaire')]
    public function delete(int $id, EntityManagerInterface $em): JsonResponse
    {
        $inventaire = $em->getRepository(Inventaire::class)->find($id);

        if (!$inventaire) {
            return new JsonResponse(['error' => 'Inventaire non trouvé'], 404);
        }

        $em->remove($inventaire);
        $em->flush();

        return new JsonResponse(['message' => 'Inventaire supprimé']);
    }
}

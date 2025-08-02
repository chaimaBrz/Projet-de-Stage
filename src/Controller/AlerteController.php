<?php

namespace App\Controller;

use App\Entity\Alerte;
use App\Entity\Equipement;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

#[Route('/api/alertes', name: 'api_alertes_')]
class AlerteController extends AbstractController
{
    #[Route('/create', name: 'create', methods: ['POST'])]
    #[OA\Tag(name: 'Alerte')]
    #[OA\Post(
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                properties: [
                    new OA\Property(property: 'titre', type: 'string', example: 'Surchauffe CPU'),
                    new OA\Property(property: 'message', type: 'string', example: 'La température dépasse 80°C'),
                    new OA\Property(property: 'niveau', type: 'integer', example: 0),
                    new OA\Property(property: 'equipement', type: 'integer', example: 1)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Alerte créée avec succès'),
            new OA\Response(response: 400, description: 'Champs requis manquants'),
            new OA\Response(response: 404, description: 'Équipement introuvable')
        ]
    )]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new BadRequestHttpException('JSON invalide : ' . $e->getMessage());
        }

        // ✅ Ne pas utiliser empty() ici pour éviter l'erreur avec 0
        $requiredFields = ['titre', 'message', 'niveau', 'equipement'];
        foreach ($requiredFields as $field) {
            if (!array_key_exists($field, $data)) {
                return new JsonResponse(['error' => 'Champs requis : titre, message, niveau, equipement'], 400);
            }
        }

        $equipement = $em->getRepository(Equipement::class)->find($data['equipement']);
        if (!$equipement) {
            return new JsonResponse(['error' => 'Equipement introuvable'], 404);
        }

        $alerte = new Alerte();
        $alerte->setTitre($data['titre']);
        $alerte->setMessage($data['message']);
        $alerte->setNiveau((int) $data['niveau']);
        $alerte->setDate(new \DateTimeImmutable());
        $alerte->setEquipement($equipement);

        $em->persist($alerte);
        $em->flush();

        return new JsonResponse(['message' => 'Alerte créée avec succès'], 201);
    }
}

<?php

namespace App\Controller;

use App\Entity\Type;
use App\Entity\Product;
use App\Repository\TypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Cocur\Slugify\Slugify;




class TypeController extends AbstractController
{

    // -------------------------------------------------------------------------------------------------------
    // ---------------------------------------------READ------------------------------------------------------
    // -------------------------------------------------------------------------------------------------------

    // #[Route('/api/types', name: 'get_types', methods: ['GET'])]
    // public function getTypes(TypeRepository $typeRepository): JsonResponse
    // {
    //     $types = $typeRepository->findAll();

    //     $typeData = [];

    //     foreach ($types as $type) {


    //         $typeData[] = [
    //             'idType' => $type->getId(),
    //             'Nom' => $type->getTypeName(),

    //         ];
    //     }

    //     return $this->json($typeData, Response::HTTP_OK);
    // }


    #[Route('/api/types', name: 'get_types', methods: ['GET'])]
    public function getTypes(TypeRepository $typeRepository): JsonResponse
    {
        $types = $typeRepository->findAll();
    
        $typeData = [];
    
        foreach ($types as $type) {
            $parent_id = ($type->getParent() !== null) ? $type->getParent()->getId() : null;
    
            $typeData[] = [
                'idType' => $type->getId(),
                'Nom' => $type->getTypeName(),
                'parent_id' => $parent_id,
            ];
        }
    
        return $this->json($typeData, Response::HTTP_OK);
    }
    
    
    // -------------------------------------------------------------------------------------------------------
    // ---------------------------------------------LIRE UN TYPE-----------------------------------------------
    // -------------------------------------------------------------------------------------------------------
    // #[Route('/api/types/{id}', name: 'get_type', methods: ['GET'])]
    
    // /**
    //  * @param Type $type
    //  * @return JsonResponse
    //  */
    // public function getType(Type $type): JsonResponse
    // {
    //     // Construire un tableau associatif avec les données du type spécifié
    //     $typeData = [
    //         'idType' => $type->getId(),
    //         'Nom' => $type->getTypeName(),
    //         // Ajoutez d'autres propriétés au besoin
    //     ];

    //     // Retourner une réponse JSON avec les données du type spécifié et un code HTTP OK
    //     return $this->json($typeData, Response::HTTP_OK);
    // }

    #[Route('/api/types/{id}', name: 'get_type', methods: ['GET'])]
    public function getType(Type $type): JsonResponse
    {
        // Construire un tableau associatif avec les données du type spécifié
        $typeData = [
            'idType' => $type->getId(),
            'Nom' => $type->getTypeName(),
            'parent_id' => $type->getParent() ? $type->getParent()->getId() : null,
            // Ajoutez d'autres propriétés au besoin
        ];
    
        // Retourner une réponse JSON avec les données du type spécifié et un code HTTP OK
        return $this->json($typeData, Response::HTTP_OK);
    }
    

    // -------------------------------------------------------------------------------------------------------
    // ---------------------------------------------MISE À JOUR------------------------------------------------
    // -------------------------------------------------------------------------------------------------------

    #[Route('/api/types/{id}', name: 'update_type', methods: ['PUT'])]
    public function updateType(Request $request, Type $type, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Vérifie si les données requises sont présentes dans la requête
        if (isset($data['typeName']) && is_string($data['typeName'])) {
            // Met à jour le nom du type
            $type->setTypeName($data['typeName']);

            // Génération automatique du slug
            $type->updateSlug();

            // Enregistre les modifications dans la base de données
            $entityManager->flush();

            // Retourne une réponse de succès
            return $this->json(['message' => 'Type mis à jour avec succès'], Response::HTTP_OK);
        }

        // Retourne une réponse indiquant des données manquantes ou invalides
        return $this->json(['error' => 'Données invalides pour la mise à jour du type'], Response::HTTP_BAD_REQUEST);
    }


    // -------------------------------------------------------------------------------------------------------
    // ---------------------------------------------DELETE----------------------------------------------------
    // -------------------------------------------------------------------------------------------------------
    
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    // Action de suppression
    // #[Route('/api/types/{id}', name: 'type_delete', methods: ['DELETE'])]
    // public function deleteType(Request $request, int $id): Response
    // {
    //     // Utilisez $this->entityManager ici
    //     $type = $this->entityManager->getRepository(Type::class)->find($id);

    //     if (!$type) {
    //         return new Response("Type non trouvé pour l'ID $id.", Response::HTTP_NOT_FOUND);
    //     }

    //     // Supprimez le type de la base de données
    //     $this->entityManager->remove($type);
    //     $this->entityManager->flush();

    //     return new Response("Type $id supprimé avec succès.", Response::HTTP_OK);
    // }

    #[Route('/api/types/{id}', name: 'type_delete', methods: ['DELETE'])]
    public function deleteType(Request $request, int $id): JsonResponse
    {
        // Utilisez $this->entityManager ici
        $type = $this->entityManager->getRepository(Type::class)->find($id);
    
        if (!$type) {
            return new JsonResponse(["message" => "Type non trouvé pour l'ID $id."], Response::HTTP_NOT_FOUND);
        }
    
        // Supprimez le type de la base de données
        $this->entityManager->remove($type);
        $this->entityManager->flush();
    
        // Récupérez la liste mise à jour des types après la suppression
        $updatedTypes = $this->entityManager->getRepository(Type::class)->findAll();
    
        return new JsonResponse(["message" => "Type $id supprimé avec succès.", "types" => $updatedTypes], Response::HTTP_OK);
    }
    
// -------------------------------------------------------------------------------------------------------
    // ---------------------------------------------DELETE-MULTIPLE-------------------------------------------
    // -------------------------------------------------------------------------------------------------------
    #[Route('/api/types', name: 'delete_types', methods: ['DELETE'])]
    public function deleteTypes(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $selectedProducts = $data['selectedProducts'] ?? [];

        try {
            // Commencez la transaction pour garantir l'intégrité des données
            $entityManager->beginTransaction();

            // Ajoutez la logique pour supprimer les types basés sur les ID dans $selectedProducts
            foreach ($selectedProducts as $productId) {
                $type = $entityManager->getRepository(Type::class)->find($productId);

                if ($type) {
                    // Supprimez l'entité (le type) de la base de données
                    $entityManager->remove($type);
                }
            }

            // Exécutez les suppressions
            $entityManager->flush();

            // Validez la transaction
            $entityManager->commit();

            return $this->json(['message' => 'Types supprimés avec succès'], Response::HTTP_OK);
        } catch (\Exception $e) {
            // En cas d'erreur, annulez la transaction pour éviter les modifications non désirées
            $entityManager->rollback();

            return $this->json(['message' => 'Erreur lors de la suppression des types', 'error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
    
    // -------------------------------------------------------------------------------------------------------
    // ---------------------------------------------CREATE----------------------------------------------------
    // -------------------------------------------------------------------------------------------------------

    // #[Route('/api/types', name: 'create_type', methods: ['POST'])]
    // public function createType(Request $request, EntityManagerInterface $entityManager): JsonResponse
    // {
    //     $data = json_decode($request->getContent(), true);

    //     // Vérifie si les données requises sont présentes dans la requête
    //     if (isset($data['typeName'])) {
    //         $typeName = $data['typeName'];

    //         // Générez le slug à partir du nom du type
    //         $slugify = new Slugify();
    //         $slug = $slugify->slugify($typeName);

    //         // Crée un nouvel objet Type
    //         $type = new Type();
    //         $type->setTypeName($typeName);
    //         $type->setSlug($slug);

    //         // Persiste le nouvel objet dans la base de données
    //         $entityManager->persist($type);
    //         $entityManager->flush();

    //         // Retourne une réponse de succès
    //         return $this->json(['message' => 'Type créé avec succès', 'id' => $type->getId()], Response::HTTP_CREATED);
    //     }

    //     // Retourne une réponse indiquant des données manquantes ou invalides
    //     return $this->json(['error' => 'Données invalides pour la création du type'], Response::HTTP_BAD_REQUEST);
    // }

    // #[Route('/api/types', name: 'create_type', methods: ['POST'])]
    // public function createType(Request $request, EntityManagerInterface $entityManager): JsonResponse
    // {
    //     $data = json_decode($request->getContent(), true);

    //     // Vérifie si les données requises sont présentes dans la requête
    //     if (isset($data['typeName'])) {
    //         $typeName = $data['typeName'];

    //         // Générez le slug à partir du nom du type
    //         $slugify = new Slugify();
    //         $slug = $slugify->slugify($typeName);

    //         // Crée un nouvel objet Type
    //         $type = new Type();
    //         $type->setTypeName($typeName);
    //         $type->setSlug($slug);

    //         // Persiste le nouvel objet dans la base de données
    //         $entityManager->persist($type);
    //         $entityManager->flush();

    //         // Retourne une réponse de succès
    //         return $this->json(['message' => 'Type créé avec succès', 'id' => $type->getId()], Response::HTTP_CREATED);
    //     }

    //     // Retourne une réponse indiquant des données manquantes ou invalides
    //     return $this->json(['error' => 'Données invalides pour la création du type'], Response::HTTP_BAD_REQUEST);
    // }
 
    #[Route('/api/types', name: 'create_type', methods: ['POST'])]
public function createType(Request $request, EntityManagerInterface $entityManager, TypeRepository $typeRepository): JsonResponse
{
    $data = json_decode($request->getContent(), true);

    // Vérifie si les données requises sont présentes dans la requête
    if (isset($data['typeName'])) {
        $typeName = $data['typeName'];
        $parentTypeId = isset($data['parent_id']) ? $data['parent_id'] : null;

        // Générez le slug à partir du nom du type
        $slugify = new Slugify();
        $slug = $slugify->slugify($typeName);

        // Crée un nouvel objet Type
        $type = new Type();
        $type->setTypeName($typeName);
        $type->setSlug($slug);

        // Si parent_id est fourni, récupérez l'entité parente
        if ($parentTypeId !== null) {
            $parentType = $typeRepository->find($parentTypeId);
            // Assurez-vous que l'entité parente existe
            if ($parentType) {
                $type->setParent($parentType);
            } else {
                return $this->json(['error' => 'Type parent introuvable'], Response::HTTP_BAD_REQUEST);
            }
        }

        // Persiste le nouvel objet dans la base de données
        $entityManager->persist($type);
        $entityManager->flush();

        // Retourne une réponse de succès
        return $this->json(['message' => 'Type créé avec succès', 'id' => $type->getId()], Response::HTTP_CREATED);
    }

    // Retourne une réponse indiquant des données manquantes ou invalides
    return $this->json(['error' => 'Données invalides pour la création du type'], Response::HTTP_BAD_REQUEST);
}

    
}
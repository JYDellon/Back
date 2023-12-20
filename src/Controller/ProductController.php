<?php

namespace App\Controller;

use App\Entity\Type;
use App\Entity\Image;
use App\Entity\Product;
use Cocur\Slugify\Slugify;
use App\Repository\ImageRepository;
use App\Service\VersionningService;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Vich\UploaderBundle\Handler\UploadHandler;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\PropertyMappingFactory;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;


class ProductController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    // -------------------------------------------------------------------------------------------------------
    // ---------------------------------------------READ------------------------------------------------------
    // -------------------------------------------------------------------------------------------------------

    #[Route('/api/products', name: 'get_products', methods: ['GET'])]
    public function getProducts(ProductRepository $productRepository): JsonResponse
    {
        $products = $productRepository->findAll();

        $productData = [];

        foreach ($products as $product) {

            if ($product->getStock() > 0) {
                $productData[] = [
                    'id' => $product->getId(),
                    'Nom' => $product->getName(),
                    'Stock' => $product->getStock(),
                    'Référence' => $product->getReference(),
                    'Prix' => $product->getPrice(),
                    'Taxe' => $product->getTaxe(),
                    'DescriptionCourte' => $product->getDescription(),
                    // 'DescriptionDétaillée' => $product->getDetailedDescription(),
                    'Conditionnement' => $product->getMesurement(),
                    'createdAt' => $product->getCreatedAt()
                ];
            }
        }

        return $this->json($productData, Response::HTTP_OK);
    }
    // -------------------------------------------------------------------------------------------------------
    // ---------------------------------------------READ-ADMIN------------------------------------------------
    // -------------------------------------------------------------------------------------------------------

    #[Route('/api/admin/products', name: 'get_productsAdmin', methods: ['GET'])]
    public function getProductsAdmin(ProductRepository $productRepository): JsonResponse
    {
        $products = $productRepository->findAll();

        $productData = [];

        foreach ($products as $product) {

            if ($product->getStock() >= 0) {
                $productData[] = [
                    'id' => $product->getId(),
                    'Nom' => $product->getName(),
                    'Stock' => $product->getStock(),
                    'Référence' => $product->getReference(),
                    'Prix' => $product->getPrice(),
                    'Taxe' => $product->getTaxe(),
                    'DescriptionCourte' => $product->getDescription(),
                    'DescriptionDétaillée' => $product->getDetailedDescription(),
                    'Conditionnement' => $product->getMesurement(),
                    'createdAt' => $product->getCreatedAt()
                ];
            }
        }

        return $this->json($productData, Response::HTTP_OK);
    }

    // -------------------------------------------------------------------------------------------------------
    // ---------------------------------------------READ--DETAIL----------------------------------------------
    // -------------------------------------------------------------------------------------------------------

    #[Route('/api/products/{id}', name: 'detailProduct', methods: ['GET'])]
    public function getProduct($id, ProductRepository $productRepository): JsonResponse
    {
        $product = $productRepository->find($id);

        if (!$product) {
            // Gérez ici le cas où le produit n'est pas trouvé (par exemple, renvoyez une erreur 404)
            return new JsonResponse(['message' => 'Produit non trouvé'], 404);
        }

        // Construisez un tableau avec les caractéristiques du produit
        $produitData = [
            'idProduit' => $product->getId(),
            'Nom' => $product->getName(),
            'Stock' => $product->getStock(),
            'Référence' => $product->getReference(),
            'Prix' => $product->getPrice(),
            'Taxe' => $product->getTaxe(),
            'Description détaillée' => $product->getDetailedDescription(),
            'Conditionnement' => $product->getMesurement()
        ];

        return new JsonResponse($produitData);
    }
    // -------------------------------------------------------------------------------------------------------
    // ---------------------------------------------READ--DETAIL-ADMIN----------------------------------------
    // -------------------------------------------------------------------------------------------------------

    #[Route('/api/admin/products/{id}', name: 'detailProductAdmin', methods: ['GET'])]
    public function getProductAdmin($id, ProductRepository $productRepository): JsonResponse
    {
        // Récupérer le produit à partir de l'ID
        $product = $productRepository->find($id);

        // Vérifier si le produit existe
        if (!$product) {
            return new JsonResponse(['message' => 'Produit non trouvé'], 404);
        }

        // Récupérer la première image du produit
        $image = $product->getImages()->first();
        $imageUrl = $image ? $image->getUrl() : null;

        // Récupérer le type du produit et son ID
        $productType = $product->getProductType();
        $typeId = $productType ? $productType->getId() : null;
        $typeName = $productType ? $productType->getTypeName() : null;

        // Construire le tableau avec les caractéristiques du produit
        $productData = [
            'idProduit' => $product->getId(),
            'Nom' => $product->getName(),
            'Stock' => $product->getStock(),
            'Référence' => $product->getReference(),
            'Prix' => $product->getPrice(),
            'Taxe' => $product->getTaxe(),
            'DescriptionCourte' => $product->getDescription(),
            'DescriptionDétaillée' => $product->getDetailedDescription(),
            'Conditionnement' => $product->getMesurement(),
            'ImageUrl' => $imageUrl,
            'TypeId' => $typeId,
            'TypeName' => $typeName,
            'product_type_id' => $typeId,
            'product_type_name' => $typeName,
        ];

        // Retourner les données du produit au format JSON
        return new JsonResponse($productData);
    }


    // -------------------------------------------------------------------------------------------------------
    // ---------------------------------------------DELETE----------------------------------------------------
    // -------------------------------------------------------------------------------------------------------

    #[Route('/api/products/{id}', name: 'deleteProduct', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits suffisants pour supprimer un produit')]
    public function deleteProduct(Product $product, EntityManagerInterface $em): JsonResponse
    {
        // Supprimer les images liées au produit
        $images = $product->getImages();
        foreach ($images as $image) {
            $em->remove($image);
        }

        // Supprimer le produit lui-même
        $em->remove($product);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }


    // -------------------------------------------------------------------------------------------------------
    // ---------------------------------------------DELETE-MULTIPLE-------------------------------------------
    // -------------------------------------------------------------------------------------------------------


    #[Route('/api/products', name: 'deleteSelectedProducts', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits suffisants pour supprimer des produits')]
    public function deleteSelectedProducts(Request $request, EntityManagerInterface $em, ProductRepository $productRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $productIds = $data['productIds'] ?? [];

        if (empty($productIds)) {
            return new JsonResponse(['message' => 'Aucun produit sélectionné.'], Response::HTTP_BAD_REQUEST);
        }

        // Fetch products by IDs
        $products = $productRepository->findBy(['id' => $productIds]);

        // Delete products and associated images
        foreach ($products as $product) {
            $images = $product->getImages();
            foreach ($images as $image) {
                $em->remove($image);
            }
            $em->remove($product);
        }

        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
    // -------------------------------------------------------------------------------------------------------
    // ---------------------------------------------UPDATE----------------------------------------------------
    // -------------------------------------------------------------------------------------------------------

    #[Route('/api/products/{id}', name: "updateProduct", methods: ['PUT'])]
    public function updateProduct(
        Request $request,
        Product $currentProduct,
        EntityManagerInterface $em
    ): JsonResponse {
        $requestData = json_decode($request->getContent(), true);

        // Récupérez les valeurs actuelles de l'entité
        $currentProductData = [
            'Nom' => $currentProduct->getName(),
            'Stock' => $currentProduct->getStock(),
            'Référence' => $currentProduct->getReference(),
            'Prix' => $currentProduct->getPrice(),
            'Taxe' => $currentProduct->getTaxe(),
            'DescriptionCourte' => $currentProduct->getDescription(),
            'DescriptionDétaillée' => $currentProduct->getDetailedDescription(),
            'Conditionnement' => $currentProduct->getMesurement(),
        ];

        // Met à jour les champs avec les nouvelles valeurs de la requête
        foreach ($requestData as $key => $value) {
            if (array_key_exists($key, $currentProductData)) {
                $currentProductData[$key] = $value;
            }
        }

        // Met à jour les propriétés de l'entité
        $currentProduct->setName($currentProductData['Nom']);
        $currentProduct->setStock($currentProductData['Stock']);
        $currentProduct->setReference($currentProductData['Référence']);
        $currentProduct->setPrice($currentProductData['Prix']);
        $currentProduct->setTaxe($currentProductData['Taxe']);
        $currentProduct->setDescription($currentProductData['DescriptionCourte']);
        $currentProduct->setDetailedDescription($currentProductData['DescriptionDétaillée']);
        $currentProduct->setMesurement($currentProductData['Conditionnement']);


        // Mise à jour du type de produit
        $productTypeId = $requestData['product_type_id'] ?? null;
        if ($productTypeId) {
            $productType = $em->getRepository(Type::class)->find($productTypeId);
            if ($productType) {
                $currentProduct->setProductType($productType);
            }
        }

        // Effectuez la mise à jour en base de données
        $em->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }





    // -------------------------------------------------------------------------------------------------------
    // ---------------------------------------------CREATE----------------------------------------------------
    // -------------------------------------------------------------------------------------------------------

    #[Route('/api/product', name: 'createProduct', methods: ['POST'])]
    public function creation(Request $request, UploaderHelper $uploaderHelper): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            $product = new Product();
            $product->setName($data['name']);
            $product->setReference($data['reference']);
            $product->setPrice($data['price']);
            $product->setMesurement($data['mesurement']);
            $product->setStock($data['stock']);
            $product->setProductType($this->entityManager->getReference(Type::class, $data['product_type_id']));
            $product->setTaxe($data['Taxe']);
            $product->setDescription($data['Description']);
            $product->setDetailedDescription($data['detailed_description']);
            $product->setCreatedAt(new \DateTime());

            // Génération automatique du slug à partir du nom du produit
            $slugify = new Slugify();
            $slug = $slugify->slugify($data['name']);
            $product->setSlug($slug);

            $this->entityManager->persist($product);
            $this->entityManager->flush();

            // Créez une instance vide de Image et liez-la au produit
            $emptyImage = new Image();
            $emptyImage->setImage($product);

            $this->entityManager->persist($emptyImage);
            $this->entityManager->flush();

            // Retournez la réponse avec l'ID du produit créé
            return new JsonResponse(['id' => $product->getId(), 'message' => 'Le produit a été créé avec succès.']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    //--------------------------------IMAGEUPDATED-------------------------------------------

    #[Route('/api/products/{id}/upload-image', name: "upload_image", methods: ['POST'])]
    public function uploadImage(Request $request, Product $product, UploaderHelper $uploaderHelper): Response
    {
        // Vérifiez si la requête contient un fichier
        if ($request->files->has('image')) {
            $file = $request->files->get('image');

            // Récupérez l'image associée au produit
            $image = $product->getImages()->first();

            // Si aucune image n'est associée, créez une nouvelle instance
            if (!$image) {
                $image = new Image();
                $image->setImage($product);
                $this->entityManager->persist($image);
            }

            // Mettez à jour le champ imageFile
            $image->setImageFile($file);

            // Persistez l'image dans la base de données
            $this->entityManager->flush();

            // Mettez à jour l'URL avec l'URL générée par UploaderHelper
            $image->setUrl($uploaderHelper->asset($image, 'imageFile'));

            // Persistez les modifications dans l'entité Image
            $this->entityManager->persist($image);
            $this->entityManager->flush();

            // Retournez une réponse réussie avec l'URL
            return $this->json(['message' => 'Image uploaded successfully', 'image_url' => $image->getUrl()], Response::HTTP_CREATED);
        
            
        }

        // Retournez une réponse d'erreur si aucun fichier n'a été trouvé dans la requête
        return $this->json(['error' => 'No file found in the request'], Response::HTTP_BAD_REQUEST);
    
    }

//-------------------------------------IMAGECREATED-------------------------------------------------------
              


    // Modifier votre contrôleur
    #[Route('/api/products/{productId}/upload-image2', name: "upload_image2", methods: ['POST'])]
public function uploadImage2(
    Request $request,
    int $productId,
    EntityManagerInterface $entityManager,
    UploadHandler $uploadHandler,
    PropertyMappingFactory $mappingFactory,
    UploaderHelper $uploaderHelper
): JsonResponse {
    try {
        // Récupérez l'entité Product correspondante
        $product = $entityManager->getRepository(Product::class)->find($productId);

      
if (!$product) {
    // Ajoutez cette ligne pour vérifier si le produit est trouvé
    error_log('Le produit n\'a pas été trouvé pour l\'ID : ' . $productId);

    throw new HttpException(Response::HTTP_NOT_FOUND, 'Produit non trouvé');
}


        // Vérifiez si le champ 'image' existe dans la requête
        if ($request->files->has('file')) {
            $file = $request->files->get('file');

            // Récupérez l'image associée au produit
            $image = $product->getImages()->first();

            // Assurez-vous que l'image existe
            if (!$image) {
                // Si l'image n'existe pas, créez une nouvelle instance d'image
                $image = new Image();

                // Associez l'image au produit
                $image->setProduct($product);

                // Persistez la nouvelle image dans la base de données
                $this->entityManager->persist($image);
            }

            // Mettez à jour le champ imageFile
            $image->setImageFile($file);

            // Persistez l'image dans la base de données
            $this->entityManager->flush();

            // Mettez à jour l'URL avec l'URL générée par UploaderHelper
            $image->setUrl($uploaderHelper->asset($image, 'imageFile'));

            // Persistez les modifications dans l'entité Image
            $this->entityManager->persist($image);
            $this->entityManager->flush();

            // Retournez une réponse réussie avec l'URL
            return $this->json(['message' => 'Image uploaded successfully', 'image_url' => $image->getUrl()], Response::HTTP_CREATED);
        } else {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'Aucun fichier téléchargé');
        }
    } catch (HttpException $e) {
        return new JsonResponse(['message' => $e->getMessage()], $e->getStatusCode());
    } catch (\Exception $e) {
        return new JsonResponse(['message' => 'Une erreur inattendue s\'est produite', 'error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}


}
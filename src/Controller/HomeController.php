<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductRepository;

class HomeController extends AbstractController
{
    /**
     * @Route("/api/new-products", name="api_new_products", methods={"GET"})
     */
    public function getNewProducts(ProductRepository $productRepository): JsonResponse
    {
        $products = $productRepository->findBy(['name' => [
            'Crème de jour hydratant légère', 'Spray hydratant',
            'Masque purifiant et revitalisant', 'Après-shampoing fortifiant et hydratant'
        ]], [], 4);

        $formattedProducts = $this->formatProducts($products);

        return $this->json($formattedProducts);
    }

    /**
     * @Route("/api/popular-products", name="api_popular_products", methods={"GET"})
     */
    public function getPopularProducts(ProductRepository $productRepository): JsonResponse
    {
        $products = $productRepository->findBy(['name' => [
            'Shampoing fortifiant et nourrissant', 'Sérum anti-âge',
            'Savon exfoliant au sel de mer et concombre de mer', 'Lotion tonique appaisante'
        ]], [], 4);

        $formattedProducts = $this->formatProducts($products);

        return $this->json($formattedProducts);
    }

    private function formatProducts(array $products): array
    {
        $formattedProducts = [];
        foreach ($products as $product) {
            $images = [];

            foreach ($product->getImages() as $image) {
                $images[] = [
                    'url' => $image->getUrl(),
                ];
            }

            $formattedProducts[] = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'price' => $product->getPrice(),
                'images' => $images,
            ];
        }

        return $formattedProducts;
    }
}

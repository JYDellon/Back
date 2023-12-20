<?php

namespace App\DataFixtures;

use App\Entity\Image;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

class ImageFixtures extends Fixture implements DependentFixtureInterface
{
    private $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager): void
    {
        $productData = [
            'Shampoing fortifiant et nourrissant',
            'Shampoing antipelliculaire',
            'Shampoing pour cuir chevelu sensible',
            'Après-shampoing fortifiant et hydratant',
            'Après-shampoing pour cuir chevelu sensible',
            'Masque réparateur',
            'Masque hydratant',
            'Sérum capillaire revitalisant',
            'Sérum anti-âge',
            'Sérum éclaircissant',
            'Crème hydratante anti-âge',
            'Crème hydratant pour peau sensible',
            'Crème de jour hydratant légère',
            'Crème de nuit régénèrante',
            'Spray hydratant',
            'Lotion tonique appaisante',
            'Crème contour des yeux cernés et poches',
            'Crème contour des yeux anti-rides',
            'Masque purifiant et revitalisant',
            'Gel apaisant pour peau sensible',
            'Eau micellaire démaquillante',
            'Baume après-rasage hydratant',
            'Baume à lèvres hydratant',
            'Gel douche hydratant',
            'Gel douche énergisant',
            'Savon exfoliant au sel de mer et concombre de mer',
            'Lotion coporelle tonifiante',
            'Gommage exfoliant pour le corps',
            'Bombe de bain relaxante',
            'Traitement anti-acné',
            'Crème anti-tâches brunes',
            'Crème apaisante pour l\'eczéma et le psoriasis',
            'Shampoing ultra apaisant pour cheveux sec',
            'Crème réparatrice pour les pieds',
            'Gelée de concombre de mer',
            'Spray nasal à l\'eau de mer et extraits de concombre de mer',
            'Baume à lèvres réparateur',
            'Elixir marin pour renforcer le système immunitaire',
            'Sirop apaisant pour les maux de gorge',
            'Probiotiques pour la flore intestinale',
            'Crème solaire SPF 30',
            'Crème solaire SPF 50',
            'Lotion après-soleil apaisante',
            'Complément alimentaire marin multivitaminé',
            'Gélules d\'oméga-3 au concombre de mer',
            'Capsules anti-inflammatoires',
            'Capsules de collagène marin au concombre de mer',
            'Complément beauté et bien-être',
            'Gélules régénèrantes pour les cheveux et les ongles',
            'Tisanes relaxantes',
            'Thé digestif',
            'Poudre de concombre de mer',
        ];

        foreach ($productData as $productName) {
            $prodName = $this->getReference('product_' . $this->slugify($productName));
            $image = new Image();
            $image->setUrl('/images/products/' . $this->slugify($productName) . '.jpg');
            $image->setImage($prodName);
            $manager->persist($image);
        }
        $manager->flush();
    }
    private function slugify(string $text): string
    {
        return $this->slugger->slug($text)->lower()->toString();
    }

    public function getDependencies(): array
    {
        // Passe ProductFixtures avant ImageFixtures
        return [
            ProductFixtures::class
        ];
    }
}

<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Type;
use Symfony\Component\String\Slugger\SluggerInterface;

class TypeFixtures extends Fixture
{
    private $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager): void
    {
        $categories = [
            'Soin du visage' => [
                'Anti-âge', 'Peau sensible', 'Soins quotidiens'
            ],
            'Soin corporel' => [
                'Nettoyant', 'Bain', 'Coffret cadeau'
            ],
            'Soin capillaire' => [
                'Shampoing et après-shampoing', 'Cuir chevelu sensible', 'Masque et coiffant'
            ],
            'Compléments alimentaires' => [
                'Peau, cheveux et ongles', 'Tisane, thé et infusion', 'Digestion et transit',
            ],
            'Santé & bien-être' => [
                'Peau et cheveux', 'Immunité', 'Protection solaire'
            ]
        ];

        foreach ($categories as $parentName => $children) {
            $parentType = new Type();
            $parentType->setTypeName($parentName);
            $parentType->setSlug($this->slugify($parentName));
            $manager->persist($parentType);
            // $this->addReference('type_' . $this->slugify($parentName), $parentType);

            foreach ($children as $childName) {
                $childType = new Type();
                $childType->setTypeName($childName);
                $childType->setParent($parentType);
                $childType->setSlug($this->slugify($childName));
                $manager->persist($childType);
                $this->addReference('type_' . $this->slugify($childName), $childType);
            }
        }

        $manager->flush();
    }

    private function slugify(string $text): string
    {
        // Utilisez le service de slugification via l'interface SluggerInterface
        return $this->slugger->slug($text)->lower()->toString();
    }
}

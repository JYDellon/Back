<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait SlugTrait
{
    #[ORM\Column(type: 'string', length: 255)]
    private $slug;
  
    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;

        
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updateSlug(): void
    {
        // Génération automatique du slug à partir du typeName
        $slug = strtolower(str_replace(' ', '-', $this->getTypeName()));

        // Mise à jour du slug dans l'entité
        $this->setSlug($slug);
    }
    
}
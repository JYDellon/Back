<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait CreatedAtTrait
{
    #[ORM\Column(type: 'datetime')]
    private ?\DateTime $createdAt = null;

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    // public function setCreatedAt(\DateTime $createdAt): self
    // {
    //     $this->createdAt = $createdAt;

    //     return $this;
    // }
    public function setCreatedAt($createdAt): self
{
    if (is_string($createdAt)) {
        $createdAt = new \DateTime($createdAt);
    }

    $this->createdAt = $createdAt;

    return $this;
}

}
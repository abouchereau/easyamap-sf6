<?php
namespace App\Entity\Traits;
 
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
 
trait IsActiveDefaultTrueTrait
{

    #[ORM\Column(name:"is_active", type: Types::BOOLEAN, nullable:false)]
    private bool $isActive = true;

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }
    

}
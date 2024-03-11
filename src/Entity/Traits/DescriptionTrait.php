<?php

 
namespace App\Entity\Traits;
 
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
 
trait DescriptionTrait
{

    #[ORM\Column(name:"description", type: Types::TEXT, nullable:true)]
    private $description;

    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }
}
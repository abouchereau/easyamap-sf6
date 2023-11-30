<?php

 
namespace App\Entity\Traits;
 
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
 
trait CreatedAtTrait
{
    #[ORM\Column(name:"created_at", type: Types::DATETIME_MUTABLE, nullable:true)]
    private ?\DateTime $createdAt = null;

    /**
     * @return \DateTime|null
     */
    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime|null $createdAt
     */
    public function setCreatedAt(?\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }



}
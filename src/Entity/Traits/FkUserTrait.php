<?php

 
namespace App\Entity\Traits;
 
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;

trait FkUserTrait
{
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[JoinColumn(name: "id_user", referencedColumnName: "id")]
    #[ORM\OrderBy(["isActive" => "DESC", "lastname" => "ASC"])]
    private $idUser;

}
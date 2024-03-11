<?php

 
namespace App\Entity\Traits;
 
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
 
trait LabelTrait
{

    #[ORM\Column(type: Types::STRING, nullable:false)]
    #[Assert\Length(max: 255)]
    private $label;

  public function setLabel($label)
  {
      $this->label = $label;

      return $this;
  }


  public function getLabel()
  {
      return $this->label;
    }
}
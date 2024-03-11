<?php

 
namespace App\Entity\Traits;
 
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
 
trait SequenceTrait
{
    #[ORM\Column(type: Types::INTEGER)]
    private $sequence;


    public function setSequence($sequence)
    {       
        $this->sequence = $sequence;

        return $this;
    }

    
    public function setSequenceAtEnd() {
        $this->sequence = 100000;
        return $this;
    }


    public function getSequence()
    {
        return $this->sequence;
    }
}
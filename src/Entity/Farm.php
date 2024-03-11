<?php

namespace App\Entity;

use App\Entity\Traits\IdTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\Traits\LabelTrait;
use App\Entity\Traits\IsActiveDefaultTrueTrait;
use App\Entity\Traits\DescriptionTrait;
use App\Entity\Traits\SequenceTrait;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: FarmRepository::class)]
#[ORM\Table(name: 'farm')]
#[ORM\UniqueConstraint(name:"label", columns:["label"])]
#[ORM\UniqueEntity("label")]
class Farm
{
    use IdTrait;
    use LabelTrait;
    use IsActiveDefaultTrueTrait;
    use DescriptionTrait;
    use SequenceTrait;

   
    #[ORM\Column(type: Types::STRING, nullable:true)]
    #[Assert\Length(max: 255)]
    private ?string $productType;

    #[ORM\Column(type: Types::STRING, nullable:true)]
    #[Assert\Length(max: 255)]
    private  ?string $checkPayableTo;

    #[ORM\Column(type: Types::STRING, nullable:true)]
    #[Assert\Length(max: 255)]
    private ?string $link;
    
    #[ORM\Column(type: Types::BOOLEAN, nullable:false)]
    private bool $equitable = false;

    #[ManyToMany(targetEntity: User::class)]
    #[JoinTable(name: "referent")]
    #[JoinColumn(name: "id_farm", referencedColumnName: "id")]
    #[InverseJoinColumn(name: "id_user", referencedColumnName: "id")]
    private Collection $referents;

    #[ORM\OneToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "id_user", referencedColumnName: "id")]
    private User $idUser;

//    #[Assert\Count(min: 1, minMessage: "Merci de choisir au moins un type de paiement")]
//    #[ManyToMany(targetEntity: PaymentType::class)]
//    #[JoinTable(name: "farm_payment_type")]
//    #[JoinColumn(name: "id_farm", referencedColumnName: "id")]
//    #[InverseJoinColumn(name: "id_payment_type", referencedColumnName: "id")]
//    private Collection $payment_types;
//
//
//    #[Assert\Count(min: 1, minMessage: "Merci de choisir au moins une fréquence de paiement")]
//    #[ManyToMany(targetEntity: PaymentFreq::class)]
//    #[JoinTable(name: "farm_payment_freq")]
//    #[JoinColumn(name: "id_farm", referencedColumnName: "id")]
//    #[InverseJoinColumn(name: "id_payment_freq", referencedColumnName: "id")]
//    private Collection $payment_freqs;
    

//    public function isCheckPaymentTypeNotEmpty() {
//        return ($this->payment_types->count() > 0);
//    }
//
//    public function isCheckPaymentFreqNotEmpty() {
//        return ($this->payment_freqs->count() > 0);
//    }

    public function getProductType(): ?string
    {
        return $this->productType;
    }

    public function setProductType(?string $productType): void
    {
        $this->productType = $productType;
    }

    public function getCheckPayableTo(): ?string
    {
        return $this->checkPayableTo;
    }

    public function setCheckPayableTo(?string $checkPayableTo): void
    {
        $this->checkPayableTo = $checkPayableTo;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): void
    {
        $this->link = $link;
    }

    public function isEquitable(): bool
    {
        return $this->equitable;
    }

    public function setEquitable(bool $equitable): void
    {
        $this->equitable = $equitable;
    }

    public function getReferents(): Collection
    {
        if (!isset($this->referents)) {
            $this->referents = new ArrayCollection();
        }
        return $this->referents;
    }

    public function setReferents(Collection $referents): void
    {
        $this->referents = $referents;
    }

    public function getIdUser(): \User
    {
        return $this->idUser;
    }

    public function setIdUser(\User $idUser): void
    {
        $this->idUser = $idUser;
    }



//    public function getPaymentTypes(): Collection
//    {
//        return $this->payment_types;
//    }
//
//    public function setPaymentTypes(Collection $payment_types): void
//    {
//        $this->payment_types = $payment_types;
//    }
//
//    public function getPaymentFreqs(): Collection
//    {
//        return $this->payment_freqs;
//    }
//
//    public function setPaymentFreqs(Collection $payment_freqs): void
//    {
//        $this->payment_freqs = $payment_freqs;
//    }

    public function __toString()
    {
      return $this->label;
    }
}

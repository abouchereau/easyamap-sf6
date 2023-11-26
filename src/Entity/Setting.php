<?php

namespace App\Entity;
use App\Repository\SettingRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'setting')]
#[ORM\Entity(repositoryClass: SettingRepository::class)]
class Setting
{

    #[ORM\Column(name: 'id', type:Types::INTEGER, nullable: false)]
    #[ORM\Id]
    private ?int $id = null;

    #[ORM\Column(name: 'use_address', type: Types::BOOLEAN, nullable: false)]
    private bool $useAddress = true;

    #[ORM\Column(name:"register_distribution", type: Types::BOOLEAN, nullable: false)]
    private bool $registerDistribution = true;

    #[ORM\Column(name:"use_report", type: Types::BOOLEAN, nullable:false)]
    private bool $useReport = false;

    #[ORM\Column(name:"name", type: Types::STRING, nullable: true)]
    #[Assert\Length(max: 255)]
    private ?string $name;

    #[ORM\Column(name:"link", type: Types::STRING, nullable: true)]
    #[Assert\Length(max: 255)]
    private ?string $link;

    #[ORM\Column(name:"logo_small_url", type: Types::STRING, nullable:true)]
    #[Assert\Length(max: 255)]
    private ?string $logoSmallUrl;

    #[ORM\Column(name:"logo_large_url", type: Types::STRING, nullable:true)]
    #[Assert\Length(max: 255)]
    private ?string $logoLargeUrl;
    

    #[ORM\Column(name:"text_register_distribution", type: Types::TEXT, nullable:true)]
    private ?string $textRegisterDistribution;


    #[ORM\Column(name:"logo_secondary", type: Types::STRING, nullable:true)]
    #[Assert\Length(max: 255)]
    private ?string $logoSecondary;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return bool
     */
    public function isUseAddress(): bool
    {
        return $this->useAddress;
    }

    /**
     * @param bool $useAddress
     */
    public function setUseAddress(bool $useAddress): void
    {
        $this->useAddress = $useAddress;
    }

    /**
     * @return bool
     */
    public function isRegisterDistribution(): bool
    {
        return $this->registerDistribution;
    }

    /**
     * @param bool $registerDistribution
     */
    public function setRegisterDistribution(bool $registerDistribution): void
    {
        $this->registerDistribution = $registerDistribution;
    }

    /**
     * @return bool
     */
    public function isUseReport(): bool
    {
        return $this->useReport;
    }

    /**
     * @param bool $useReport
     */
    public function setUseReport(bool $useReport): void
    {
        $this->useReport = $useReport;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getLink(): ?string
    {
        return $this->link;
    }

    /**
     * @param string|null $link
     */
    public function setLink(?string $link): void
    {
        $this->link = $link;
    }

    /**
     * @return string|null
     */
    public function getLogoSmallUrl(): ?string
    {
        return $this->logoSmallUrl;
    }

    /**
     * @param string|null $logoSmallUrl
     */
    public function setLogoSmallUrl(?string $logoSmallUrl): void
    {
        $this->logoSmallUrl = $logoSmallUrl;
    }

    /**
     * @return string|null
     */
    public function getLogoLargeUrl(): ?string
    {
        return $this->logoLargeUrl;
    }

    /**
     * @param string|null $logoLargeUrl
     */
    public function setLogoLargeUrl(?string $logoLargeUrl): void
    {
        $this->logoLargeUrl = $logoLargeUrl;
    }

    /**
     * @return string|null
     */
    public function getTextRegisterDistribution(): ?string
    {
        return $this->textRegisterDistribution;
    }

    /**
     * @param string|null $textRegisterDistribution
     */
    public function setTextRegisterDistribution(?string $textRegisterDistribution): void
    {
        $this->textRegisterDistribution = $textRegisterDistribution;
    }

    /**
     * @return string|null
     */
    public function getLogoSecondary(): ?string
    {
        return $this->logoSecondary;
    }

    /**
     * @param string|null $logoSecondary
     */
    public function setLogoSecondary(?string $logoSecondary): void
    {
        $this->logoSecondary = $logoSecondary;
    }


    
    public function toArray() {
        return array(
            'useAddress' => $this->useAddress,
            'registerDistribution' => $this->registerDistribution,
            'useReport' => $this->useReport,
            'name' => $this->name,
            'link' => $this->link,
            'logoSmallUrl' => $this->logoSmallUrl,
            'logoLargeUrl' => $this->logoLargeUrl,
            'logoSecondary' => $this->logoSecondary,
            'textRegisterDistribution' => $this->textRegisterDistribution
            //'cotisation' => $this->cotisation
        );
    }
    
}
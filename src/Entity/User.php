<?php

namespace App\Entity;

use App\Entity\Traits\CreatedAtTrait;
use App\Entity\Traits\IdTrait;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'user')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{

    use IdTrait;
    use CreatedAtTrait;

    final public const ROLE_USER = 'ROLE_USER';
    final public const ROLE_ADMIN = 'ROLE_ADMIN';
    final public const ROLE_ADHERENT = 'ROLE_ADHERENT';
    final public const ROLE_REFERENT = 'ROLE_REFERENT';
    final public const ROLE_FARMER = 'ROLE_FARMER';

    #[ORM\Column(type: Types::STRING)]
    #[Assert\NotBlank]
    private ?string $firstname = null;

    #[ORM\Column(type: Types::STRING)]
    #[Assert\NotBlank]
    private ?string $lastname = null;

    #[ORM\Column(type: Types::STRING, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 50)]
    private ?string $username = null;

    #[ORM\Column(type: Types::STRING, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Email]
    private ?string $email = null;

    #[ORM\Column(type: Types::STRING)]
    private ?string $password = null;

    #[ORM\Column(type: Types::JSON)]
    private array $roles = [];

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable:true)]
    private ?\DateTime $lastConnection = null;

    #[ORM\Column(name:"tel1", type: Types::STRING, nullable:true)]
    #[Assert\Length(max: 255)]
    private ?string $tel1;

    #[ORM\Column(name:"tel2", type: Types::STRING, nullable:true)]
    #[Assert\Length(max: 255)]
    private ?string $tel2;

    #[ORM\Column( type: Types::STRING, nullable:true)]
    #[Assert\Length(max: 255)]
    private ?string $address;

    #[ORM\Column(type: Types::STRING, nullable:true)]
    #[Assert\Length(max: 5, maxMessage: "Le code postal doit faire 5 caractères")]
    private ?string $zipcode;

    #[ORM\Column(type: Types::STRING, nullable:true)]
    #[Assert\Length(max: 255)]
    private ?string $town;

    #[ORM\Column(type: Types::STRING, length: 100)]
    private ?string $resetToken;

    #[ManyToMany(targetEntity: Farm::class)]
    #[JoinTable(name: "referent")]
    #[JoinColumn(name: "id_farm", referencedColumnName: "id")]
    #[InverseJoinColumn(name: "id_user", referencedColumnName: "id")]
    private Collection $farms;

    #[ORM\PrePersist]
    public function onPrePersist()
    {
        $this->setCreatedAt(new \DateTime());
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    public function getUsername(): string
    {
        return $this->getUserIdentifier();
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    public function hasRole($role):bool {
        $roles = $this->getRoles();
        return in_array($role,$roles);
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): void
    {
        $this->firstname = $firstname;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): void
    {
        $this->lastname = $lastname;
    }

    public function setFullName(?string $lastname): void
    {
        $this->lastname = $lastname;
    }


    public function getSalt(): ?string
    {
        // We're using bcrypt in security.yaml to encode the password, so
        // the salt value is built-in and you don't have to generate one
        // See https://en.wikipedia.org/wiki/Bcrypt

        return null;
    }

    /**
     * Removes sensitive data from the user.
     *
     * {@inheritdoc}
     */
    public function eraseCredentials(): void
    {
        // if you had a plainPassword property, you'd nullify it here
        // $this->plainPassword = null;
    }

    /**
     * @return array{int|null, string|null, string|null}
     */
    public function __serialize(): array
    {
        // add $this->salt too if you don't use Bcrypt or Argon2i
        return [$this->id, $this->username, $this->password];
    }

    /**
     * @param array{int|null, string, string} $data
     */
    public function __unserialize(array $data): void
    {
        // add $this->salt too if you don't use Bcrypt or Argon2i
        [$this->id, $this->username, $this->password] = $data;
    }

    public function getLastConnection(): ?\DateTime
    {
        return $this->lastConnection;
    }


    public function setLastConnection(?\DateTime $lastConnection): void
    {
        $this->lastConnection = $lastConnection;
    }

    public function getTel1(): ?string
    {
        return $this->tel1;
    }


    public function setTel1(?string $tel1): void
    {
        $this->tel1 = $tel1;
    }


    public function getTel2(): ?string
    {
        return $this->tel2;
    }


    public function setTel2(?string $tel2): void
    {
        $this->tel2 = $tel2;
    }


    public function getAddress(): ?string
    {
        return $this->address;
    }


    public function setAddress(?string $address): void
    {
        $this->address = $address;
    }


    public function getZipcode()
    {
        return $this->zipcode;
    }


    public function setZipcode($zipcode): void
    {
        $this->zipcode = $zipcode;
    }


    public function getTown()
    {
        return $this->town;
    }


    public function setTown($town): void
    {
        $this->town = $town;
    }

    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    public function setResetToken(?string $resetToken): self
    {
        $this->resetToken = $resetToken;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->hasRole(User::ROLE_USER);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(User::ROLE_ADMIN);
    }

    public function isFarmer(): bool
    {
        return $this->hasRole(User::ROLE_FARMER);
    }

    public function isReferent(): bool
    {
        return $this->hasRole(User::ROLE_REFERENT);
    }

    public function isAdherent(): bool
    {
        return $this->hasRole(User::ROLE_ADHERENT);
    }

    public function getFarms(): Collection
    {
        if(!isset($this->farms)) {
            $this->farms = new ArrayCollection();
        }
        return $this->farms;
    }
    public function setFarms(Collection $farms): void
    {
        $this->farms = $farms;
    }


    public function __toString()
    {
        return $this->lastname.' '.$this->firstname;
    }

}

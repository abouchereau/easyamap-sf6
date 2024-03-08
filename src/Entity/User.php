<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity;

use App\Entity\Traits\CreatedAtTrait;
use App\Entity\Traits\IsActiveDefaultTrueTrait;
use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator as CustomAssert;
use Symfony\Component\Validator\Constraints\PasswordStrength;

/**
 * Defines the properties of the User entity to represent the application users.
 * See https://symfony.com/doc/current/doctrine.html#creating-an-entity-class.
 *
 * Tip: if you have an existing database, you can generate these entity class automatically.
 * See https://symfony.com/doc/current/doctrine/reverse_engineering.html
 *
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'user')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{

    use IsActiveDefaultTrueTrait;
    use CreatedAtTrait;

    // We can use constants for roles to find usages all over the application rather
    // than doing a full-text search on the "ROLE_" string.
    // It also prevents from making typo errors.
    final public const ROLE_USER = 'ROLE_USER';
    final public const ROLE_ADMIN = 'ROLE_ADMIN';
    final public const ROLE_ADHERENT = 'ROLE_ADHERENT';
    final public const ROLE_REFERENT = 'ROLE_REFERENT';
    final public const ROLE_FARMER = 'ROLE_FARMER';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

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
    #[Assert\Email]
    private ?string $email = null;

    #[ORM\Column(type: Types::STRING)]
    private ?string $password = null;

    /**
     * @var string[]
     */
    #[ORM\Column(type: Types::JSON)]
    private array $roles = [];

    #[ORM\Column(name:"is_admin", type: Types::BOOLEAN, nullable:false)]
    private bool $isAdmin;


    #[ORM\Column(name:"is_adherent", type: Types::BOOLEAN, nullable:false)]
    private bool $isAdherent;




    #[ORM\Column(name:"last_connection", type: Types::DATETIME_MUTABLE, nullable:true)]
    private ?\DateTime $lastConnection = null;

    #[ORM\Column(name:"tel1", type: Types::STRING, nullable:true)]
    #[Assert\Length(max: 255)]
    private ?string $tel1;

    #[ORM\Column(name:"tel2", type: Types::STRING, nullable:true)]
    #[Assert\Length(max: 255)]
    private ?string $tel2;

    #[ORM\Column(name:"address", type: Types::STRING, nullable:true)]
    #[Assert\Length(max: 255)]
    private ?string $address;

    #[ORM\Column(name:"zipcode", type: Types::STRING, nullable:true)]
    #[Assert\Length(max: 5, maxMessage: "Le code postal doit faire 5 caractères")]
    private $zipcode;

    #[ORM\Column(name:"town", type: Types::STRING, nullable:true)]
    #[Assert\Length(max: 255)]
    private $town;

    #[ORM\Column(type: 'string', length: 100)]
    private $resetToken;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Farm")
     * @JoinTable(name="referent",
     *      joinColumns={@JoinColumn(name="id_user", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="id_farm", referencedColumnName="id")}
     *      )
     **/
    private Collection $farms;

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * Returns the roles or permissions granted to the user for security.
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param string[] $roles
     */
    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    /**
     * @param $role
     * @return bool
     */
    public function hasRole($role):bool {
        $roles = $this->getRoles();
        return in_array($role,$roles);
    }

    /**
     * @return string|null
     */
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    /**
     * @param string|null $firstname
     */
    public function setFirstname(?string $firstname): void
    {
        $this->firstname = $firstname;
    }

    /**
     * @return string|null
     */
    public function getLastname(): ?string
    {
        return $this->lastname;
    }


    /**
     * @param string|null $lastname
     */
    public function setLastname(?string $lastname): void
    {
        $this->lastname = $lastname;
    }

    /**
     * @param string|null $lastname
     */
    public function setFullName(?string $lastname): void
    {
        $this->lastname = $lastname;
    }




    /**
     * Returns the salt that was originally used to encode the password.
     *
     * {@inheritdoc}
     */
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

    /**
     * @return bool
     */
    public function getIsAdmin(): bool
    {
        return $this->isAdmin;
    }

    /**
     * @param bool $isAdmin
     */
    public function setIsAdmin(bool $isAdmin): void
    {
        $this->isAdmin = $isAdmin;
    }

    /**
     * @return bool
     */
    public function getIsAdherent(): bool
    {
        return $this->isAdherent;
    }

    /**
     * @param bool $isAdherent
     */
    public function setIsAdherent(bool $isAdherent): void
    {
        $this->isAdherent = $isAdherent;
    }

    /**
     * @return \DateTime|null
     */
    public function getLastConnection(): ?\DateTime
    {
        return $this->lastConnection;
    }

    /**
     * @param \DateTime|null $lastConnection
     */
    public function setLastConnection(?\DateTime $lastConnection): void
    {
        $this->lastConnection = $lastConnection;
    }

    /**
     * @return string|null
     */
    public function getTel1(): ?string
    {
        return $this->tel1;
    }

    /**
     * @param string|null $tel1
     */
    public function setTel1(?string $tel1): void
    {
        $this->tel1 = $tel1;
    }

    /**
     * @return string|null
     */
    public function getTel2(): ?string
    {
        return $this->tel2;
    }

    /**
     * @param string|null $tel2
     */
    public function setTel2(?string $tel2): void
    {
        $this->tel2 = $tel2;
    }

    /**
     * @return string|null
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @param string|null $address
     */
    public function setAddress(?string $address): void
    {
        $this->address = $address;
    }

    /**
     * @return mixed
     */
    public function getZipcode()
    {
        return $this->zipcode;
    }

    /**
     * @param mixed $zipcode
     */
    public function setZipcode($zipcode): void
    {
        $this->zipcode = $zipcode;
    }

    /**
     * @return mixed
     */
    public function getTown()
    {
        return $this->town;
    }

    /**
     * @param mixed $town
     */
    public function setTown($town): void
    {
        $this->town = $town;
    }

// ...

    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    public function setResetToken(?string $resetToken): self
    {
        $this->resetToken = $resetToken;

        return $this;
    }

    public function __toString()
    {
        return $this->lastname.' '.$this->firstname;
    }

}

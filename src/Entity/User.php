<?php

namespace App\Entity;

use App\Dto\Response\UserResponseDto;
use App\Repository\UserRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_USERNAME', fields: ['userName'])]
class User extends BaseEntity implements PasswordAuthenticatedUserInterface, UserInterface
{

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    #[ORM\Column(length: 60)]
    private ?string $password = null;

    #[ORM\Column(length: 50)]
    private ?string $firstName = null;

    #[ORM\Column(length: 50)]
    private ?string $lastName = null;

    #[ORM\Column(length: 14)]
    private ?string $cnpjCpfRg = null;

    #[ORM\Column(nullable: false)]
    private ?bool $isLegalEntity  = false;

    #[ORM\Column(length: 50)]
    private ?string $userName = null;

    #[ORM\ManyToMany(targetEntity: "Role", inversedBy: "users", cascade: ["persist"])]
    #[ORM\JoinTable(name: "user_roles")]
    private Collection  $roles;

    #[ORM\Column(length: 255)]
    private ?string $twoFactorToken = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $twoFactorExpiresAt = null;

    #[ORM\Column(nullable: false)]
    private ?bool $isTwoFactorEnabled = false;


    public function __construct(
        string $email,
        string $password,
        string $fisrtsName,
        string $lastName,
        string $userName,
        string $cnpjCpfRg,
    ) {
        parent::__construct();
        $this->email = $this->validateEmail($email);
        $this->password = $this->hashPassword($password);
        $this->firstName = $fisrtsName;
        $this->lastName = $lastName;
        $this->userName = $userName;
        $this->cnpjCpfRg = $this->validateCnpjCpf($cnpjCpfRg);
        $this->roles = new ArrayCollection();
    }


    public function mapUserDto(): UserResponseDto
    {
        $userDto = new UserResponseDto();
        $userDto->email = $this->email;
        $userDto->firstName = $this->firstName;
        $userDto->lastName = $this->lastName;
        $userDto->userName = $this->userName;
        $userDto->cnpjCpfRg =$this->cnpjCpfRg;
        $userDto->token = $this->twoFactorToken;
        return $userDto;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function isEmail(string $email): string
    {
        $this->validateEmail($email);
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function isFirstName(string $firstName): string
    {
        return $this->firstName === $firstName;
    }

    public function isLastName(string $lastName): string
    {
        return $this->lastName === $lastName;
    }

    public function isUserName(string $userName): string
    {
        return $this->userName === $userName;
    }

    public function isCnpjCpf(string $cnpjCpf): string
    {
        return $this->cnpjCpfRg === $cnpjCpf;
    }

    private function validateEmail(string $email): string
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Email inválido.");
        }
        return $email;
    }

    private function hashPassword(string $password): string
    {
        if (strlen($password) < 8) {
            throw new \InvalidArgumentException("A senha deve ter pelo menos 8 caracteres.");
        }

        return password_hash($password, PASSWORD_BCRYPT);
    }

    private function validateCnpjCpf(string $cnpjCpf): string
    {
        if ($cnpjCpf == null) {
            throw new \InvalidArgumentException("O campo 'CNPJ/CPF' não pode estar vazio.");
        } else if ($cnpjCpf >= 11) {
        }
        return $cnpjCpf;
    }

    public function updatePassword(string $currentPassowrd, string $newPassword): void
    {
        if (!password_verify($currentPassowrd, $this->password)) {
            throw new \InvalidArgumentException("Senha atual incorreta.");
        }

        $this->password = $this->hashPassword($newPassword);
        $this->dateCreated = new \DateTime();
    }

    public function updateEmail(string $newEmail): void
    {
        $this->email = $this->validateEmail($newEmail);
        $this->dateUpdated = new \DateTime();
    }

    public function isLegalentity(): bool
    {
        return $this->isLegalentity ?? false;
    }

    public function addRole(Role $role): void
    {
        if (!$this->roles->contains($role)) {
            $this->roles[] = $role;
        }
    }

    public function getRoleNames(): array
    {
        return $this->roles->map(fn(Role $role) => $role->getName())->toArray();
    }

    public function authenticate(string $inputPassword): bool
    {
        return password_verify($inputPassword, $this->password);
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getfullName(): string
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    public function getRoles(): array
    {
        return $this->roles->toArray();
    }

    public function eraseCredentials(): void
    {
        // Apaga dados sensíveis temporários
    }

    public function getTwoFactorToken(): ?string
    {
        return $this->twoFactorToken;
    }

    public function setTwoFactorToken(string $twoFactorToken): static
    {
        $this->twoFactorToken = $twoFactorToken;

        return $this;
    }

    public function getTwoFactorExpiresAt(\DateTimeImmutable $dateTime ): ?\DateTimeInterface
    {
        return $this->twoFactorExpiresAt = $dateTime;
    }

    public function verifyTwoFactorExpiresAt(\DateTimeImmutable $dateTime): bool
    {
        if($dateTime > $this->twoFactorExpiresAt)
        {
           return true;
        }
        return false;
    }

    public function isTwoFactorEnabled(): bool
    {
        return $this->isTwoFactorEnabled;
    }
    public function setTwoFactorEnabled(bool $isTwoFactorEnabled): static
    {
        $this->isTwoFactorEnabled = $isTwoFactorEnabled;

        return $this;
    }
}

<?php

namespace App\Entity;

use App\Dto\Response\UserResponseDto;
use App\Repository\UserRepository;
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

    #[ORM\JoinTable(name: "user_roles")]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id")]
    #[ORM\InverseJoinColumn(name: "role_id", referencedColumnName: "id")]
    #[ORM\ManyToMany(targetEntity: Role::class, inversedBy: 'users', cascade: ['persist'])]
    private Collection $roles;

    #[ORM\Column(length: 255)]
    private ?string $twoFactorToken = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $twoFactorExpiresAt = null;

    #[ORM\Column(nullable: false)]
    private ?bool $isTwoFactorEnabled = false;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $resetPasswordToken = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $resetPasswordTokenExpiresAt = null;

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

    public function getEmail(): string 
    {
        return $this->validateEmail($this->email);
    }
    
    public function setEmail(string $email): static 
    {
        $this->email = $email;

        return $this;
    }

    public function updateEmail(string $newEmail): void
    {
        $this->email = $this->validateEmail($newEmail);
        $this->dateUpdated = new \DateTime();
    }

    private function validateEmail(string $email): string
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Email inválido.");
        }
        return $email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function updatePassword(string $currentPassword, string $newPassword): void
    {
        if (!password_verify($currentPassword, $this->password)) {
            throw new \InvalidArgumentException("Senha atual incorreta.");
        }

        $this->password = $this->hashPassword($newPassword);
        $this->dateCreated = new \DateTime();
    }

    public function resetPassword(string $newPassword): void
    {
        $this->password = $this->hashPassword($newPassword);
        $this->dateCreated = new \DateTime();
    }

    private function hashPassword(string $password): string
    {
        if (strlen($password) < 8) {
            throw new \InvalidArgumentException("A senha deve ter pelo menos 8 caracteres.");
        }

        return password_hash($password, PASSWORD_BCRYPT);
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstname(string $firstName): static
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastname(string $lastName): static
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getUserName(): string
    {
        return $this->userName;
    }

    public function setUserName(string $userName): static
    {
        $this->userName = $userName;

        return $this;
    }

    public function getFullName(): string
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    public function getCnpjCpfRg(): string
    {
        return $this->cnpjCpfRg;
    }

    public function setCnpjCpf(string $cnpjCpf): static
    {
        $this->cnpjCpfRg = $cnpjCpf;

        return $this;
    }

    private function validateCnpjCpf(string $cnpjCpf): string
    {
        if (empty($cnpjCpf)) {
            throw new \InvalidArgumentException("O campo 'CNPJ/CPF' não pode estar vazio.");
        }
        return $cnpjCpf;
    }

    public function addRole(Role $role): static
    {
        if (!$this->roles->contains($role)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles->toArray();
    }

    public function setRolesName(): array
    {
        return $this->roles->map(fn(Role $role) => $role->getName())->toArray();
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

    public function getTwoFactorExpiresAt(): ?\DateTimeInterface
    {
        return $this->twoFactorExpiresAt;
    }

    public function setTwoFactorExpiresAt(\DateTimeImmutable $twoFactorExpiresAt): static
    {
        $this->twoFactorExpiresAt = $twoFactorExpiresAt;

        return $this;
    }

    public function verifyTwoFactorExpiresAt(\DateTimeImmutable $dateTime): bool
    {
        return $dateTime >= $this->twoFactorExpiresAt;
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

    /* Métodos relacionados a reset de senha */
    public function getResetPasswordToken(): ?string
    {
        return $this->resetPasswordToken;
    }

    public function setResetPasswordToken(?string $resetPasswordToken): static
    {
        $this->resetPasswordToken = $resetPasswordToken;

        return $this;
    }

    public function verifyResetPasswordToken(string $twoFactorToken): bool
    {
        return $this->resetPasswordToken === $twoFactorToken;
    }

    public function getResetPasswordTokenExpiresAt(): ?\DateTimeImmutable
    {
        return $this->resetPasswordTokenExpiresAt;
    }

    public function setResetPasswordTokenExpiresAt(?\DateTimeImmutable $resetPasswordTokenExpiresAt): static
    {
        $this->resetPasswordTokenExpiresAt = $resetPasswordTokenExpiresAt;

        return $this;
    }

    public function verifyResetPasswordTokenExpiresAt(\DateTimeImmutable $dateTime): bool
    {
        return $dateTime >= $this->resetPasswordTokenExpiresAt;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function authenticate(string $inputPassword): bool
    {   
        return password_verify($inputPassword, $this->password);
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getLegalRegister(): bool
    {
        return $this->isLegalEntity ?? false;
    }

    public function eraseCredentials(): void
    {
        // Apaga dados sensíveis temporários
    }
}

<?php

namespace App\Entity;

use App\Repository\LoginRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LoginRepository::class)]
class Login extends BaseEntity
{

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $email_userName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $passwordHash = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTime $lastLoginAttempt = null;

    #[ORM\Column(length: 255)]
    private ?string $lastLoginIp = null;

    public function __construct(string $email, string $passwordHash, string $lastLoginIp) 
    {
        parent::__construct();
        $this->email_userName = $this->validateEmail($email);
        $this->passwordHash = $this->validatePasswordHash($passwordHash);
        $this->lastLoginIp = $this->validateLastLoginIp($lastLoginIp);
        $this->lastLoginAttempt = new \DateTime();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function validateUserName(string $userName): ?string 
    {
        if($userName == null)
        {
            throw new \InvalidArgumentException("O campo 'userName' não pode estar vazio.");
        }

        return $userName;
    }

    private function validateEmail(string $email_userName): ?string
    {
        return $email_userName;
    }

    private function validatePasswordHash(string $passwordHash): string
    {
        $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/';
        if(preg_match($pattern, $passwordHash))
        {
            throw new \InvalidArgumentException("Senha inválida.");
        }

        return $passwordHash;
    }

    private function validateLastLoginIp(string $lastLoginIp): ?string
    {
        if(filter_var($lastLoginIp, FILTER_VALIDATE_IP) == null)
        {
            throw new \InvalidArgumentException("Usuário não autorizado.");
        }

        return $lastLoginIp;
    }

    public function getEmailUserName(): ?string
    {
        return $this->email_userName;
    }

    public function setEmail(?string $email): static
    {
        $this->email_userName = $email;

        return $this;
    }

    public function getPasswordHash(): ?string
    {
        return $this->passwordHash;
    }

    public function setPasswordHash(?string $passwordHash): static
    {
        $this->passwordHash = $passwordHash;

        return $this;
    }

    public function getLastLoginAttempt(): ?\DateTimeInterface
    {
        return $this->lastLoginAttempt;
    }

    public function setLastLoginAttempt(\DateTimeInterface $lastLoginAttempt): static
    {
        $this->lastLoginAttempt = $lastLoginAttempt;

        return $this;
    }

    public function getLastLoginIp(): ?string
    {
        return $this->lastLoginIp;
    }

    public function setLastLoginIp(string $lastLoginIp): static
    {
        $this->lastLoginIp = $lastLoginIp;

        return $this;
    }
}

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

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTime $lastLoginAttempt = null;

    #[ORM\Column(length: 255)]
    private ?string $lastLoginIp = null;

    #[ORM\Column]
    private ?bool $remember = false;

    public function __construct(string $email, string $passwordHash, string $lastLoginIp) 
    {
        parent::__construct();
        $this->email_userName = $this->validateEmail($email);
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

    public function getLastLoginAttempt(): ?\DateTimeInterface
    {
        return $this->lastLoginAttempt;
    }

    public function setLastLoginAttempt(\DateTimeInterface $lastLoginAttempt): static
    {
        $currentTime = new \DateTime('now');
        
        if (!$lastLoginAttempt) {
            throw new \InvalidArgumentException('O parâmetro $lastLoginAttempt não pode ser nulo.');
        }
    
        $interval = $currentTime->diff($lastLoginAttempt);
    
        if ($interval->i > 0 || $interval->h > 0 || $interval->d > 0) {
            $this->lastLoginAttempt = $currentTime;
        }
    
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

    public function isRemember(): ?bool
    {
        return $this->remember;
    }

    public function setRemember(bool $remember): static
    {
        $this->remember = $remember;

        return $this;
    }
}

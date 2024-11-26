<?php

namespace App\Dto\Create;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

class UserCreateDto
{
    #[Assert\NotBlank(message: "O email não pode estar vazio.")]
    #[Assert\Email(message: "O email '{{ value }}' não é um endereço de email válido.")]
    #[Assert\Length(max: 180, maxMessage: "O email não pode ter mais de {{ limit }} caracteres.")]
    public string $email;

    #[Assert\NotBlank(message: "A senha não pode estar vazia.")]
    #[Assert\Length(min: 8, max: 20, minMessage: "A senha deve ter pelo menos {{ limit }} caracteres.", maxMessage: "A senha não pode ter mais de {{ limit }} caracteres.")]
    public string $password;

    #[Assert\NotBlank(message: "O primeiro nome não pode estar vazio.")]
    #[Assert\Length(max: 50, maxMessage: "O primeiro nome não pode ter mais de {{ limit }} caracteres.")]
    public string $firstName;

    #[Assert\NotBlank(message: "O sobrenome não pode estar vazio.")]
    #[Assert\Length(max: 50, maxMessage: "O sobrenome não pode ter mais de {{ limit }} caracteres.")]
    public string $lastName;

    #[Assert\NotBlank(message: "O CNPJ/CPF/RG não pode estar vazio.")] 
    #[Assert\Length( max: 14, maxMessage: "O CNPJ/CPF/RG não pode ter mais de {{ limit }} caracteres." )]
    public string $cnpjCpfRg;

    public bool $legalRegister;

    #[Assert\NotBlank(message: "A senha não pode estar vazia.")]
    #[Assert\Length(min: 8, max: 50, minMessage: "A senha deve ter pelo menos {{ limit }} caracteres.", maxMessage: "A senha não pode ter mais de {{ limit }} caracteres.")]
    public string $userName;
    
    public ?array $roles = [];

    public string $token;
}

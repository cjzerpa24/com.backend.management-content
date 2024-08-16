<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class RegisterUserDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        public string $email,
        #[Assert\NotBlank]
        #[Assert\Length(min: 8, max: 20)]
        public string $password,
        #[Assert\NotBlank]
        #[Assert\EqualTo(propertyPath: 'password')]
        public string $confirmPassword,
    ) {}
}

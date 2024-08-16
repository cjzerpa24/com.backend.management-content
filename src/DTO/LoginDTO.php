<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class LoginDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        public string $email,
        #[Assert\NotBlank]
        #[Assert\Length(min: 8, max: 20)]
        public string $password
    ) {
    }
}

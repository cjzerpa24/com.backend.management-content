<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateUserDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        public string $email,
        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        public string $name,
    ) {}
}
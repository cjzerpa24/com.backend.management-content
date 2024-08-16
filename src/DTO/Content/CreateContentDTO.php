<?php

namespace App\DTO\Content;

use Symfony\Component\Validator\Constraints as Assert;

class CreateContentDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        public string $title,
        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        public string $description,
    ) {}
}

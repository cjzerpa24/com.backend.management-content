<?php

namespace App\DTO\Content;

use Symfony\Component\Validator\Constraints as Assert;

class RateContentDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Range(min: 0.00, max: 5.00)]
        public float $rate,
    ) {}
}

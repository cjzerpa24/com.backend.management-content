<?php

namespace App\DTO;

class GetProfileDTO
{
    public function __construct(
        public string $email,
        public string $name,
        public array $roles
    ) {
    }
}
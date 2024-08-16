<?php

namespace App\DTO\User;

class GetProfileDTO
{
    public function __construct(
        public string $email,
        public string $name,
        public array $roles
    ) {
    }
}
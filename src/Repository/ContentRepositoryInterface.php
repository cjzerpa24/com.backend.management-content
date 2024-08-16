<?php

namespace App\Repository;

use App\Entity\Content;

interface ContentRepositoryInterface
{
    public function findById(int $id): ?Content;

    public function findByCriteria(array $criteria, ?array $operator = null): array;

    public function save(Content $content): void;

    public function update(Content $content): void;

    public function remove(Content $content): void;
}
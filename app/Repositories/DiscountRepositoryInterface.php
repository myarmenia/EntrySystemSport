<?php

namespace App\Repositories;

use App\Models\Discount;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface DiscountRepositoryInterface
{
    public function paginateForUser(int $userId, array $roles, int $perPage = 10): LengthAwarePaginator;

    public function findForUserOrFail(int $id, int $userId, array $roles): Discount;

    public function createForUser(array $data, array $packageIds, int $userId, array $roles): Discount;

    public function updateForUser(Discount $discount, array $data, array $packageIds, int $userId, array $roles): Discount;

    public function deleteForUser(Discount $discount, int $userId, array $roles): void;
}

<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Package;
use App\Repositories\DiscountRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\Discount;
use Illuminate\Support\Collection;


class DiscountService
{
    public function __construct(
        private readonly DiscountRepositoryInterface $discountRepo
    ) {}

    private function userRoles(): array
    {
        // Spatie getRoleNames()
        return auth()->user()?->getRoleNames()?->toArray() ?? [];
    }

    public function list(int $perPage = 10): LengthAwarePaginator
    {
        return $this->discountRepo->paginateForUser(auth()->id(), $this->userRoles(), $perPage);
    }

    public function findOrFail(int $id): Discount
    {
        return $this->discountRepo->findForUserOrFail($id, auth()->id(), $this->userRoles());
    }

    public function create(array $data, array $packageIds): Discount
    {
        return $this->discountRepo->createForUser($data, $packageIds, auth()->id(), $this->userRoles());
    }

    public function update(Discount $discount, array $data, array $packageIds): Discount
    {
        return $this->discountRepo->updateForUser($discount, $data, $packageIds, auth()->id(), $this->userRoles());
    }

    public function delete(Discount $discount): void
    {
        $this->discountRepo->deleteForUser($discount, auth()->id(), $this->userRoles());
    }

    /**
     * Packages list for create/edit form (սահմանափակ user-ի համար՝ միայն իր client-ի package-ները)
     */
    public function packagesForSelect(): Collection
    {
        $user = auth()->user();
        $roles = $this->userRoles();
        $limited = ['client_admin', 'client_admin_rfID', 'client_sport'];

        $q = Package::query()
            ->where('is_active', 1)
            ->orderByDesc('id');

        if (count(array_intersect($roles, $limited)) > 0) {
            $clientId = Client::where('user_id', $user->id)->value('id');
            if (!$clientId) return collect();
            $q->where('client_id', $clientId);
        }

        return $q->get(); // ✅ object-ներ
    }
}

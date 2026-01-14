<?php

namespace App\Repositories\Interfaces;

use App\Models\Client;
use App\Models\Discount;
use App\Repositories\DiscountRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class DiscountRepository implements DiscountRepositoryInterface
{
    private function getClientIdForUserOrNull(int $userId, array $roles): ?int
    {
        $limitedRoles = ['client_admin','client_admin_rfID','client_sport'];

        $isLimited = count(array_intersect($roles, $limitedRoles)) > 0;

        if (!$isLimited) return null;

        return Client::where('user_id', $userId)->value('id');
    }

    private function baseQueryForUser(int $userId, array $roles): Builder
    {
        $query = Discount::query()->with('packages');

        $clientId = $this->getClientIdForUserOrNull($userId, $roles);

        if ($clientId !== null) {
            // եթե client չունի → դատարկ set
            if (!$clientId) return $query->whereRaw('1=0');

            $query->where('client_id', $clientId);
        }

        return $query;
    }

    public function paginateForUser(int $userId, array $roles, int $perPage = 10): LengthAwarePaginator
    {
        return $this->baseQueryForUser($userId, $roles)
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findForUserOrFail(int $id, int $userId, array $roles): Discount
    {
        return $this->baseQueryForUser($userId, $roles)->findOrFail($id);
    }

    public function createForUser(array $data, array $packageIds, int $userId, array $roles): Discount
    {
        $clientId = $this->getClientIdForUserOrNull($userId, $roles);

        // Եթե role-ը սահմանափակ է՝ client_id պարտադիր է
        if ($clientId !== null) {
            if (!$clientId) abort(403, 'Client not found for this user.');
            $data['client_id'] = $clientId;
        }

        return DB::transaction(function () use ($data, $packageIds) {
            /** @var Discount $discount */
            $discount = Discount::create($data);
            $discount->packages()->sync($packageIds);
            return $discount->load('packages');
        });
    }

    public function updateForUser(Discount $discount, array $data, array $packageIds, int $userId, array $roles): Discount
    {
        // owner check՝ repository query-ն արդեն user-ի scope-ով էր գալիս, բայց կրկնակի պաշտպանիչ
        $clientId = $this->getClientIdForUserOrNull($userId, $roles);
        if ($clientId !== null && (int)$discount->client_id !== (int)$clientId) {
            abort(403, 'Forbidden');
        }

        // client_id-ն update չենք թողնում սահմանափակ user-երի համար
        unset($data['client_id']);

        return DB::transaction(function () use ($discount, $data, $packageIds) {
            $discount->update($data);
            $discount->packages()->sync($packageIds);
            return $discount->load('packages');
        });
    }

    public function deleteForUser(Discount $discount, int $userId, array $roles): void
    {
        $clientId = $this->getClientIdForUserOrNull($userId, $roles);
        if ($clientId !== null && (int)$discount->client_id !== (int)$clientId) {
            abort(403, 'Forbidden');
        }

        $discount->delete(); // soft delete
    }
}

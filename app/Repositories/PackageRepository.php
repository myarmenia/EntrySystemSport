<?php

namespace App\Repositories;

use App\Models\Client;
use App\Models\Package;
use App\Repositories\Interfaces\PackageRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;


class PackageRepository implements PackageRepositoryInterface
{

    public function index()
    {
        $now = Carbon::now();

        $q = Package::query()
            ->orderBy('months')
            ->with(['discounts' => function ($d) use ($now) {
                $d->where('starts_at', '<=', $now)
                    ->where('ends_at', '>=', $now)
                    ->orderByDesc('id');
            }]);

        if (auth()->user()->hasRole(['client_admin', 'client_admin_rfID', 'manager', 'client_sport'])) {
            $client = Client::where('user_id', auth()->id())->first();
            $q->where('client_id', $client?->id);
        }
        return $q->paginate(10)->withQueryString();
    }
    //public function index()
    //{
    //    $q = Package::query()->orderBy('months');
    //
    //    if (auth()->user()->hasRole(['client_admin', 'client_admin_rfID', 'manager','client_sport'])) {
    //        $client = Client::where('user_id', auth()->id())->first();
    //        $q->where('client_id', $client?->id);
    //    }
    //
    //    return $q->paginate(10)->withQueryString();
    //}
    public function getPackagesByDiscount()
    {
        $now = Carbon::now();

        $query = Package::query()
            ->where('is_active', 1)
            ->with(['discounts' => function ($q) use ($now) {
                $q->where('status', 1)
                    ->where('starts_at', '<=', $now)
                    ->where('ends_at', '>=', $now)
                    ->orderByDesc('id');
            }])
            ->orderByDesc('id');

        // ✅ եթե client_admin / manager է → միայն իր client-ի փաթեթները
        if (Auth::user()->hasRole(['client_admin', 'client_admin_rfID', 'manager', 'client_sport'])) {
            $clientId = Client::where('user_id', Auth::id())->value('id');

            if ($clientId) {
                $query->where('client_id', $clientId);
            } else {
                $query->whereRaw('1=0'); // fallback
            }
        }

        $packages = $query->get();

        // ✅ հաշվարկ (DB query չի անում)
        foreach ($packages as $p) {
            $discount = $p->discounts->first();

            $p->is_discounted = (bool) $discount;
            $p->discounted_price_amd = (float) $p->price_amd;

            if ($discount) {
                $price = (float) $p->price_amd;
                $value = (float) $discount->value;

                if ($discount->type === 'percent') {
                    $p->discounted_price_amd = max(0, $price - ($price * $value / 100));
                } elseif ($discount->type === 'amount') {
                    $p->discounted_price_amd = max(0, $price - $value);
                }
            }
        }

        return $packages;
    }

    public function store(array $data)
    {
        if (Auth::user()->hasRole(['client_admin', 'client_admin_rfID', 'manager', 'client_sport'])) {
            $client = Client::where('user_id', Auth::id())->first();

            if ($client) {
                $data['client_id'] = $client->id;
            }
        }

        return Package::create($data);
    }

    public function getPackagesList()
    {
        return Package::orderBy('months')->get();
    }

    public function getActivePackages()
    {
        $query = Package::where('is_active', 1)
            ->orderBy('months');

        // եթե client_admin / manager է → միայն իր client-ի փաթեթները
        if (Auth::user()->hasRole(['client_admin', 'client_admin_rfID', 'manager', 'client_sport'])) {
            $clientId = Client::where('user_id', Auth::id())->value('id');

            if ($clientId) {
                $query->where('client_id', $clientId);
            } else {
                // safety fallback
                $query->whereRaw('1=0');
            }
        }

        return $query->get();
    }
}

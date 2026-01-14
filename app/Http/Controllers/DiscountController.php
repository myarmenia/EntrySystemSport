<?php

namespace App\Http\Controllers;

use App\Http\Requests\DiscountStoreRequest;
use App\Http\Requests\DiscountUpdateRequest;
use App\Models\Client;
use App\Models\Discount;
use App\Models\Package;
use App\Services\DiscountService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DiscountController extends Controller
{
    public function __construct(private readonly DiscountService $discountService)
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $data = Discount::with('packages')->latest()->paginate(10);
        $i = ($data->currentPage() - 1) * $data->perPage();

        return view('discounts.index', compact('data', 'i'));
    }


    public function create()
    {
        $user = Auth::user();

        // գտնում ենք տվյալ user-ի client-ը
        $clientId = Client::where('user_id', $user->id)->value('id');

        if (!$clientId) {
            return redirect()->back()->with('error', 'Client չի գտնվել');
        }
        $now = Carbon::now();

        // ✅ միայն տվյալ client-ի ակտիվ փաթեթները
        $packages = Package::query()
            ->where('client_id', $clientId)
            ->where('is_active', 1)
            ->whereDoesntHave('discounts', function ($q) use ($now) {
                $q->where('status', 1)
                    ->where('starts_at', '<=', $now)
                    ->where('ends_at', '>=', $now);
            })
            ->orderByDesc('id')
            ->get();

        return view('discounts.create', compact('packages'));
    }


    public function store(DiscountStoreRequest $request)
    {
        $data = $request->only(['name', 'type', 'value', 'starts_at', 'ends_at', 'status']);
        $packageIds = $request->input('package_ids', []);

        $this->discountService->create($data, $packageIds);

        return redirect()->route('discounts.index')->with('success', 'Զեղչը ստեղծվեց');
    }

    public function edit(int $id)
    {
        $discount = $this->discountService->findOrFail($id);
        $packages = $this->discountService->packagesForSelect();

        $selectedPackageIds = $discount->packages->pluck('id')->toArray();

        return view('discounts.edit', compact('discount', 'packages', 'selectedPackageIds'));
    }

    //public function update(DiscountUpdateRequest $request, int $id)
    //{
    //    $discount = $this->discountService->findOrFail($id);
    //
    //    $data = $request->only(['name', 'type', 'value', 'starts_at', 'ends_at', 'status']);
    //    $packageIds = $request->input('package_ids', []);
    //
    //    $this->discountService->update($discount, $data, $packageIds);
    //
    //    return redirect()->route('discounts.index')->with('success', 'Զեղչը թարմացվեց');
    //}
    public function update(DiscountUpdateRequest $request, Discount $discount)
    {
        $this->discountService->update(
            $discount,
            $request->validatedData(),
            $request->packageIds()
        );

        return redirect()
            ->route('discounts.index')
            ->with('message', 'Զեղչը հաջողությամբ թարմացվեց');
    }


    public function destroy(int $id)
    {
        $discount = $this->discountService->findOrFail($id);
        $this->discountService->delete($discount);

        return redirect()->route('discounts.index')->with('success', 'Զեղչը ջնջվեց');
    }
}

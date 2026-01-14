<?php

namespace App\Http\Controllers\PackageController;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Package;
use App\Services\PackageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;



class PackageController extends Controller
{
    public function __construct(protected PackageService $service) {}

    public function index(Request $request)
    {
        $data = $this->service->list();

        return view('package.index', compact('data'))
            ->with('i', ($request->input('page', 1) - 1) * 10);
    }


    public function create()
    {
        return view('package.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'months' => 'required|integer|min:1',
            'price_amd' => 'required|integer|min:0',
        ]);
        $this->service->store($validated);

        return redirect()->route('package.list')->with('success', 'Փաթեթը ստեղծվեց');
    }


    public function destroy($id)
    {

        $package = Package::findOrFail($id);


        $package->delete();

        return redirect()
            ->route('package.list')
            ->with('success', 'Փաթեթը հաջողությամբ ջնջվեց');
    }

    public function edit($id)
    {
        $package = Package::findOrFail($id);
        return view('package.edit', compact('package'));
    }

    public function update(Request $request, $id)
    {

        // 1️⃣ գտնում ենք փաթեթը
        $package = Package::findOrFail($id);
        //dd($request);

        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'months'    => 'required|integer|min:1',
            'price_amd' => 'required|integer|min:0',
            'is_active' => 'required|boolean',
        ]);

        // 4️⃣ update
        $package->update($validated);

        // 5️⃣ redirect
        return redirect()
            ->route('package.list')
            ->with('success', 'Փաթեթը հաջողությամբ թարմացվեց');
    }
}

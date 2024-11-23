<?php

namespace App\Http\Controllers;

use App\Http\Requests\Installation\StoreInstallationRequest;
use App\Http\Requests\Installation\UpdateInstallationRequest;
use App\Models\Contract;
use App\Models\Installation;
use App\Models\InstallationSetting;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;

use function PHPUnit\Framework\isNull;

class InstallationController extends Controller
{
    public function index(Request $request)
    {
        $query = Installation::query();

        if ($request->has('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%$search%")
                    ->orWhere('contract_id', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%")
                    ->orWhere('assigned_date', 'like', "%$search%");
                // Puedes agregar más campos si es necesario
            });
        }

        // Ordenación
        $order = 'asc';
        if ($request->order && isNull($request->order)) {
            $order = $request->order;
        }
        $query->orderBy(
            $request->attribute ?: 'id',
            $order
        );


        $installation = $query->with('contract.inventorieDevice.device.user')->latest()->paginate(8)->through(function ($item) {
            return [
                'id' => $item->id,
                'contract_id' => $item->contract->inventorieDevice->device->user->name,
                'description' => $item->description,
                'assigned_date' => $item->assigned_date,
            ];
        });


        $totalInstallationCount = Installation::count();

        return Inertia::render('Admin/Installation/Installation', [
            'installation' => $installation,
            'pagination' => [
                'links' => $installation->links()->elements[0],
                'next_page_url' => $installation->nextPageUrl(),
                'prev_page_url' => $installation->previousPageUrl(),
                'per_page' => $installation->perPage(),
                'total' => $installation->total(),
            ],
            'success' => session('success') ?? null,
            'totalInstallationCount' => $totalInstallationCount
        ]);
    }
    public function edit($id)
    {
        $installation = Installation::findOrFail($id);


        // $contractIds = Installation::select('contract_id')->get();
        // $contracts = Contract::with('user')
        // ->whereNotIn('id', $contractIds)
        // ->orWhere('id', $installation->contract_id)
        // ->get();
        $contracts = Contract::with('inventorieDevice.device.user')->get();


        return Inertia::render('Admin/Installation/Edit', [
            'installation' => $installation,
            'contracts' => $contracts
        ]);
    }
    public function update(UpdateInstallationRequest $request, $id)
    {

        $installation = Installation::findOrFail($id);

        $validatedData = $request->validated();
        $installation->update($validatedData);
        return redirect()->route('installation')->with('success', 'La Instalación fue Actualizada Con Éxito');
    }
    public function create()
    {

        // $contractIds = Installation::select('contract_id')->get();
        // $contracts = Contract::with('user')->whereNotIn('id', $contractIds)->get();
        $contracts = Contract::with('inventorieDevice.device.user')->get();

        // dd($contracts);
        return Inertia::render('Admin/Installation/Create', [
            'contracts' => $contracts
        ]);
    }
    public function store(StoreInstallationRequest $request)
    {
        $validatedData = $request->validated();
        $installation =  Installation::create([
            'contract_id' => $validatedData['contract_id'],
            'description' => $validatedData['description'],
            'assigned_date' => $validatedData['assigned_date'],
        ]);

        InstallationSetting::create([
            'installation_id' => $installation->id,
        ]);

        //  $this->setFirstMonthPayment($installation);
        return redirect()->route('installation')->with('success', 'La Instalación ha sido creado con éxito');
    }

    public function destroy($id, Request $request)
    {
        $data = [
            "q" => $request->q,
            "attribute" => $request->attribute,
            "order" => $request->order,
        ];
        try {
            $community = Installation::findOrFail($id);
            $community->delete();
            return Redirect::route('installation', $data)->with('success', 'La Instalación fue Eliminado Con Éxito');
        } catch (Exception  $e) {
            return Redirect::route('installation', $data)->with('error', 'Error al cargar el registro');
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\InstallationSettings\StoreInstallationSettingsRequest;
use App\Models\Installation;
use App\Models\InstallationSetting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InstallationSettingsController extends Controller
{
    public function index(Request $request)
    {
        $query = InstallationSetting::query();


        if ($request->has('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%$search%")
                    ->orWhere('installation_id', 'like', "%$search%")
                    ->orWhere('exemption_months', 'like', "%$search%");
                // Puedes agregar más campos si es necesario
            });
        }

        if ($request->attribute) {
            $query->orderBy($request->attribute, $request->order);
        } else {
            $query->orderBy('id', 'asc');
        }

        $installationSt = $query->with('installation.contract.device.device.user')->latest()->paginate(8)->through(function ($item) {
            return [
                'id' => $item->id,
                'installation_id' => $item->installation->contract->device->user->name,
                'exemption_months' => $item->exemption_months,

            ];
        });



        $totalInstallationStCount = InstallationSetting::count();

        return Inertia::render('Admin/Settings/InstallationSettings/InstallationSettings', [
            'installationSettings' => $installationSt,
            'pagination' => [
                'links' => $installationSt->links()->elements[0],
                'next_page_url' => $installationSt->nextPageUrl(),
                'prev_page_url' => $installationSt->previousPageUrl(),
                'per_page' => $installationSt->perPage(),
                'total' => $installationSt->total(),
            ],
            'success' => session('success') ?? null,
            'error' => session('error') ?? null,
            'totalInstallationSettingsCount' => $totalInstallationStCount
        ]);
    }

    public function create()
    {
        $installations = Installation::with('contract.device.device.user')->get();
       // $installations = Installation::with('contract.device.device.user')->get();
       // dd($installations);
        
        return Inertia::render(
            'Admin/Settings/InstallationSettings/Create',
            [
                'installations' => $installations,
            ]
        );
    }

    public function store(StoreInstallationSettingsRequest $request)
    {
        $installation = InstallationSetting::create([
            'installation_id' => $request->installation_id,
            'exemption_months' => $request->exemption_months,
        ]);
        return redirect()->route('settings.installation')->with('success', 'La configuración ha sido creado con éxito');
    }
    
}

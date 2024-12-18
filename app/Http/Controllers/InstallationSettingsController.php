<?php

namespace App\Http\Controllers;

use App\Http\Requests\InstallationSettings\StoreInstallationSettingsRequest;
use App\Http\Requests\InstallationSettings\UpdateInstallationSettingsRequest;
use App\Models\ExemptionPeriod;
use App\Models\Installation;
use App\Models\InstallationSetting;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

use function PHPUnit\Framework\isNull;

class InstallationSettingsController extends Controller
{
    public function index(Request $request)
    {
        try{
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

            // Ordenación
            $order = 'asc';
            if ($request->order && in_array(strtolower($request->order), ['asc', 'desc'], true)) {
                $order = strtolower($request->order);
            }
            $query->orderBy(
                $request->attribute ?: 'id',
                $order
            );


            $installationSt = $query->with('installation.contract.inventorieDevice.device.user')->latest()->paginate(8)->through(function ($item) {
                return [
                    'id' => $item->id,
                    //'installation_id' => $item->installation_id,
                    'installation_id' => $item->installation->contract->inventorieDevice->device->user->name,
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
        }catch(Exception $e){
            return redirect()->back()->with('error', 'Error al mostrar las instalaciones');

        }

        // Ordenación
        $order = 'asc';
        if ($request->order && in_array(strtolower($request->order), ['asc', 'desc'], true)) {
            $order = strtolower($request->order);
        }
        $query->orderBy(
            $request->attribute ?: 'id',
            $order
        );


        $installationSt = $query->with('installation.contract.inventorieDevice.device.user')->latest()->paginate(8)->through(function ($item) {
            return [
                'id' => $item->id,
                //'installation_id' => $item->installation_id,
                'installation_id' => $item->installation->contract->inventorieDevice->device->user->name,
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
    public function editFromInstallation($id)
    {
        try {
            $installationSetting  = InstallationSetting::where('installation_id', $id)->first();
            if (!$installationSetting) {

                $installationSetting =  InstallationSetting::create([
                    'installation_id' => $id,
                ]);
            }
            $installation = Installation::with('contract.inventorieDevice.device.user')->findOrFail($installationSetting->installation_id);


            return Inertia::render('Admin/Settings/InstallationSettings/Edit', [
                'installationSetting' => $installationSetting,
                'installation' => $installation,
            ]);
        } catch (Exception $e) {
            //dd($e);
            return Redirect::back()->with('error', 'Error al cargar el registro');

            // return redirect()->route('charges')->with('error', 'Hubo un error al obtener la información del registro');
        }
    }
    public function edit($id)
    {

        try {
            //dd();
            $installationSetting  = InstallationSetting::findOrFail($id);

            $installation = Installation::with('contract.inventorieDevice.device.user')->findOrFail($installationSetting->installation_id);

            return Inertia::render('Admin/Settings/InstallationSettings/Edit', [
                'installationSetting' => $installationSetting,
                'installation' => $installation,
            ]);
        } catch (Exception $e) {
            return Redirect::back()->with('error', 'Error al cargar el registro');
        }
    }
    public function update(UpdateInstallationSettingsRequest $request, $id)
    {
        // dd("aqui");
        try{

            $installation = InstallationSetting::findOrFail($id);
    
            $validatedData = $request->validated();
            
            self::setNewInstallation($installation, $validatedData['exemption_months']);
            $installation->update($validatedData);
    
            return redirect()->route('settings.installation')->with('success', 'Configuración Actualizado Con Éxito');
        }catch(Exception $e){
            return redirect()->route('settings.installation')->with('error', 'Error al Actualizar la Configuración');

        }

    }
    public function create()
    {
        try {
            $installations = Installation::with('contract.inventorieDevice.device.user')
                ->whereNotIn('id', function ($query) {
                    $query->select('installation_id')->from('installation_settings');
                })
                ->get();
            // $settings = InstallationSetting::all();
            // $installationIds = $settings->pluck('installation_id');

            // $installations = Installation::with('contract.inventorieDevice.device.user')
            // ->whereNotIn('id',$installationIds)
            // ->get();
            // $installations = Installation::with('contract.inventorieDevice.device.user')->get();
            // dd($installations);

            return Inertia::render(
                'Admin/Settings/InstallationSettings/Create',
                [
                    'installations' => $installations,
                ]
            );
        } catch (Exception $e) {
        }
    }
    public function getExemptionMonth($id){

        $installationSettings = InstallationSetting::where();
    }
    public function store(StoreInstallationSettingsRequest $request)
    {
        try {
            $validateData = $request->validated();
            //dd("Entro");
            $installation_settings = InstallationSetting::create([
                'installation_id' => $validateData['installation_id'],
                'exemption_months' => $validateData['exemption_months'],
            ]);

            self::setNewInstallation($installation_settings, null);
            return Redirect::route('settings.installation')->with('success', 'La configuración ha sido creado con éxito');

            // return redirect()->route('settings.installation')->with('success', 'La configuración ha sido creado con éxito');
        } catch (Exception $e) {
            // return redirect()->route('settings.installation')->with('success', 'Error Al Crear La Configuración');
            return Redirect::route('settings.installation')->with('error', 'Error al cargar el registro');
        }
    }
    public function setNewInstallation(InstallationSetting $installation_s, $new_exemption){

        $installation = Installation::with('installationSettings')->findOrFail($installation_s->installation_id);

        $controller = new InstallationController;
        //$madafaker = Carbon::parse($installation->assigned_date)->addMonths((int)$new_exemption);

        if($installation->description == "1")
        {
            //dd("Entro aqui");
            if(!is_null($new_exemption))
            {   
                Log::info("1. Entra en set New installation, si hay exemption nueve : ".$installation->assigned_date);
                //Log::info("Este es el cambio de instalación: ".$installation->);
                $controller->
                updateEndDateContract(
                    $installation, 
                    Carbon::parse($installation->assigned_date),
                    $installation->description,
                    $new_exemption,
                );
            }else{
                Log::info("Es new exemption es nulo");
                $controller-> updateEndDateContract(
                    $installation, 
                    ($installation->assigned_date), 
                    $installation->description,
                    null
                );
            }

        }

        // if(!is_null($new_exemption_months))
        // {
        //     if(is_null($installation_s->exemption_months))
        //     {
        //         $exemption_config = ExemptionPeriod::first();

        //         $date = Carbon::parse($installation->assigned_date);
                
        //         if(($date->day >= $exemption_config->start_day)&&($date->day <= $exemption_config->end_day))
        //         {
                    
        //         }


        //         //Entonces toma los de la configuración global
                
        //     }else{
        //         //Entonces toma las configuración personalizada
                
        //     }
        // }else{

        // }

    }

    public function destroy($id, Request $request)
    {
        $data = [
            "q" => $request->q ?? null,
            "attribute" => $request->attribute ?? null,
            "order" => $request->order ?? null,
        ];
        try {
            $settings = InstallationSetting::with('installation')->findOrFail($id);
            $this->removeDate($settings);


            $settings->delete();
            return Redirect::route('settings.installation', $data)->with('success', 'Configuración de Instalación Eliminada Con Éxito');
        } catch (Exception $e) {
            return Redirect::route('settings.installation', $data)->with('errror', 'Error al cargar el registro');
        }
    }

    private function removeDate(InstallationSetting $installationSetting)
    {
        if(!is_null($installationSetting->exemption_months))
        {
            $controller = new ContractController;
            $controller->removeDateInstallationSettings($installationSetting->installation->contract_id, $installationSetting->exemption_months);

            $controller->updateEndDateContract($installationSetting->installation, $installationSetting->installation->assigned_date, null);
        }
    }
}

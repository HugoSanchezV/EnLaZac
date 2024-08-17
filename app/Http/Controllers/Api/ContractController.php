<?php

namespace App\Http\Controllers\Api;

use App\Models\Contract;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Contract as ContractResource;

class OutletController extends Controller
{
    /**
     * Get outlet listing on Leaflet JS geoJSON data structure.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $contracts = Contract::all();

        $geoJSONdata = $contracts->map(function ($contracts) {
            return [
                'type'       => 'Feature',
                'properties' => new ContractResource($contracts),
                'geometry'   => [
                    'type'        => 'Point',
                    'coordinates' => [
                        $contracts->longitude,
                        $contracts->latitude,
                    ],
                ],
            ];
        });

        return response()->json([
            'type'     => 'FeatureCollection',
            'features' => $geoJSONdata,
        ]);
    }
}
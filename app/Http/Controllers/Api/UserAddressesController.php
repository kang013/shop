<?php


namespace App\Http\Controllers\Api;

use App\Http\Requests\UserAddressRequest;
use App\Http\Resources\UserAddressesResource;
use Illuminate\Http\Request;

class UserAddressesController extends Controller
{
    public function index(Request $request)
    {
        return new UserAddressesResource($request->user()->addresses);
    }

    public function store(UserAddressRequest $request)
    {
        $request->user()->addresses()->create($request->only([
            'province',
            'city',
            'district',
            'address',
            'zip',
            'contact_name',
            'contact_phone',
        ]));

        return response()->json($request)->setStatusCode(201);
    }
}

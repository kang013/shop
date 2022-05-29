<?php


namespace App\Http\Controllers\Api;

use App\Http\Resources\UserAddressesResource;
use Illuminate\Http\Request;

class UserAddressesController extends Controller
{
    public function index(Request $request)
    {
        return new UserAddressesResource($request->user()->addresses);
    }
}

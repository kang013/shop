<?php


namespace App\Http\Controllers\Api;

use App\Http\Requests\UserAddressRequest;
use App\Http\Resources\UserAddressesResource;
use App\Models\UserAddress;
use Illuminate\Http\Request;

class UserAddressesController extends Controller
{
    public function index(Request $request)
    {
        return new UserAddressesResource($request->user()->addresses);
    }

    /**
     * 获取一个默认收货地址
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function default(Request $request)
    {
        $addresses = $request->user()->addresses()->orderBy('default','desc')->orderBy('last_used_at', 'desc')->first();
        return response()->json($addresses);
    }

    public function store(UserAddressRequest $request)
    {
        $result = $request->user()->addresses()->create($request->only([
            'province',
            'city',
            'district',
            'address',
            'contact_name',
            'contact_phone',
            'default',
        ]));

        if($result->default){
            $request->user()->addresses()->where('default',1)->update(['default'=>0]);
            $request->user()->addresses()->where('id',$result->id)->update(['default'=>1]);
        }

        return response()->json($request);
    }

    public function update(UserAddress $user_address, UserAddressRequest $request)
    {
        $this->authorize('own', $user_address);
        $user_address->update($request->only([
            'province',
            'city',
            'district',
            'address',
            'contact_name',
            'contact_phone',
            'default',
        ]));

        if($user_address->default){
            $request->user()->addresses()->where('default',1)->update(['default'=>0]);
            $request->user()->addresses()->where('id',$user_address->id)->update(['default'=>1]);
        }

        return response()->json($request);
    }

    public function destroy(UserAddress $user_address)
    {
        $this->authorize('own', $user_address);
        $user_address->delete();

        return response()->json();
    }
}

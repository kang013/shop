<?php


namespace App\Policies;

use App\Models\CartItem;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CartItemPolicy
{
    use HandlesAuthorization;

    public function own(User $user, CartItem $CartItem)
    {
        return $CartItem->user_id == $user->id;
    }
}

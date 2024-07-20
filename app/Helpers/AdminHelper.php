<?php

namespace App\Helpers;

use App\Models\User;


class AdminHelper 
{
    // get authenticated user from DB
    public function GetAuthUser()
    {
        $publicHelper = new PublicHelper();
        $token = $publicHelper->GetAndDecodeJWT();
        $userID = $token->data->userID;
           return $user;

    }
}
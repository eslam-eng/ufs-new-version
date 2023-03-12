<?php

namespace App\Services;

use App\Exceptions\NotFoundException;
use App\Models\User;

class AuthService extends BaseService
{
    /**
     * @throws NotFoundException
     * @throws \Exception
     */
    public function loginWithEmailOrPhone(string $identifier, string $password)
    {

        $identifierField = is_numeric($identifier) ? 'phone':'email';
        $credential = [$identifierField=>$identifier,'password'=>$password];
        $user = User::where($identifierField, $identifier)->where('password',$password)->first();
//        if (!auth()->attempt($credential))
//            return throw new NotFoundException(__('lang.login failed'),422);
        if (!$user)
            return throw new NotFoundException(__('The user name or password are incorrect.'), 422);
       if ($user->active == User::NONACTIVE)
            return throw new \Exception(__('account_not_activated'),422);
        return $user;
    }

    public function getAuthUser()
    {
        return auth('sanctum')->user();
    }

    public function setUserFcmToken(User $user , $fcm_token)
    {
        if (isset($fcm_token))
            $user->update(['device_token'=>$fcm_token]);
    }
}

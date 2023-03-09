<?php

namespace App\Services;

use App\Exceptions\NotFoundException;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class AuthService extends BaseService
{
    /**
     * @throws NotFoundException
     */
    public function loginWithEmailOrPhone(string $identifier, string $password)
    {

        $identifierField = is_numeric($identifier) ? 'phone':'email';
        $credential = [$identifierField=>$identifier,'password'=>$password];
        if (!auth()->attempt($credential))
            return throw new NotFoundException(__('lang.login failed'),422);
        $user = User::where($identifierField, $identifier)->first();
        if ($user->active == User::NONACTIVE)
            return throw new \Exception(__('lang.account_not_activated'),422);
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

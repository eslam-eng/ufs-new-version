<?php

if (!function_exists('apiResponse')) {
    function apiResponse($data = null, $message = null, $code = 200)
    {
        $array = [
            'data' => $data,
            'status' => in_array($code, successCode()),
            'message' => $message,
        ];
        return response($array, $code);
    }
}

if (!function_exists('successCode')) {
    function successCode(): array
    {
        return [
            200, 201, 202
        ];
    }
}


//if (!function_exists('notifyUser')) {
//
//    function notifyUser(\App\Models\User $user,$data=[])
//    {
//        $user->notify(new \App\Notifications\GeneralNotification($data));
//    }
//}

if (!function_exists('getLocale')) {

    function getLocale(): string
    {
        return app()->getLocale();
    }
}


if (!function_exists('setLanguage')) {

    function setLanguage(string $locale): void
    {
        app()->setLocale($locale);
    }
}

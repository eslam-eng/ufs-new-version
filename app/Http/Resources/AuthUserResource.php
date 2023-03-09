<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AuthUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        return [
            'token'=>$this->getToken(),
            'token_type'=>'Bearer',
            'user'=>[
                "id"=> $this->id,
                "name"=> $this->name,
                "email"=> $this->email,
                "user_name"=> $this->username,
                "phone"=> $this->phone,
            ],
        ];
    }

    public $additional =[
        'status'=>true,
        'message'=>'logged_in_successfully'
    ];
}

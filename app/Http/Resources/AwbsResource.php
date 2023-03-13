<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AwbsResource extends JsonResource
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
            'id'=>$this->id,
            'date'=>$this->date,
            'code'=>$this->code,
            'company'=>$this->whenLoaded('company') && isset($this->company) ? $this->company->name : null,
            'department'=>$this->whenLoaded('department') && isset($this->department) ? $this->department->name:null,
            'branch'=>$this->whenLoaded('branch') && isset($this->branch) ? $this->branch->name:null,
            'receiver_name'=>$this->whenLoaded('receiver') && isset($this->receiver) ? $this->receiver->name:null,
            'reference'=>$this->whenLoaded('receiver') && isset($this->receiver) ? $this->receiver->referance:null,
            'status'=>optional($this->status)->name,
            'receiver_title'=>optional($this->status)->name,
            'id_number'=>$this->id_number,
            'image'=>$this->attachment
        ];
    }

    public $additional =[
        'status'=>true,
        'message'=>'awbs data returned successfully'
    ];
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AwbsResource;
use App\Services\AwbService;
use Illuminate\Http\Request;

class AwbController extends Controller
{
    public function __construct(public AwbService $awbService)
    {
    }

    public function index(Request $request)
    {
        $filters = [
            'company_id' => auth('sanctum')->user()->company_id,
            'branch_id' => auth('sanctum')->user()->branch_id,
        ];
        if (isset($request->keyword))
            $filters['keyword'] = $request->keyword;
        $withRelations = [
            'company:id,name,address', 'department:id,name', 'branch',
            'receiver:id,name,phone,referance', 'status'
        ];
        $awbs = $this->awbService->listing(filters: $filters, withRelations: $withRelations);
        return AwbsResource::collection($awbs);
    }
}

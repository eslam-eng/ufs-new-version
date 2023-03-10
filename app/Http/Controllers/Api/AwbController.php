<?php

namespace App\Http\Controllers;

use App\Helper\StatusCode;
use App\Imports\AwbImport;
use App\Models\Awb;
use App\Models\AwbHistory;
use App\Models\AwbDetail;
use App\Models\Receipt;
use App\Models\Status;
use App\Models\Store;
use Illuminate\Http\Request;
use App\Models\CourierSheetDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AwbController extends Controller {

    public function index() {
        $referanceCol = "(select referance from receivers where receivers.id = receiver_id)";
        $branchName = "(select branch_name from receivers where receivers.id = receiver_id limit 1)";
        $addressCol = "(select address from receivers where receivers.id = receiver_id limit 1)";
        $query = Awb::query()
        ->with(['company', 'department', 'paymentType', 'branch', 'receiver', 'service', 'status', 'user', 'awbHistory'])
        ->select(
            '*',
            DB::raw($referanceCol . ' as referance'),
            DB::raw($branchName . ' as branch_name'),
            DB::raw($addressCol . ' as receiver_address')
        );

        if (request()->company_id > 0) {
            $query->where('company_id', request()->company_id);
        }

        if (request()->branch_id > 0)
            $query->where('branch_id', request()->branch_id);

        if (request()->status_id > 0)
            $query->where('status_id', request()->status_id);

        if (request()->city_id > 0)
            $query->where('city_id', request()->city_id);

        if (request()->area_id > 0)
            $query->where('area_id', request()->area_id);

        if (request()->department_id > 0)
            $query->where('department_id', request()->department_id);

        if (request()->search > 0)
            $query->where(function($q){
                $q->where('code', "like", "%" . request()->search . "%")
                ->orWhere('notes', "like", "%" . request()->search . "%");
            });

        if (request()->referance) {
            $query->whereRaw($referanceCol . " = ? ", [request()->referance]);
        }

        if (request()->branch_name) {
            $ids = DB::table('receivers')->select('id', 'branch_name')->where("branch_name",  "like",  "%" . request()->branch_name . "%")->pluck('id')->toArray();

            //return $ids;
            $query->whereIn('receiver_id', $ids);
            //$query->whereRaw($branchName . " like %'" . request()->branch_name . "'%");
        }

        if (request()->code)
            $query->where('code', "like", "%" . request()->code . "%");

        if (request()->date_from)
            $query->whereDate('date', '>=', request()->date_from);

        if (request()->date_to)
            $query->whereDate('date', '<=', request()->date_to);

        if (request()->courier_sheet == 'active') {
            $ids = CourierSheetDetail::select('awb_id')->pluck('awb_id')->toArray();
            $query->whereNotIn('id', $ids);
        }

        if (request()->user()->company_id != 1) {
            $query->where('company_id', request()->user()->company_id);
            $query->where('branch_id', request()->user()->branch_id);
        }

        if (request()->steper) {
            $ids = Status::where('steper', request()->steper)->pluck('id')->toArray();
            $query->whereIn('status_id', $ids);
        }

        $pageLength = 60;

        if (request()->page_length > 0)
            $pageLength = request()->page_length;

        return $query->orderBy('id', "DESC")->paginate($pageLength);
    }

    public function load($resource) {
        $query = Awb::with(['details', 'company', 'department', 'paymentType', 'branch', 'receiver', 'service', 'status', 'city', 'area', 'user']);
        return $query->where('id', $resource)->first();
    }

    public function findByCode($code)
    {
       $awb = Awb::query()->with(['details', 'company', 'department', 'paymentType', 'branch', 'receiver', 'service', 'status', 'city', 'area', 'user'])->where('code',$code)->first();
       if (!$awb)
           return responseJson(0, __('there is not awb with this code'));
        return responseJson(1, null,$awb) ;
    }

    public function getTrash()
    {
        return Awb::onlyTrashed()
                ->with(['company', 'department', 'paymentType', 'branch',
                    'receiver', 'service', 'status', 'city', 'area', 'user', 'awbHistory'])->get();

    }

    public function changeStatus(Awb $resource, Request $request) {
        $oldStatus = $resource->status->name;

        if (optional($resource->status)->code == StatusCode::$DELIVERED && $request->status_id == optional(Status::delivered())->id) {

            return responseJson(0, __('the shipment already delivered'));

        }

        $validator = validator($request->all(), ['status_id' => 'required']);
        if ($validator->fails()) {
            return responseJson(0, $validator->errors()->first(), "");
        }
        try {


            // store awb status
            $resource->update($request->all());

            AwbHistory::create([
                'awb_id' => $resource->id,
                'user_id' => $request->user()->id,
                'status_id' => $request->status_id
            ]);

            // calculate awb shipment price
            $calAwbShipmentPrice = new CalculatorShipmentPriceController();
            $calAwbShipmentPrice->getShipmentPrice($resource->fresh());

            // make transaction on treasury in case of collected status
            $this->makeTransactionForCollectedStatus($resource->fresh());

            // make transaction on treasury in case of paid to sender status
            $this->makeTransactionForPaidToSenderStatus($resource->fresh());

            // make transaction on treasury in case of Return With Paid Status
            $this->makeTransactionForReturnWithChargeStatus($resource->fresh());

            watch(__('The shipment ') . $resource->code . ' status has changed from ' . $oldStatus . ' to ' . $resource->status->name, 'fa fa-newspaper-o');
            return responseJson(1, __('done'), $resource->awbHistory()->get());
        } catch (\Exception $th) {
            return responseJson(0, $th->getMessage());
        }
    }

    public function changeStatusV1(Awb $resource, Request $request) {
        $oldStatus = $resource->status->name;

        if (optional($resource->status)->code == StatusCode::$DELIVERED && $request->status_id == optional(Status::delivered())->id) {
            return responseJson(0, __('the shipment already delivered'));
        }

        $validator = validator($request->all(), [
            'receiver_name' => ['required'],
            'receiver_title' => ['required'],
            'attachment' => ['nullable',Rule::requiredIf(is_null($request->get('id_number')))],
            'id_number' => ['nullable','numeric','min:14',Rule::requiredIf(!$request->has('attachment') || is_null($request->file('attachment')))],
        ]);
        if ($validator->fails()) {
            return responseJson(0, $validator->errors()->first(), "");
        }
        try {
            // store awb status
            $filename = null ;
            if ($request->has('attachment') && !is_null($request->file('attachment')))
            {
                $filename = uploadImg($request->file('attachment'), "/uploads/awbs/delivered/",function ($filename){
                    return $filename ;
                });
            }
            $data = array_merge($validator->validated(),['attachment'=>$filename,'status_id'=>StatusCode::$DELIVERED]);
            $resource->update($data);

            AwbHistory::create([
                'awb_id' => $resource->id,
                'user_id' => $request->user()->id,
                'status_id' => StatusCode::$DELIVERED
            ]);

            // calculate awb shipment price
            $calAwbShipmentPrice = new CalculatorShipmentPriceController();
            $calAwbShipmentPrice->getShipmentPrice($resource->fresh());

            // make transaction on treasury in case of collected status
            $this->makeTransactionForCollectedStatus($resource->fresh());

            // make transaction on treasury in case of paid to sender status
            $this->makeTransactionForPaidToSenderStatus($resource->fresh());

            // make transaction on treasury in case of Return With Paid Status
            $this->makeTransactionForReturnWithChargeStatus($resource->fresh());

            watch(__('The shipment ') . $resource->code . ' status has changed from ' . $oldStatus . ' to ' . $resource->status->name, 'fa fa-newspaper-o');
            return responseJson(1, __('done'), $resource);
        } catch (\Exception $th) {
            return responseJson(0, $th->getMessage());
        }
    }


    public function store(Request $request) {
        $validator = validator($request->all(), $this->rules());
        if ($validator->fails()) {
            return responseJson(0, $validator->errors()->first(), "");
        }

        if (Status::count() <= 0)
            return responseJson(0, __('there is not status exists'));

        try {
            $data = $request->all();
            $data['status_id'] = optional(Status::where('type', 'awb')->first())->id;
            $data['user_id'] = $request->user()->id;

            if ($request->date)
                $data['date'] = $request->date;
            else
                $data['date'] = date('Y-m-d');

            // store awb object
            $resource = Awb::create($data);

            // awb code
            $code = date('y') . date('m') . date('d') . $resource->id;

            // check if is return
            if ($resource->is_return)
                $code = "R-" . $code;

            // generate awb code
            $resource->update([
                "code" => $code
            ]);

            if ($request->created_at) {
                $resource->update([
                    "created_at" => $request->created_at
                ]);
            }

            // store history
            AwbHistory::create([
                'awb_id' => $resource->id,
                'user_id' => $request->user()->id,
                'status_id' => $resource->status_id
            ]);

            // store details of awb
            if (isset($data['details']))
            foreach ($data['details'] as $row) {
                $row['awb_id'] = $resource->id;
                AwbDetail::create($row);
            }

            // calculate awb shipment price
            $calAwbShipmentPrice = new CalculatorShipmentPriceController();
            $calAwbShipmentPrice->getShipmentPrice($resource->fresh());

            // make transationc
            $this->makeTransactionForCollectedStatus($resource->refresh());

            watch(__('create awb with code ') . $resource->code, 'fa fa-newspaper-o');
            return responseJson(1, __('done'), $resource->fresh());
        } catch (\Exception $th) {
        return responseJson(0, $th->getMessage()/*__('please fill all inputs')*/);
        }
    }

    public function update(Request $request, Awb $resource) {
        $validator = validator($request->all(), $this->rules());
        if ($validator->fails()) {
            return responseJson(0, $validator->errors()->first(), "");
        }
        try {
            $data = $request->all();

            // store awb object
            $resource->update($data);

            if (isset($data['details'])) {
                // delete old
                $resource->details()->delete();

                // store new details of awb
                foreach ($data['details'] as $row) {
                    $row['awb_id'] = $resource->id;
                    AwbDetail::create($row);
                }
            }

            // calculate awb shipment price
            $calAwbShipmentPrice = new CalculatorShipmentPriceController();
            $calAwbShipmentPrice->getShipmentPrice($resource->refresh());


            watch(__('update awb with code ') . $resource->code, 'fa fa-newspaper-o');
            return responseJson(1, __('done'), $resource);
        } catch (\Exception $th) {
            return responseJson(0, $th->getMessage());
        }
    }

    public function destroy($resource) {
        try {
            $resource = DB::table('awbs')->find($resource);

            watch(__('delete awb with code ') . $resource->code, 'fa fa-trash');
            if ($resource->deleted_at) {
                $resource = Awb::onlyTrashed()->find($resource->id);
                $resource->forceDelete();
            }
            else {
                $resource = Awb::find($resource->id);
                $resource->delete();
            }
            return responseJson(1, __('done'));
        } catch (\Exception $th) {
            return responseJson(0, __($this->exception_message),$th->getMessage());
        }
    }

    public function restore($resource) {
        try {
            $resource = Awb::onlyTrashed()->find($resource);
            watch(__('restore awb with code ') . $resource->code, 'fa fa-reply');
            $resource->restore();
            return responseJson(1, __('done'));
        } catch (\Exception $th) {
            return responseJson(0, __($this->exception_message),$th->getMessage());
        }
    }


    public function awbHistory()
    {
        $query = Awb::query()->with('awbHistory');
        if (request()->code > 0)
            $query->where('code', "like", "%" . request()->code . "%");
        return $query->get();
    }


    public function downloadExcel()
    {
        return response()->download(public_path('/uploads/excel/awb.xlsx'));
    }

//    import excel file into data base

    public function awbImport(Request $request)
    {
        $validator = validator($request->all(),['file'=>'required|mimes:xls,xlsx',]);
        if ($validator->fails()) {
            return responseJson(0, $validator->errors()->first(), "");
        }
        try {
            $file = $request->file('file');
            $awbfile = new AwbImport();
            $awbfile->import($file);
            if ($awbfile->failures()->isNotEmpty())
                return responseJson(0, "", $awbfile->failures());

            return responseJson(1, __('file imported'), "");
        }catch (\Exception $e){
            return responseJson(0, __('this item cannot be deleted may be there relation to another'), $e->getMessage());
        }

    }


    public function makeTransactionForReturnWithChargeStatus(Awb $awb)
    {
        if (
            (optional($awb->status)->code == StatusCode::$RETURN_WITH_CHARGE || optional($awb->status)->code == StatusCode::$RETURN_WITHOUT_CHARGE) &&
            $awb->payment_type_id != 1
            )
        {
            $value = $awb->shiping_price;
            $store = Store::first();

            if ($value == 0) {
                return;
            }
            $receipt = Receipt::where('model_id', $awb->id)->where('model_type', 'awb')->where('type', 'in')->first();
            if ($receipt) {
                if ($receipt->value != $value) {
                    $store->makeTransation($receipt->value * -1);
                    $receipt->delete();
                } else
                    return;
            }

            $inReceipt = Receipt::create([
                'date'=>date('Y-m-d'),
                'store_id'=>optional($store)->id,
                'model_id'=>$awb->id,
                'model_type'=>'awb',
                'notes'=>__('تم التحصيل من بوليصه رقم ').$awb->code,
                'value'=>$value,
                'type'=>'in'
            ]);

            $store->makeTransation($value);
        }
    }

    public function makeTransactionForCollectedStatus(Awb $awb)
    {
        if ((optional($awb->status)->code == 7 && $awb->payment_type_id != 1) || ($awb->payment_type_id == 1 && optional($awb->status)->code != 8))
        {
            $value = $awb->shiping_price+$awb->collection;
            $store = Store::first();

            if ($value == 0) {
                return;
            }
            $receipt = Receipt::where('model_id', $awb->id)->where('model_type', 'awb')->where('type', 'in')->first();
            if ($receipt) {
                if ($receipt->value != $value) {
                    $store->makeTransation($receipt->value * -1);
                    $receipt->delete();
                } else
                    return;
            }

            $inReceipt = Receipt::create([
                'date'=>date('Y-m-d'),
                'store_id'=>optional($store)->id,
                'model_id'=>$awb->id,
                'model_type'=>'awb',
                'notes'=>__('تم التحصيل من بوليصه رقم ').$awb->code,
                'value'=>$value,
                'type'=>'in'
            ]);

            $store->makeTransation($value);
        }
    }

    public function makeTransactionForPaidToSenderStatus(Awb $awb)
    {
        if (optional($awb->status)->code == 8)
        {
            $value = $awb->net_price;
            $store = Store::first();

            if ($value == 0) {
                return;
            }

            $receipt = Receipt::where('model_id', $awb->id)->where('model_type', 'awb')->where('type', 'out')->first();
            if ($receipt) {
                if ($receipt->value != $value) {
                    $store->makeTransation($receipt->value);
                    $receipt->delete();
                } else
                    return;
            }

            $inReceipt = Receipt::create([
                'date'=>date('Y-m-d'),
                'store_id'=>optional($store)->id,
                'model_id'=>$awb->id,
                'model_type'=>'awb',
                'notes'=>__('تم التوريد من البوليصه رقم ').$awb->code,
                'value'=>$value,
                'type'=>'out'
            ]);

            $store->makeTransation($value * -1);
        }
    }

    public function rules() {
        return [
            'collection' => 'nullable|numeric',
            'company_id' => 'required|exists:companies,id',
            'branch_id' => 'required|exists:branches,id',
            'department_id' => 'required|exists:departments,id',
            'receiver_id' => 'required|exists:receivers,id',
            'payment_type_id' => 'required|exists:payment_types,id',
            'service_id' => 'required|exists:services,id',
            'city_id' => 'required|exists:cities,id',
            'area_id' => 'required|exists:areas,id',
            'weight' => 'required',
            'pieces' => 'required',
        ];
    }

}

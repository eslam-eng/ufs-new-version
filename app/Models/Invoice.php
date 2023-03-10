<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $table = "invoices";
    protected $fillable = ['invoice_id', 'url'];


    public function getQrcodeTextOfInvoiceCover()
    {
        $company = Company::find(request()->company_id);
        $totalTms = request()->export_transportation +
            request()->internal_transportation +
            request()->custom_clearance +
            request()->document_legalization +
            request()->certificate_conformity;

        $vatAmount1 = (request()->vet_fax / 100) * request()->internal_transportation;
        $vatAmount2 = (request()->vet_fax / 100) * request()->custom_clearance;
        $vatAmount = $vatAmount1 + $vatAmount2;

        $text = "";

        $text .= "Tax Invoice No: " . request()->invoice_number . "\n";
        $text .= "Date: " . request()->date . "\n";
        //$text .= "VAT No: " . $company->commercial_number . "\n";
        $text .= "Tax No: 310233274700003\n";
        $text .= "Total Amount ( GR ): " . $totalTms . "\n";
        $text .= "Total Vat: " . $vatAmount . "\n";
        $text .= "Total Amount ( GR + VAT ): " . ($totalTms + $vatAmount) . "\n";


        if ($totalTms > 0) {
            $this->qrcode_text = $text;
            $this->update();
        }

        return $this->qrcode_text;
    }

    public function getQrcodeTextOfInvoiceAwb()
    {
        $company = Company::find(request()->company_id);
        $text = "";

        $text .= "Tax Invoice No: " . request()->invoice_number . "\n";
        $text .= "Date: " . request()->date . "\n";
        $text .= "Customer Name: " . $company->name . "\n";
        $text .= "Customer Address: " . $company->address . "\n";
        $text .= "Tax No: 310233274700003\n";
        $text .= "Special transaction Options: Export\n";
        $text .= "Total With Out VAT: " . request()->allWithoutTotal . "\n";
        $text .= "VAT Amount 15%: " . request()->totalVats . "\n";
        $text .= "Total With VAT: " . request()->allTotal . "\n";


        //if (request()->allWithoutTotal > 0) {
            $this->qrcode_text = $text;
            $this->update();
        //}

        return $this->qrcode_text;
    }
}

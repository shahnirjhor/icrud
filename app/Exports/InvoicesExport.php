<?php

namespace App\Exports;

use App\Models\Company;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class InvoicesExport implements FromView
{
    protected $invoices;
    protected $companyDateFormat;

    public function __construct(Request $request)
    {
        $query = Invoice::query();
        $companyDateFormat = "d M Y";
        $this->invoices = $query->get();
        $this->companyDateFormat = $companyDateFormat;
    }

    public function view(): View
    {
        return view('exports.invoices', [
            'invoices' => $this->invoices,
            'companyDateFormat' => $this->companyDateFormat,
        ]);
    }
}

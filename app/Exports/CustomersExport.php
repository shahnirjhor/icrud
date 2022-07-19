<?php

namespace App\Exports;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class CustomersExport implements FromView
{
    protected $customers;

    public function __construct(Request $request, $company_id=null)
    {
        $query = Customer::query();

        if ($company_id) {
            $query->where('company_id', $company_id);
        }

        $this->customers = $query->get();
    }

    public function view(): View
    {
        return view('exports.customers', [
            'customers' => $this->customers
        ]);
    }

}

<?php

namespace App\Exports;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ItemsExport implements FromView
{
    protected $items;
    protected $companyDateFormat;

    public function __construct(Request $request, $company_id=null)
    {
        $query = Item::query();
        $companyDateFormat = "d M Y";
        $this->items = $query->get();
        $this->companyDateFormat = $companyDateFormat;
    }

    public function view(): View
    {
        return view('exports.items', [
            'items' => $this->items,
            'companyDateFormat' => $this->companyDateFormat,
        ]);
    }
}

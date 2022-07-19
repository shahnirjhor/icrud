<?php

namespace App\Http\Controllers;

use Session;
use App\Models\Item;
use App\Exports\ItemsExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->export)
            return $this->doExport($request);
        $items = $this->filter($request)->paginate(10)->withQueryString();
        return view('items.index',compact('items'));
    }

    /**
     * Filter the item as user requerment
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    private function filter(Request $request)
    {
        $query = Item::latest();

        if ($request->name)
            $query->where('name', 'like', '%'.$request->name.'%');

        if ($request->sku)
            $query->where('sku', 'like', '%'.$request->sku.'%');

        if ($request->enabled > -1)
            $query->where('enabled', $request->enabled);

        return $query;
    }

    /**
     * Performs exporting
     *
     * @param Request $request
     * @return void
     */
    private function doExport(Request $request)
    {
        return Excel::download(new ItemsExport($request), 'items.xlsx');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('items.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validation($request);
        $data = $request->only(['name','sku','sale_price','purchase_price','quantity','enabled','description']);
        if ($request->picture) {
            $data['picture'] = $request->picture->store('item-images');
        }
        DB::transaction(function () use ($data) {
            Item::create($data);
        });

        return redirect()->route('item.index')->with('success', trans('Item Added Successfully'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function edit(Item $item)
    {
        return view('items.edit', compact('item'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Item $item)
    {
        $this->validation($request, $item->id);
        $data = $request->only(['name','sku','sale_price','purchase_price','quantity','enabled','description']);
        if ($request->picture) {
            $data['picture'] = $request->picture->store('item-images');
        }
        $item->update($data);
        return redirect()->route('item.index')->with('success', trans('Item Updated Successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function destroy(Item $item)
    {
        $item->delete();
        return redirect()->route('item.index')->with('success', trans('Item Deleted Successfully'));
    }

    /**
     * validate the specified data
     *
     * @param Request $request
     * @param integer $id
     * @return void
     */
    private function validation(Request $request, $id = 0)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'unique:items,sku,'.$id, 'max:255'],
            'sale_price' => ['required', 'numeric'],
            'purchase_price' => ['required', 'numeric'],
            'quantity' => ['required', 'numeric'],
            'description' => ['nullable', 'string', 'max:1000'],
            'enabled' => ['required', 'in:0,1'],
            'picture' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048']
        ]);
    }
}

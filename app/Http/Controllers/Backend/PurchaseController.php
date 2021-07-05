<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Product;
use App\Model\Supplier;
use App\Model\Unit;
use App\Model\Category;
use App\Model\Purchase;
use Auth;
use DB;

class PurchaseController extends Controller
{
    public function view()
    {
        $allData = Purchase::orderBy('date', 'desc')->orderBy('id', 'desc')->get();
        return view('backend.purchase.view-purchase', compact('allData'));
    }

    public function add()
    {
        $data['suppliers'] = Supplier::all();
        $data['categories']  = Category::all();
        $data['units']  = Unit::all();
        return view('backend.purchase.add-purchase', $data);
    }

    public function store(Request $request)
    {
        if ($request->category_id == null) {
            return redirect()->back()->with('error', 'Sorry! No Category is Selected');
        } else {
            $count_category = count($request->category_id);
            for ($i = 0; $i < $count_category; $i++) {
                $purchase = new Purchase();
                $purchase->date = date('Y-m-d', strtotime($request->date[$i]));
                $purchase->purchase_no = $request->purchase_no[$i];
                $purchase->supplier_id = $request->supplier_id[$i];
                $purchase->category_id = $request->category_id[$i];
                $purchase->product_id = $request->product_id[$i];
                $purchase->buy_qty = $request->buy_qty[$i];
                $purchase->unit_price = $request->unit_price[$i];
                $purchase->total_price = $request->total_price[$i];
                $purchase->description = $request->description[$i];
                $purchase->created_by = Auth::user()->id;
                $purchase->save();
            }
        }
        return redirect()->route('purchases.view')->with('success', 'Purchase Added Successfully');
    }

    public function pendingList(){
        $allData = Purchase::orderBy('date', 'desc')->orderBy('id', 'desc')->where('status', '0')->get();
        return view('backend.purchase.view-pending-list', compact('allData'));
    }

    public function approve($id){
        $purchase = Purchase::find($id);
        $product = Product::where('id', $purchase->product_id)->first();
        $purchase_qty = ((float)($purchase->buy_qty)) + ((float)($product->quantity));
        $product->quantity = $purchase_qty;
        if($product->save()){
            DB::table('purchases')->where('id', $id)->update(['status' => 1 ]);
        }
        return redirect()->route('purchases.pending.list')->with('success', 'Purchase Approved Successfully');
    }

    public function delete($id)
    {
        $purchase = Purchase::find($id);
        $purchase->delete();
        return redirect()->route('purchases.view')->with('success', 'Purchases Deleted Successfully');
    }
}

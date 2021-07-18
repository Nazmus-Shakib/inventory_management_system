<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Product;
use App\Model\Supplier;
use App\Model\Unit;
use App\Model\Category;
use Auth;
use PDF;

class StockController extends Controller
{
    public function stockReport()
    {
        $allData = Product::orderBy('supplier_id', 'asc')->orderBy('category_id', 'asc')->get();
        return view('backend.stock.stock-report', compact('allData'));
    }

    public function stockReportPDF()
    {
        $data['allData'] = Product::orderBy('supplier_id', 'asc')->orderBy('category_id', 'asc')->get();
        $pdf = PDF::loadView('backend.pdf.stock-invoice-pdf', $data);
        $pdf->SetProtection(['copy', 'print'], '', 'pass');
        return $pdf->stream('stock.pdf');
    }
}
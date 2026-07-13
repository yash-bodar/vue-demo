<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(Request $request){
        $query = Order::with('user');
        $query = $this->filterQuery($query, $request);
        $data = $query->paginate($request->per_page ?? 10);
        return response()->json(['success' => true,'data' => $data]);
    }

    public function filterQuery($query, $request){
        if($request->has('status') && $request->status){
            $query->where('status', $request->status);
        }
        if($request->has('payment_status') && $request->payment_status){
            $query->where('payment_status', $request->payment_status);
        }
        if($request->has('currency') && $request->currency){
            $query->where('currency', $request->currency);
        }
        if($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('status', 'like', '%' . $request->search . '%');
                $q->orWhere('payment_status', 'like', '%' . $request->search . '%');
                $q->orWhere('currency', 'like', '%' . $request->search . '%');
                $q->orWhere('created_at', 'like', '%' . $request->search . '%');
                $q->orWhereHas('user', function($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%');
                });
            });
        }
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        return $query;
    }

    public function getOrders(Request $request){
        $query = Order::where('user_id', $request->user_id ?? Auth::user()->id);
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        $orders = $query->paginate($request->per_page ?? 10);
        return response()->json(['success' => true,'data' => $orders]); 
    }

    public function getOrderDetail($id){
        $order = Order::where('id', $id)->with('user', 'address')->first();
        
        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found'], 404);
        }
        return response()->json(['success' => true, 'data' => $order]);
    }

    public function exportOrders(Request $request)
    {
        $query = Order::with('user');
        $query = $this->filterQuery($query, $request);
        $data = $query->get();
        
        if($request->type === 'pdf') {
            $pdf = Pdf::loadView('pdf.orders', compact('data'));
            return $pdf->download('orders.pdf');
        } elseif($request->type === 'csv') {
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="orders.csv"',
            ];
            
            $callback = function() use ($data) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['ID', 'User', 'No. of Products', 'Currency', 'Total', 'Status', 'Payment Status', 'Created At']);
                
                foreach ($data as $order) {
                    fputcsv($file, [
                        $order->id,
                        $order->user->name,
                        count($order->items),
                        $order->currency,
                        $order->total_amount,
                        $order->status,
                        $order->payment_status,
                        $order->created_at->format('Y-m-d'),
                    ]);
                }
                fclose($file);
            };
            
            return response()->stream($callback, 200, $headers);
        }
        return response()->json(['success' => true,'data' => $data]);
    }

    public function downloadInvoice($id)
    {
        $order = Order::where('id', $id)->with('user', 'address')->first();
        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found'], 404);
        }
        $pdf = Pdf::loadView('pdf.invoice', compact('order'));
        return $pdf->download('invoice.pdf');

    }

    public function updateStatus(Request $request, $id)
    {
        $order = Order::find($id);
        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found'], 404);
        }
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,completed,cancelled,refunded'
        ]);
        $order->status = $request->status;
        $order->save();

        return response()->json(['success' => true, 'message' => 'Order status updated successfully', 'data' => $order]);
    }

    public function updatePaymentStatus(Request $request, $id)
    {
        $order = Order::find($id);
        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found'], 404);
        }
        $request->validate([
            'payment_status' => 'required|in:paid,pending,cancelled'
        ]);
        $order->payment_status = $request->payment_status;
        $order->save();
 
        return response()->json(['success' => true, 'message' => 'Payment status updated successfully', 'data' => $order]);
    }

}

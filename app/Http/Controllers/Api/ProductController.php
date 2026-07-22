<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::with('category');
        $query = $this->filterQuery($query, $request);
        $data = $query->paginate($request->per_page ?? 10);
        return response()->json(['success' => true,'data' => $data]);
    }

    public function filterQuery($query, $request){
        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->has('currency') && $request->currency) {
            $query->where('currency', $request->currency);
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        if($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
                $q->orWhere('status', 'like', '%' . $request->search . '%');
                $q->orWhere('currency', 'like', '%' . $request->search . '%');
                $q->orWhereDate('created_at', 'like', '%' . $request->search . '%');
                $q->orWhereHas('category', function($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%');
                });
            });
        }
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        return $query;
    }

    public function show($id)
    {
        $product = Product::with(['category', 'wishlist', 'variants.size', 'variants.color'])->where('id', $id)->first();
        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found'], 404);
        }
        return response()->json(['success' => true, 'data' => $product]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'nullable|exists:products,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'currency' => 'required|string|max:3',
            'category_id' => 'required|exists:category,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 400);
        }

        if($request->id) {
            $product = Product::find($request->id);
            if(!$product) {
                return response()->json(['success' => false, 'message' => 'Product not found'], 404);
            }
            if ($request->hasFile('image')) {
                if ($product->image && Storage::disk('public')->exists($product->image)) {
                    Storage::disk('public')->delete($product->image);
                }
                $product->image = $request->file('image')->store('products', 'public');
            }
        } else {
            $product = new Product();
            if ($request->hasFile('image')) {
                $product->image = $request->file('image')->store('products', 'public');
            }
        }
        $product->name = $request->name;
        $product->price = $request->price;
        $product->stock = $request->stock;
        $product->description = $request->description;
        $product->status = $request->status;
        $product->currency = $request->currency;
        $product->category_id = $request->category_id;
        // Pre-validate SKUs across other products and check duplicate merging
        if ($request->has('variants')) {
            $variantsData = $request->variants;
            if (is_string($variantsData)) {
                $variantsData = json_decode($variantsData, true);
            }

            if (is_array($variantsData)) {
                $productId = $request->id ?? null;
                foreach ($variantsData as $variantItem) {
                    $sku = trim($variantItem['sku'] ?? '');
                    if ($sku !== '') {
                        $existingOtherProductVariant = ProductVariant::where('sku', $sku)
                            ->when($productId, function ($q) use ($productId) {
                                return $q->where('product_id', '!=', $productId);
                            })
                            ->first();

                        if ($existingOtherProductVariant) {
                            return response()->json([
                                'success' => false,
                                'message' => "The SKU '{$sku}' already exists in another product."
                            ], 422);
                        }
                    }
                }
            }
        }

        if($product->save()){
            // Handle variants saving with stock merge
            if ($request->has('variants')) {
                $variantsData = $request->variants;
                if (is_string($variantsData)) {
                    $variantsData = json_decode($variantsData, true);
                }

                if (is_array($variantsData)) {
                    $keepVariantIds = [];
                    foreach ($variantsData as $variantItem) {
                        if (empty($variantItem['name'])) {
                            continue;
                        }
                        
                        $vPrice = (isset($variantItem['price']) && $variantItem['price'] !== null && $variantItem['price'] !== '') ? $variantItem['price'] : null;
                        $sku = trim($variantItem['sku'] ?? '');

                        // Check if variant with same SKU exists under current product
                        $existingSameProductVariant = null;
                        if ($sku !== '') {
                            $existingSameProductVariant = $product->variants()
                                ->where('sku', $sku)
                                ->when(!empty($variantItem['id']), function ($q) use ($variantItem) {
                                    return $q->where('id', '!=', $variantItem['id']);
                                })
                                ->first();
                        }

                        if ($existingSameProductVariant) {
                            // Merge stock into existing variant
                            $existingSameProductVariant->stock += ($variantItem['stock'] ?? 0);
                            if ($vPrice !== null) {
                                $existingSameProductVariant->price = $vPrice;
                            }
                            $existingSameProductVariant->save();
                            $keepVariantIds[] = $existingSameProductVariant->id;
                        } else {
                            $variant = $product->variants()->updateOrCreate(
                                ['id' => $variantItem['id'] ?? null],
                                [
                                    'size_id' => $variantItem['size_id'] ?? null,
                                    'color_id' => $variantItem['color_id'] ?? null,
                                    'name' => $variantItem['name'],
                                    'price' => $vPrice,
                                    'stock' => $variantItem['stock'] ?? 0,
                                    'sku' => $sku !== '' ? $sku : null,
                                ]
                            );
                            $keepVariantIds[] = $variant->id;
                        }
                    }
                    
                    // Delete variants not sent in request
                    $product->variants()->whereNotIn('id', array_filter($keepVariantIds))->delete();
                }
            }
            return response()->json(['success' => true]);
        }else{
            return response()->json(['success' => false]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $product = Product::with(['variants.size', 'variants.color'])->select('id', 'name', 'price', 'stock', 'description','image','status','currency','category_id')->where('id', $id)->first();
        if(!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found'], 404);
        } else {
            return response()->json(['success' => true,'data' => $product]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $product = Product::find($id);
        if(!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found'], 404);
        }
        $product->delete();
        return response()->json(['success' => true]);
    }
    
    public function getProducts(Request $request)
    {
        $query = Product::with(['wishlist', 'variants.size', 'variants.color'])->where('status', 'Active');
        if($request->has('selectedCategory') && $request->selectedCategory){
            $query->where('category_id', $request->selectedCategory);
        }

        if($request->has('searchQuery') && $request->searchQuery){
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->searchQuery . '%');
                $q->orWhere('description', 'like', '%' . $request->searchQuery . '%');
            });
        }

        // Convert user currency filter value to base currency dynamically inside DB query
        $userCurrency = Auth::user() ? Auth::user()->currency : 'USD';
        $userRate = \App\Models\CurrencyRate::getRateToINR($userCurrency);

        if($request->has('minPrice') && $request->minPrice !== null && $request->minPrice !== ''){
            $query->where(function($q) use ($request, $userRate) {
                $q->whereRaw("(price * (select rate_to_inr from currency_rates where currency_code = products.currency limit 1) / ?) >= ?", [$userRate, $request->minPrice]);
            });
        }
        if($request->has('maxPrice') && $request->maxPrice !== null && $request->maxPrice !== ''){
            $query->where(function($q) use ($request, $userRate) {
                $q->whereRaw("(price * (select rate_to_inr from currency_rates where currency_code = products.currency limit 1) / ?) <= ?", [$userRate, $request->maxPrice]);
            });
        }

        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        $data = $query->paginate($request->per_page ?? 8);
        return response()->json(['success' => true,'data' => $data]);
    }

    public function exportProducts(Request $request)
    {
        $query = Product::with('category');
        $query = $this->filterQuery($query, $request);
        $data = $query->get();
        
        if($request->type === 'pdf') {
            $pdf = Pdf::loadView('pdf.products', compact('data'));
            return $pdf->download('products.pdf');
        } elseif($request->type === 'csv') {
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="products.csv"',
            ];
            
            $callback = function() use ($data) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['ID', 'Name', 'Description', 'Price', 'Currency', 'Stock', 'Status', 'Category', 'Created At']);
                
                foreach ($data as $product) {
                    fputcsv($file, [
                        $product->id,
                        $product->name,
                        $product->description,
                        $product->price,
                        $product->currency,
                        $product->stock,
                        $product->status,
                        $product->category ? $product->category->name : 'N/A',
                        $product->created_at->format('Y-m-d H:i:s'),
                    ]);
                }
                fclose($file);
            };
            
            return response()->stream($callback, 200, $headers);
        }
        return response()->json(['success' => true,'data' => $data]);
    }

}

<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Company;
use App\Http\Requests\ProductRequest;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
{
    try {
        $query = Product::query();

        if ($search = $request->search) {
            $query->where('product_name', 'LIKE', "%{$search}%");
        }

        if ($company_id = $request->company_id) {
            $query->where('company_id', $company_id);
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->filled('min_stock')) {
            $query->where('stock', '>=', $request->min_stock);
        }

        if ($request->filled('max_stock')) {
            $query->where('stock', '<=', $request->max_stock);
        }

        // Handle sorting
        $sortColumn = $request->get('sort', 'id'); // Default sort by ID
        $sortDirection = $request->get('direction', 'desc'); // Default direction descending
        $query->orderBy($sortColumn, $sortDirection);

        $products = $query->paginate(10);

        $companies = Company::all();

        if ($request->ajax()) {
            // 部分的なビューを返す
            return view('products.partials.product_list', compact('products'));
        }

        // 通常のリクエストの場合
        return view('products.index', compact('products', 'companies'));
    } catch (\Exception $e) {
        if ($request->ajax()) {
            // エラーが発生した場合のJSONレスポンス
            return response()->json(['error' => $e->getMessage()], 500);
        } else {
            // 通常リクエストのエラーハンドリング
            return redirect()->back()->withErrors($e->getMessage());
        }
    }
}





    public function create()
    {
        $companies = Company::all();
        return view('products.create', compact('companies'));
    }

    public function store(ProductRequest $request)
    {
        try {
            $product = new Product($request->validated());

            if ($request->hasFile('img_path')) {
                $filename = $request->img_path->getClientOriginalName();
                $filePath = $request->img_path->storeAs('products', $filename, 'public');
                $product->img_path = '/storage/' . $filePath;
            } else {
                $product->img_path = 'https://picsum.photos/200/300';
            }

            $product->save();

            return redirect()->route('products.index')->with('success', 'Product created successfully');
        } catch (\Exception $e) {
            \Log::error('Failed to create product: ', ['error' => $e->getMessage()]);
            return redirect()->route('products.index')->with('error', 'Failed to create product: ' . $e->getMessage());
        }
    }

    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $companies = Company::all();
        return view('products.edit', compact('product', 'companies'));
    }

    public function update(ProductRequest $request, Product $product)
{
    try {
        $validatedData = $request->validated();

        if ($request->hasFile('img_path')) {
            if ($product->img_path) {
                \Storage::disk('public')->delete(str_replace('/storage/', '', $product->img_path));
            }

            $filename = $request->img_path->getClientOriginalName();
            $filePath = $request->img_path->storeAs('products', $filename, 'public');
            $validatedData['img_path'] = '/storage/' . $filePath;
        } else {
            unset($validatedData['img_path']);
        }

        $product->fill($validatedData);
        $product->save();

        return redirect()->route('products.index')->with('success', 'Product updated successfully');
    } catch (\Exception $e) {
        \Log::error('Failed to update product: ', ['error' => $e->getMessage()]);
        return redirect()->route('products.index')->with('error', 'Failed to update product: ' . $e->getMessage());
    }
}


public function destroy(Product $product)
{
    try {
        $product->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }
        \Log::info('リクエスト詳細: ', [
            'is_ajax' => request()->ajax(),
            'headers' => request()->headers->all(),
            'method' => request()->method(),
            'url' => request()->url(),
        ]);
        

        if (request()->ajax()) {
            \Log::info('AJAXリクエストが認識されました');
        } else {
            \Log::info('通常のリクエストとして処理されました');
        }
        

        // This part will only be executed if the request is not AJAX
        return redirect()->route('products.index')->with('success', 'Product deleted successfully');
    } catch (\Exception $e) {
        \Log::error('Failed to delete product: ', ['error' => $e->getMessage()]);

        if (request()->ajax()) {
            return response()->json(['success' => false, 'error' => '商品の削除に失敗しました。'], 500);
        }

        // This part will only be executed if the request is not AJAX
        return redirect()->route('products.index')->with('error', 'Failed to delete product: ' . $e->getMessage());
    }
}

}

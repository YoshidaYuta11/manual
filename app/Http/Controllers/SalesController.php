<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{
    public function purchase(Request $request)
    {
        $productId = $request->input('product_id');
        $quantity = $request->input('quantity');

        // トランザクションの開始
        DB::beginTransaction();

        try {
            $product = Product::find($productId);

            // 在庫が足りない場合はエラーを返す
            if ($product->stock < $quantity) {
                return response()->json([
                    'success' => false,
                    'message' => '在庫が不足しています。',
                ], 400);
            }

            // salesテーブルにレコードを追加
            Sale::create([
                'product_id' => $productId,
                'quantity' => $quantity,
            ]);

            // productsテーブルの在庫数を減算
            $product->stock -= $quantity;
            $product->save();

            // トランザクションのコミット
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => '購入が完了しました。',
            ]);

        } catch (\Exception $e) {
            // トランザクションのロールバック
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => '購入処理中にエラーが発生しました。',
            ], 500);
        }
    }
}


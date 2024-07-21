<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'product_name' => 'required|string|max:255',
            'company_id' => 'required|exists:companies,id',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'comment' => 'nullable|string|max:1000',
            'img_path' => 'nullable|image|max:2048', 
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'product_name.required' => '商品名は必須です。',
            'product_name.string' => '商品名は文字列でなければなりません。',
            'product_name.max' => '商品名は255文字以内でなければなりません。',
            'company_id.required' => '会社IDは必須です。',
            'company_id.exists' => '選択した会社IDは無効です。',
            'price.required' => '価格は必須です。',
            'price.numeric' => '価格は数値でなければなりません。',
            'price.min' => '価格は0以上でなければなりません。',
            'stock.required' => '在庫は必須です。',
            'stock.integer' => '在庫は整数でなければなりません。',
            'stock.min' => '在庫は0以上でなければなりません。',
            'comment.string' => 'コメントは文字列でなければなりません。',
            'comment.max' => 'コメントは1000文字以内でなければなりません。',
            'img_path.image' => '画像は有効な画像ファイルでなければなりません。',
            'img_path.max' => '画像ファイルのサイズは2048KB以下でなければなりません。',
        ];
    }
}

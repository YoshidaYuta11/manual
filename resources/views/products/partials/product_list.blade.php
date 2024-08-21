<div id="product-list" class="products mt-5">
    <h2>商品情報</h2>
    <table class="table table-striped tablesorter">
        <thead>
            <tr>
                <th>ID</th>
                <th>商品名</th>
                <th>メーカー</th>
                <th>価格</th>
                <th>在庫数</th>
                <th>コメント</th>
                <th>商品画像</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($products as $product)
            <tr>
                <td>{{ $product->id }}</td>
                <td>{{ $product->product_name }}</td>
                <td>{{ $product->company->company_name }}</td>
                <td>{{ $product->price }}</td>
                <td>{{ $product->stock }}</td>
                <td>{{ $product->comment }}</td>
                <td>
                    @if (filter_var($product->img_path, FILTER_VALIDATE_URL))
                        <img src="{{ $product->img_path }}" alt="商品画像" width="100">
                    @else
                        <img src="{{ asset($product->img_path) }}" alt="商品画像" width="100">
                    @endif
                </td>
                <td>
                    <a href="{{ route('products.show', $product) }}" class="btn btn-info btn-sm mx-1">詳細表示</a>
                    <form method="POST" action="{{ route('products.destroy', $product) }}" class="delete-form" data-id="{{ $product->id }}">
    @csrf
    @method('DELETE')
    <button class="btn btn-danger btn-sm mx-1" type="submit" data-id="{{ $product->id }}">削除</button>
</form>

                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8">商品が見つかりませんでした。</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

{{ $products->links() }}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tablesorter@2.31.3/dist/js/jquery.tablesorter.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.3/js/jquery.tablesorter.min.js"></script>
<script>
    $(document).ready(function() {
        // tablesorterの初期化
        $(".tablesorter").tablesorter({
            sortList: [[0, 1]],  // 初期ソート順をIDの降順に設定
            headers: {
                // 操作列（最後の列）はソート不可
                7: { sorter: false }
            }
        });
    });
</script>

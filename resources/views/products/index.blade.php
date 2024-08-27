@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">商品情報一覧</h1>

    <a href="{{ route('products.create') }}" class="btn btn-primary mb-3">商品新規登録</a>

    <form id="search-form" method="GET" action="{{ route('products.index') }}">
        @csrf
        <div class="mb-3">
            <label for="search" class="form-label">商品名で検索:</label>
            <input type="text" id="search" name="search" class="form-control" value="{{ request('search') }}">
        </div>

        <div class="mb-3">
            <label for="company_id" class="form-label">メーカーで検索:</label>
            <select class="form-select" id="company_id" name="company_id">
                <option value="">全てのメーカー</option>
                @foreach($companies as $company)
                    <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>{{ $company->company_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="min_price" class="form-label">最小価格:</label>
            <input type="number" id="min_price" name="min_price" class="form-control" value="{{ request('min_price') }}">
        </div>

        <div class="mb-3">
            <label for="max_price" class="form-label">最大価格:</label>
            <input type="number" id="max_price" name="max_price" class="form-control" value="{{ request('max_price') }}">
        </div>

        <div class="mb-3">
            <label for="min_stock" class="form-label">最小在庫数:</label>
            <input type="number" id="min_stock" name="min_stock" class="form-control" value="{{ request('min_stock') }}">
        </div>

        <div class="mb-3">
            <label for="max_stock" class="form-label">最大在庫数:</label>
            <input type="number" id="max_stock" name="max_stock" class="form-control" value="{{ request('max_stock') }}">
        </div>

        <button type="submit" class="btn btn-primary">検索</button>
    </form>

    @if ($errors->any())
        <div class="alert alert-danger mt-3">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success mt-3">
            {{ session('success') }}
        </div>
    @endif

    <div id="product-list" class="products mt-5">
        <h2>商品情報</h2>
        <table class="table table-striped tablesorter">
            <thead>
                <tr>
                    <th><a href="?sort=id&direction={{ request('direction') == 'asc' ? 'desc' : 'asc' }}">ID</a></th>
                    <th><a href="?sort=product_name&direction={{ request('direction') == 'asc' ? 'desc' : 'asc' }}">商品名</a></th>
                    <th><a href="?sort=company_name&direction={{ request('direction') == 'asc' ? 'desc' : 'asc' }}">メーカー</a></th>
                    <th><a href="?sort=price&direction={{ request('direction') == 'asc' ? 'desc' : 'asc' }}">価格</a></th>
                    <th><a href="?sort=stock&direction={{ request('direction') == 'asc' ? 'desc' : 'asc' }}">在庫数</a></th>
                    <th>コメント</th>
                    <th>商品画像</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                    <tr id="product-{{ $product->id }}">
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
                            <form method="POST" action="{{ route('products.destroy', $product) }}" class="delete-form">
    @csrf
    @method('DELETE')
    <button class="btn btn-danger btn-sm mx-1" type="submit">削除</button>
</form>



                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{ $products->links() }}
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tablesorter@2.31.3/dist/js/jquery.tablesorter.min.js"></script>



<script>

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});


    $(document).ready(function() {
    // tablesorterの初期化
    $(".tablesorter").tablesorter({
        sortList: [[0, 1]],  // 初期ソート順をIDの降順に設定
        headers: {
            7: { sorter: false }  // 操作列（最後の列）はソート不可
        }
    });

    $(document).on('click', 'th a', function(e) {
    e.preventDefault();

    var url = $(this).attr('href');

    $.ajax({
        url: url,
        type: 'GET',
        success: function(response) {
            // テーブル部分だけを更新するコード
            $('#table-container').html($(response).find('#table-container').html());
        },
        error: function() {
            alert('データの取得に失敗しました。');
        }
    });
});

    // 検索フォームの非同期送信処理
    $('#search-form').on('submit', function(e) {
        e.preventDefault(); // フォームのデフォルト動作（ページリロード）をキャンセル
        var form = $(this);
        var url = form.attr('action');
        var formData = form.serialize();

        $.ajax({
            url: url,
            type: 'GET',
            data: formData,
            success: function(response) {
                // 商品一覧を更新
                
                $('#product-list').html(response);
                // テーブルソートを再初期化
                $(".tablesorter").tablesorter();
            },
            error: function(xhr) {
                console.log(xhr.responseText);
                alert('検索に失敗しました。');
            }
        });
    });

    // 非同期削除処理
    $(document).on('submit', '.delete-form', function(e) {
    e.preventDefault();
    console.log('削除フォームが送信されました。');

    if (!confirm('この商品を削除しますか？')) {
        return;
    }

    var form = $(this);
    var url = form.attr('action'); // action属性からURLを取得
    console.log('AJAXリクエストを送信します:', url);

    $.ajax({
        url: url,
        type: 'DELETE',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        },
        data: {
            _token: form.find('input[name="_token"]').val(),
        },
        success: function(response) {
            console.log('AJAXリクエスト成功:', response);
            if (response.success) {
                form.closest('tr').remove(); // テーブルの行を削除
                alert('商品が削除されました。');
            } else {
                alert('削除に失敗しました。');
            }
        },
        error: function(xhr) {
            console.log('AJAXリクエスト失敗:', xhr.responseText);
            alert('削除に失敗しました。');
        }
    });
});

});

</script>

    
    
            </div>
            
        </div>
        
    </div>
   
</div>




@endsection

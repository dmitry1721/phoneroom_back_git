@extends('layouts.admin')

@section('content')
    <div>
        <h2 class="pt-2 mb-3">Мой склад</h2>
        <hr>
        <div>
            <div class="card card-primary">
{{--                <div class="card-header text-lg">Подключение к аккаунту</div>--}}
                <div class="card-body d-flex justify-content-between">
                    <div class="col-4">
                        <a href="#" disabled class="w-100 mt-3 active btn btn-outline-dark">Экспорт товаров</a>
                        <hr>
{{--                        <a href="#" disabled class="w-100 btn btn-outline-dark">Экспорт товаров</a>--}}
{{--                        <hr>--}}
{{--                        <a href="#" disabled class="w-100 btn btn-outline-dark">Экспорт товаров</a>--}}
{{--                        <hr>--}}
{{--                        <a href="#" disabled class="w-100 btn btn-outline-dark">Экспорт товаров</a>--}}
                    </div>
                    <div class="col-7 offset-1 text-end">
                        <form action="{{ route('admin.my-warehouse.export') }}" method="post" class="pt-3 my-login-validation" enctype="multipart/form-data">
                            @csrf
                            <h2 class="text-lg text-bold mb-3">Не экспортированные товары:</h2>
                            <table id="example2" class="table table-responsive-lg table-hover dataTable dtr-inline" aria-describedby="example2_info">
                                <thead class="thead thead">
                                <tr>
                                    <th scope="col">
                                        Выберите до 10 товаров
                                    </th>
                                    <th scope="col">ID</th>
                                    <th scope="col">Название</th>
                                    <th scope="col">Просмотр</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($products as $product)
                                    <tr>
                                        <th>
                                            <input style="cursor: pointer" type="checkbox" name="ids[{{ $product->id }}]">
                                        </th>
                                        <th>{{ $product->id }}</th>
                                        <th>{{ $product->name }}</th>
                                        <th>
                                            <a href="{{ route('admin.products.show', $product->slug) }}"><i class="fa fa-solid fa-eye"></i></a>
                                        </th>
                                        {{--                                    <th>{{ $product->created_at }}</th>--}}
                                        {{--                                    <th>{{ $product->updated_at ?? 'Изменений нет' }}</th>--}}
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                {{--                            <tr>--}}
                                {{--                                <th rowspan="1" colspan="1">ID</th>--}}
                                {{--                                <th rowspan="1" colspan="1">Название</th>--}}
                                {{--                                <th rowspan="1" colspan="1">Просмотр</th>--}}
                                {{--                                <th rowspan="1" colspan="1">Дата создания</th>--}}
                                {{--                                <th rowspan="1" colspan="1">Дата изменения</th>--}}
                                {{--                            </tr>--}}
                                </tfoot>
                            </table>
                            <div class="form-group mt-3">
                                <button type="submit" class="btn w-100 btn-danger btn-block">
                                    Экспортировать
                                </button>
                            </div>
                        </form>
                        <div class="row pt-3">
                            {{ $products->withQueryString()->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
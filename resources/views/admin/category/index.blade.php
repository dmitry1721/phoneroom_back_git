@extends('layouts.admin')

@section('content')

    <div>
        <h2 class="pt-2 mb-3">Категории:</h2>
        <div class="row w-100 pt-2 justify-content-between">
            <div class="col">
                <a class="btn d-inline-block btn-primary" href="{{ route('admin.categories.create') }}">Добавить категорию</a>
            </div>
        </div>
        <hr>
        <div class="col-12 col-lg-10">
            <table id="example2" class="table table-responsive-lg table-hover dataTable dtr-inline" aria-describedby="example2_info">
                <thead class="thead thead">
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Название</th>
                    <th scope="col">Дата создания</th>
                    <th scope="col">Дата изменения</th>
                    <th scope="col">Действия</th>
                </tr>
                </thead>
                <tbody>
                @foreach($categories as $category)
                    <tr>
                        <th scope="col">{{ $category->id }}</th>
                        <th scope="col">{{ $category->name }}</th>
                        <th scope="col">{{ $category->created_at }}</th>
                        <th scope="col">{{ $category->updated_at ?? 'Изменений нет' }}</th>
                        <th scope="col">
                            <a href="{{ route('admin.categories.show', $category->slug) }}"><i class="fa fa-solid fa-eye"></i></a>
                            <a class="pl-md-5 pr-md-5 pl-3 pr-3" href="{{ route('admin.categories.edit', $category->slug) }}"><i class="fa fa-solid fa-pen"></i></a>
                            <form class="m-0 p-0 d-inline-block" action="{{ route('admin.categories.destroy', $category->slug) }}" method="post">
                                @csrf
                                @method('delete')
                                <button class="btn btn-danger" type="submit"><i class="fa fa-solid fa-trash"></i></button>
                            </form>
                        </th>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th rowspan="1" colspan="1">ID</th>
                        <th rowspan="1" colspan="1">Название</th>
                        <th rowspan="1" colspan="1">Дата создания</th>
                        <th rowspan="1" colspan="1">Дата изменения</th>
                        <th rowspan="1" colspan="1">Действия</th>
                    </tr>
                </tfoot>
            </table>

            <div class="row pt-5 pb-2">
                {{ $categories->withQueryString()->links() }}
            </div>
        </div>
    </div>
@endsection
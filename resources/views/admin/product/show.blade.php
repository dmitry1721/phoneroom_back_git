@extends('layouts.admin')
@section('content')
    <h1 class="pt-2 mb-3">{{ $variant['product_name'] ?? $product->name }}</h1>
    <hr>
    <div class="card card-solid">
        <div class="card-body mt-2">
            <div class="row">
                <div class="col-12 col-sm-6">
                    <div class="col-12">
                        <img  src="{{ asset($variant['image'] ?? asset($product->image)) }}" class="product-image" alt="Product Image">
                    </div>
                    <div class="col-12 product-image-thumbs justify-content-center">
                        <div class="product-image-thumb" style="cursor: pointer;"><img  src="{{ asset($variant['image'] ?? asset($product->image)) }}" alt="Product Image"></div>
                    @foreach($product->images()->orderBy('position')->get() as $img)
                        @if($variant)
                            @if($img->variant_id === (int)$variant['id'] && $img->path !== '' )
                                <div class="product-image-thumb" style="cursor: pointer;"><img src="{{ asset($img->path) }}" alt="Product Image"></div>
                            @endif
                        @else
                            @if($img->variant_id === null && $img->path !== '')
                                <div class="product-image-thumb" style="cursor: pointer;"><img src="{{ asset($img->path) }}" alt="Product Image"></div>
                            @endif
                        @endif
                    @endforeach
                    </div>
                </div>
                <div class="col-12 col-sm-6">
                    <img  src="{{ asset($product->brand->image) }}" width="214" height="74" class="brand-image" alt="Brand Image">
                    <h4 class="mt-2">Рейтинг: {{ $product->rating }}</h4>
                    <hr>
                    <h4>Теги:</h4>
                    @foreach($product->tags as $pt)
                        <div class="btn-group mt-2 col btn-group-toggle" data-toggle="buttons">
                            <label class="text-center">
                                @if($pt->image)
                                    <img  src="{{ asset($pt->image) }}" class="brand-image pr-1" alt="Tag Image">
                                @endif
                                {{ $pt->name }}
                            </label>
                        </div>
                    @endforeach
                    @if(isset($product->option) && empty($variant) !== true)
                        @foreach(json_decode($product->option->options_json, true) as $option)
                            @foreach($option as $key => $val)
                                @if(!is_array($val))
                                    <h4 class="mt-4">{{ $val }}:</h4>
                                @else
                                    @if(isset($option[$key]['colors']))
                                        @for($i=0; $i<count($option[$key])-1; $i++)
                                            <div class="btn-group mt-2 btn-group-toggle" data-toggle="buttons">
                                                @if(!is_array($option[$key][$i]))
                                                    @if(str_contains($variant['name'], $option[$key][$i]))
                                                        <label class="btn btn-default text-center border-dark">
                                                            <input type="radio" name="color_option" id="color_option_a1" autocomplete="off" checked="">
                                                            {{ $option[$key][$i] }}
                                                            <br>
                                                            <i class="fas fa-circle fa-2x" style="color:{{ $option[$key]['colors'][$i] }};"></i>
                                                        </label>
                                                    @else
                                                        <label class="btn btn-default text-center">
                                                            <input type="radio" name="color_option" id="color_option_a1" autocomplete="off" checked="">
                                                            {{ $option[$key][$i] }}
                                                            <br>
                                                            <i class="fas fa-circle fa-2x" style="color: {{ $option[$key]['colors'][$i] }};"></i>
                                                        </label>
                                                    @endif
                                                @endif
                                            </div>
                                        @endfor
                                    @else
                                        @foreach($val as $v)
                                            @if(str_contains($variant['name'], $v))
                                                <div class="btn-group mt-2 btn-group-toggle" data-toggle="buttons">
                                                    <label class="btn btn-default text-center border-dark">
                                                        <input type="radio" name="color_option" id="color_option_b1" autocomplete="off">
                                                        {{ $v }}
                                                    </label>
                                                </div>
                                            @else
                                                <div class="btn-group mt-2 btn-group-toggle" data-toggle="buttons">
                                                    <label class="btn btn-default text-center">
                                                        <input type="radio" name="color_option" id="color_option_b1" autocomplete="off">
                                                        {{ $v }}
                                                    </label>
                                                </div>
                                            @endif
                                        @endforeach
                                    @endif
                                @endif
                            @endforeach
                        @endforeach
                    @endif
                    <div class="bg-gray py-2 px-3 mt-4">
                        <h2 class="mb-0">
                            {{ $variant['price'] ?? $product->price }} ₽
                        </h2>
                        @if(isset($product->old_price) or isset($variant['old_price']))
                        <h4 class="mt-0">
                            <small style="text-decoration-line: line-through;">{{ $variant['old_price'] ?? $product->old_price }} ₽</small>
                        </h4>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <nav class="w-100">
                    <div class="nav nav-tabs" id="product-tab" role="tablist">
                        <a class="nav-item nav-link active" id="product-desc-tab" data-toggle="tab" href="#product-desc" role="tab" aria-controls="product-desc" aria-selected="true">Описание</a>
                        <a class="nav-item nav-link" id="product-properties-tab" data-toggle="tab" href="#product-properties" role="tab" aria-controls="product-properties" aria-selected="false">Характеристики</a>
                        <a class="nav-item nav-link" id="product-comments-tab" data-toggle="tab" href="#product-comments" role="tab" aria-controls="product-comments" aria-selected="false">Отзывы</a>
                    </div>
                </nav>
                <div class="tab-content p-3" id="nav-tabContent">
                    <div class="tab-pane fade active show" id="product-desc" role="tabpanel" aria-labelledby="product-desc-tab" style="white-space: pre-line;">
                        {{ $variant['description'] ?? $product->description  }}
                    </div>
                    <div class="tab-pane fade" id="product-properties" role="tabpanel" aria-labelledby="product-properties-tab">
                        @if($variant['country'] ?? $product->country)
                            <span class="text-md d-block mb-2">Страна - {{ $variant['country'] ?? $product->country }}</span>
                        @endif
                        @if($product->vat)
                            @if($product->vat === -1)
                                <span class="text-md d-block mb-2">НДС % - без НДС</span>
                            @else
                                <span class="text-md d-block mb-2">НДС % - {{ $product->vat }}</span>
                            @endif
                        @endif
                        @if($variant['min_price'] ?? $product->min_price)
                            <span class="text-md d-block mb-2">Минимальная цена - {{ $variant['min_price'] ?? $product->min_price }}</span>
                        @endif
                        @if($variant['purchase_price'] ?? $product->purchase_price)
                            <span class="text-md d-block mb-2">Закупочная цена - {{ $variant['purchase_price'] ?? $product->purchase_price }}</span>
                        @endif
                        @if($variant['min_balance'] ?? $product->min_balance)
                            <span class="text-md d-block mb-3">Неснижаемый остаток - {{ $variant['min_balance'] ?? $product->min_balance }}</span>
                        @endif
                        <ul>
                            @if(isset($product->property))
                                @foreach(json_decode($product->property->properties_json) as $property => $val)
                                    @if(!is_object($val))
                                        <li><strong>{{ $val }}</strong></li>
                                    @else
                                        @foreach($val as $k => $v)
                                            <li>{{ $k }}: {{ $v }}</li>
                                        @endforeach
                                    @endif
                                @endforeach
                            @endif
                        </ul>
                    </div>
                    @if(count($comments) !== 0)
                        <div class="tab-pane fade" id="product-comments" role="tabpanel" aria-labelledby="product-comments-tab">
                            @foreach($comments as $comment)
                                @if($comment->type === 0)
                                    <div class="card-wrapper d-inline-block">
                                        <div class="card card-info">
                                            <div class="card-header">
                                                <div class="d-flex justify-content-between">
                                                    <h6 class="card-title">{{ ($comment->user->last_name ?? "").' '.$comment->user->first_name.' '.($comment->user->middle_name ?? "") }}</h6>
                                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                    <h6 class="card-title">{{ $comment->created_at }}</h6>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="container">
                                                    <h6>
                                                        <b>Достоинства: </b>
                                                        {{ $comment->advantages ?? "-" }}
                                                    </h6>
                                                    <h6>
                                                        <b>Недостатки: </b>
                                                        {{ $comment->disadvantages ?? "-" }}
                                                    </h6>
                                                    <p>
                                                        <b>Отзыв:  </b>
                                                        {{ $comment->comment ?? "-" }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="card-footer">
                                                <a  href="{{ route('admin.comments.create', $comment->id) }}">Ответить</a>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        Отзывы отсутствуют
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection









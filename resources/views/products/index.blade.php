@extends('layouts.app')

@section('content')

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Products</h1>
    </div>

    @if(Session::has('success'))
<p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('success') }}</p>
@endif


    <div class="card">
        <form action="{{route('product.index')}}" method="get" class="card-header">
            <div class="form-row justify-content-between">
                <div class="col-md-2">
                    <input type="text" name="title" placeholder="Product Title" class="form-control" value="{{app('request')->input('title')??''}}">
                </div>
                <div class="col-md-2">
                    <select name="variant" id="" class="form-control">
                        <option value="">Select Varient</option>
                        @php
                            $variant = app('request')->input('variant')??'';
                        @endphp
                        @foreach($variants as $key=>$val)
                        <optgroup label="{{substr($key,1,-1)}}">
                            @foreach($val as $v)
                                <option value="{{$v->id}}" {{$variant==$v->id?"selected":''}}>{{$v->variant}}</option>
                            @endforeach
                       </optgroup>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Price Range</span>
                        </div>
                        <input type="text" name="price_from" aria-label="First name" placeholder="From" class="form-control" value="{{app('request')->input('price_from')??''}}">
                        <input type="text" name="price_to" aria-label="Last name" placeholder="To" class="form-control" value="{{app('request')->input('price_to')??''}}">
                    </div>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date" placeholder="Date" class="form-control" value="{{app('request')->input('date')??''}}">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary float-right"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </form>

        <div class="card-body">
            <div class="table-response">
                <table class="table">
                    <thead>
                    <tr>
                        <th>SL</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Variant</th>
                        <th width="150px">Action</th>
                    </tr>
                    </thead>

                    <tbody>

                       @foreach($products as $product)

                        <tr>
                            <td>{{$product->id}}</td>
                           
                            <td class="col-sm-2">{{$product->title}} <br>
                                {{Carbon\Carbon::parse($product->created_at)->diffForHumans()}}
                            </td>
                            <td class="col-sm-4">{{$product->description}}</td>
                            <td>
                                
                                <dl class="row mb-0" style="height: 80px; overflow: hidden" id="variant">
                                    @if(isset($product_variants[$product->id]))
                                    @foreach($product_variants[$product->id] as $v)
                                    <dt class="col-sm-3 pb-0">
                                        <span>{{$v->variant_one}}</span>
                                            <span>/</span>
                                            <span>{{$v->variant_two}}</span>
                                            <span>/</span>
                                            <span>{{$v->variant_three}}</span>
                                    </dt>
                                    <dd class="col-sm-9">
                                        <dl class="row mb-0">
                                            <dt class="col-sm-4 pb-0">Price:<span style="margin: 0 5px;padding:0 5px;border: 1px solid #ddd">{{$v->price}}</span></dt>
                                            <dd class="col-sm-8 pb-0">InStock : <span style="margin: 0 5px;padding: 0 5px;border: 1px solid #ddd">{{$v->stock}}</dd>
                                        </dl>
                                    </dd>
                                    @endforeach
                                    @endif
                                </dl>
                                
                                
                                <button onclick="$('#variant').toggleClass('h-auto')" class="btn btn-sm btn-link">Show more</button>
                            </td>
                            <td>
                                <a href="{{route('product.edit',$product->id)}}" class="btn btn-primary">Edit</a>
                            </td>
                            
                        </tr>

                       @endforeach

                    <!-- <tr>
                        <td>1</td>
                        <td>T-Shirt <br> Created at : 25-Aug-2020</td>
                        <td>Quality product in low cost</td>
                        <td>
                            <dl class="row mb-0" style="height: 80px; overflow: hidden" id="variant">

                                <dt class="col-sm-3 pb-0">
                                    SM/ Red/ V-Nick
                                </dt>
                                <dd class="col-sm-9">
                                    <dl class="row mb-0">
                                        <dt class="col-sm-4 pb-0">Price : {{ number_format(200,2) }}</dt>
                                        <dd class="col-sm-8 pb-0">InStock : {{ number_format(50,2) }}</dd>
                                    </dl>
                                </dd>
                            </dl>
                            <button onclick="$('#variant').toggleClass('h-auto')" class="btn btn-sm btn-link">Show more</button>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('product.edit', 1) }}" class="btn btn-success">Edit</a>
                            </div>
                        </td>
                    </tr> -->

                    </tbody>

                </table>
                @if(method_exists($products,'links'))
                {{ $products->links() }}
                @endif
            </div>

        </div>

        <div class="card-footer">
            <div class="row justify-content-between">
                <div class="col-md-6">
                    <p>
                        @php
                        $curPage = app('request')->input('page')??1;
                        $start = (((int)$curPage-1)*(int)$productper_page)+1;;
                        $end = (int)$curPage*(int)$productper_page;
                        $end = $products_count<$end?$products_count:$end;
                        @endphp

                        Showing {{$start}} to {{$end}} out of
                        {{$products_count}}
                    </p>
                </div>
                <div class="col-md-2">

                </div>
            </div>
        </div>
    </div>

@endsection

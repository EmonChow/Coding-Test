<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductVariantPrice;
use App\Models\Variant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Session;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index()
    {


        $productPerPage = 5;
        $data['products'] = $this->searchProduct()?:Product::orderBy('id','asc')->paginate($productPerPage);
        $data['products_count'] = Product::count();
        $data['productper_page'] = $productPerPage; 

        $product_variants_data = DB::table('product_variant_prices')
        ->leftJoin('product_variants as variant_one','variant_one.id','product_variant_prices.product_variant_one')
        ->leftJoin('product_variants as variant_two','variant_two.id','product_variant_prices.product_variant_two')
        ->leftJoin('product_variants as variant_three','variant_three.id','product_variant_prices.product_variant_three')        
        ->select('product_variant_prices.*','variant_one.variant as variant_one','variant_two.variant as variant_two','variant_three.variant as variant_three')
        ->get()->toArray();

        $data['product_variants'] = array_reduce($product_variants_data,function($return,$data){
            $return[$data->product_id][] = $data;
            return $return;
       });

        
        $variants = DB::Select("SELECT product_variants.id,variant,variants.title from product_variants left join variants on variants.id=product_variants.variant_id WHERE product_variants.id in ( SELECT DISTINCT product_variant_one FROM product_variant_prices UNION SELECT DISTINCT product_variant_two FROM product_variant_prices UNION SELECT DISTINCT product_variant_three FROM product_variant_prices) ");

        $data['variants'] = array_reduce($variants,function($return,$data){
            $return["'".$data->title."'"][] = $data;
            return $return;
        });

        return view('products.index',$data);
    }




    private function searchProduct(){
        $title = request()->title;
        $variant = request()->variant;
        $price_from = request()->price_from;
        $price_to = request()->price_to;
        $date = request()->date;        
        $atleastonce = false;

        $getProductId = DB::table("products");

        if(($price_from && $price_to) || $variant){
            $getProductId->leftJoin("product_variant_prices","product_variant_prices.product_id","products.id");
            $getProductId->leftJoin('product_variants as variant_one','variant_one.id','product_variant_prices.product_variant_one')
            ->leftJoin('product_variants as variant_two','variant_two.id','product_variant_prices.product_variant_two')
            ->leftJoin('product_variants as variant_three','variant_three.id','product_variant_prices.product_variant_three');
            if(($price_from && $price_to))
            $getProductId->whereBetween('price',[$price_from,$price_to]);
            $getProductId->where(function ($query) use ($variant) {
                $query->where('product_variant_one', '=', $variant)
                      ->orWhere('product_variant_two', '=', $variant)
                      ->orWhere('product_variant_three', '=', $variant);
            });
            $atleastonce = true;
        }

        if($title){
            $getProductId->where('title','like',"%$title%");
            $atleastonce = true;
        }
        if($date){
            $getProductId->where(DB::raw('DATE(products.created_at)'),$date);
            $atleastonce = true;
        }
        if($atleastonce){
            $getProductId = $getProductId->select(DB::raw("Distinct products.id"))->get();
            $ids = array_column($getProductId->toArray(),'id');
            return DB::table('products')->whereIn('id',$ids)->get();
        }
        
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        $variants = Variant::all();
        return view('products.create', compact('variants'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {

    }


    /**
     * Display the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function show($product)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        $variants = Variant::all();
        return view('products.edit', compact('variants'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        //
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function index()
    {
        $products = DB::table('products')->get();

        $filteredProducts = [];

        foreach ($products as $product) {
            $attributes = DB::table('attribute_values')
                ->join('attributes', 'attribute_values.attribute_id', '=', 'attributes.id')
                ->where('attribute_values.entity_id', $product->id)
                ->where('attributes.entity_type', 'products')
                ->select('attribute_values.*', 'attributes.name as attribute_name')
                ->get();

            $isActive = $attributes->first(function ($attr) {
                return $attr->attribute_name === 'Activo' && $attr->integer_value == 1;
            });

            if ($isActive) {
                $product->attributes = $attributes;
                $filteredProducts[] = $product;
            }
        }

        return response()->json($filteredProducts);
    }


    public function filter($id)
    {
        $products = DB::table('products')->get();

        $filteredProducts = [];

        foreach ($products as $product) {
            $attributes = DB::table('attribute_values')
                ->join('attributes', 'attribute_values.attribute_id', '=', 'attributes.id')
                ->where('attribute_values.entity_id', $product->id)
                ->where('attributes.entity_type', 'products')
                ->select('attribute_values.*', 'attributes.name as attribute_name')
                ->get();

            // Verifica se o produto está ativo
            $isActive = $attributes->first(function ($attr) {
                return $attr->attribute_name === 'Activo' && $attr->integer_value == 1;
            });

            // Verifica se o produto tem a categoria correspondente ao $id
            $hasCategory = $attributes->first(function ($attr) use ($id) {
                return $attr->attribute_name === 'categoría de producto' && $attr->integer_value == $id;
            });

            if ($isActive && $hasCategory) {
                $product->attributes = $attributes;
                $filteredProducts[] = $product;
            }
        }

        return response()->json($filteredProducts);
    }
}

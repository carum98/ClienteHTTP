<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Return welcome Page
     * @return \Illuminate\Http\Response
     */
    public function showProduct($title, $id)
    {
        $product = $this->marketService->getProduct($id);

        return view('products.show')->with([
            'product' => $product,
        ]);
    }
}

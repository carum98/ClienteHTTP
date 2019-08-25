<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CategoryProductController extends Controller
{
    /** Return welcome Page
    * @return \Illuminate\Http\Response
    */

   public function showProducts($title, $id)
   {
       $products = $this->marketService->getCategoryProduct($id);

       return view('categories.products.show')->with([
           'products' => $products,
       ]);
   }
}

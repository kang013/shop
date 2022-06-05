<?php


namespace App\Http\Controllers\Api;

use App\Models\Category;

class CategoriesController extends Controller
{
    public function index()
    {
        $category = Category::all();
        
        return response()->json($category);
    }

}

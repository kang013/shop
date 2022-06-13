<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Slide;
use Illuminate\Http\Request;

class SlideController extends Controller
{
    public function index(Request $request, Slide $slide)
    {
        $categoryId = $slide->getCategoryId($request->input('index'));
        $slide = $slide->where(['status'=>1])->orderByDesc('order')->get();
        return response()->json($slide);
    }
}

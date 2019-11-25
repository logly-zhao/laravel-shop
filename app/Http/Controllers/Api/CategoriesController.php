<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Transformers\CategoryTransformer;

class CategoriesController extends Controller
{
    public function index()
    {
        $categoris  = $this->response->collection(Category::where('is_directory', '0')->orderBy('created_at', 'asc')->get(), new CategoryTransformer());
        $data['code'] = 0;
        $data['data'] = $categoris -> original;
        return $data;
    }
}

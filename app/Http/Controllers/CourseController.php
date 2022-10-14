<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    //
    public function index()
    {
        return view('posts.index', [
            'posts' => Course::latest()->filter(
                request(['search'/*, 'category', 'author'*/])
            )->paginate(18)->withQueryString()
        ]);
    }
}

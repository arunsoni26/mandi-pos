<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;

class FrontendController extends Controller
{
    public function home()
    {
        return view('frontend.index');
    }

    public function news()
    {
        $news = News::orderBy('id', 'desc')->limit(7)->get();
        $allnews = News::orderBy('id', 'desc')->get();
        return view('frontend.news', compact('news', 'allnews'));
    }

    public function show($id)
    {
        $news = News::findOrFail($id);
        return view('frontend.news_show', compact('news'));
    }

    public function loadMore(Request $request)
    {
        $offset = (int) $request->offset ?? 0;
        $news = News::orderBy('id', 'desc')->skip($offset)->take(6)->get();
        return response()->json($news);
    }
}

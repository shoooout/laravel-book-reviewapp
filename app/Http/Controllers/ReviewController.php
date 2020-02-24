<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Review;

class ReviewController extends Controller
{
    public function index() {
        $reviews = Review::where('status', 1)->orderBy('created_at', 'DESC')->paginate(9);

        //dd($reviews); //中身を分解して動作を止めるデバッグ用の関数
        return view('index', compact('reviews'));
    }

    public function show($id)
    {
        $review = Review::where('id', $id)->where('status', 1)->first();

        return view('show', compact('review'));
    }

    public function create()
    {
        return view('review');
    }

    public function store(Request $request)
    {
        $post = $request->all();

        //コントローラーへの追加でバリデーションを追加できる
        $validateData = $request->validate([
            'title' => 'required|max:225',
            'body' => 'required',
            'image' => 'mimes:jpeg, png, jpg, gif, svg|max:2048',
        ]);

        if ($request->hasFile('image')) {            
            $request->file('image')->store('/public/images');
            $data = ['user_id' => \Auth::id(), 'title' => $post['title'], 'body' => $post['body'], 'image' => $request->file('image')->hashName()];
        } else {
            $data = ['user_id' => \Auth::id(), 'title' => $post['title'], 'body' => $post['body']];
        }

        Review::insert($data);

        return redirect('/')->with('flash_message', '投稿が完了しました');
    }
}

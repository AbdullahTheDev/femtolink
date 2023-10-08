<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Link;

class LinkController extends Controller
{
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'long_url' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors(), 'timestamp' => time()], 400);
        }

        $link = new Link();

        $link->ownerId = $request->user()->id;
        $link->long_url = $request->long_url;
        $link->short_url = $this->generateShortLink();

        $link->save();

        return response()->json(['success' => true, 'message' => 'Link created successfully', 'timestamp' => time()], 200);

    }

    public function generateShortLink($length = 16)
    {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $str = substr(str_shuffle(str_repeat($pool, 5)), 0, $length);

        if($this->doesExistShortLink($str)){
            return $this->generateShortLink();
        } else {
            return $str;
        }
    }
    public function doesExistShortLink(string $short_url){
        $link = Link::where('short_url', '=', $short_url);
        return $link->count() > 0;
    }
}
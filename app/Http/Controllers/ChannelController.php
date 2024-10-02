<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ChannelController extends Controller
{
    public function index() {
        $channels = User::all()->sortByDesc('created_at');
        $title = 'أحدث القنوات';
        return view('channels', compact('channels', 'title'));
    }

    public function search(Request $request)
    {
        $channels = User::where('name', 'like', "%{$request->search}%")->paginate(12);
        $title = ' عرض نتائج البحث عن: ' . $request->term;
        return view('channels', compact('channels', 'title'));
    }
}

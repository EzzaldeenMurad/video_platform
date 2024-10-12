<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Video;
use App\Models\View;
use Illuminate\Http\Request;

class ChannelController extends Controller
{
    public $channel;
    function __construct(User $channel)
    {
        $this->channel = $channel;
    }
    public function index()
    {
        $channels =  $this->channel->all()->sortByDesc('created_at');
        $title = 'أحدث القنوات';
        return view('channels', compact('channels', 'title'));
    }

    public function search(Request $request)
    {
        $channels = $this->channel->where('name', 'like', "%{$request->search}%")->paginate(12);
        $title = ' عرض نتائج البحث عن: ' . $request->term;
        return view('channels', compact('channels', 'title'));
    }

    public function adminIndex()
    {
        $users = User::all();
        return view('admin.channels.index', compact('users'));
    }

    public function adminUpdate(Request $request, User $user)
    {
        $user->administration_level = $request->administration_level;
        $user->save();

        session()->flash('flash_message', 'تم تعديل صلاحيات القناة بنجاح');

        return redirect(route('channels.index'));
    }
    public function adminDestroy(User $user)
    {
        $user->delete();

        session()->flash('flash_message', 'تم حذف القناة بنجاح');

        return redirect(route('channels.index'));
    }
    public function adminBlock(Request $request, User $user)
    {
        $user->block = 1;
        $user->save();

        session()->flash('flash_message', 'تم حظر القناة بنجاح');

        return redirect(route('channels.index'));
    }
    public function blockedChannels()
    {
        $channels = User::where('block', 1)->get();
        return view('admin.channels.blocked-channels', compact('channels'));
    }

    public function openBlock(Request $request, User $user)
    {
        $user->block = 0;
        $user->save();

        session()->flash('flash_message', 'تم فك حظر القناة بنجاح');

        return redirect(route('channels.blocked'));
    }
    public function allChannels()
    {
        $channels = $this->channel->all()->sortByDesc('created_at');

        return view('admin.channels.all', compact('channels'));
    }
}

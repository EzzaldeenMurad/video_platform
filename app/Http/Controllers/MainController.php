<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Video;
use App\Models\View;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MainController extends Controller
{
    public $video;
    public function __construct(Video $video)
    {
        $this->video = $video;
    }
    public function index()
    {
        $date = Carbon::today()->subDays(7);
        $title = 'الفيديوهات الأكثر مشاهدة خلال هذا الأسبوع';
        // $videos = $this->video->getVideoViews($date);
        $videos = Video::join('views', 'videos.id', '=', 'views.video_id')
            ->orderBy('views.views_number', 'Desc')
            ->where('videos.created_at', '>=', $date)->with('user')->get();
        return view('index', compact('videos', 'title'));
    }
    public function channelsVideos(User $channel)
    {
        $videos = Video::where('user_id', $channel->id)->get();
        $title = ' جميع الفيديوهات الخاصة بالقناة: ' . $channel->name;
        return view('videos.my-videos', compact('videos', 'title'));
    }
}

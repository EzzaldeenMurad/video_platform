<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function LikeVideo(Request $request)
    {
        $videoId = $request->videoId;
        $isLike = $request->isLike === 'true';
        $update = false;
        $user = Auth::user();
        $video = Video::find($videoId);
        if (!$video) {
            return null;
        }
        $like = $user->likes()->where('video_id', $videoId)->first();

        if ($like) {
            $alreadyLike = $like->like;
            $update = true;
            if ($alreadyLike == $isLike) {
                $like->delete();
            }
        } else {
            $like = new Like();
        }
        $like->user_id = $user->id;
        $like->video_id = $video->id;
        $like->like = $isLike;
        if ($update) {
            $like->update();
        } else {
            $like->save();
        }
        $countLike = Like::where(['video_id' => $video->id, 'like' => '1'])->count();
        $countDislike = Like::where(['video_id' => $video->id, 'like' => '0'])->count();

        return response()->json(['countLike' => $countLike, 'countDislike' => $countDislike,]);
    }
}

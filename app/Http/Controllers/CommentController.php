<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Video;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function saveComment(Request $request)
    {
        $userComment = $request->comment;
        $videoId = $request->videoId;

        $video = Video::find($videoId);
        $comment = new Comment();

        $user = Auth::user();

        if ($video) {
            Comment::create([
                'body' => $userComment,
                'user_id' => $user->id,
                'video_id' => $videoId
            ]);
        }

        $userName = $user->name;
        $userImage = $user->profile_photo_url;
        $commentDate = Carbon::now()->diffForHumans();

        $commentId = $comment->id;

        return response()->json(['userName' => $userName, 'commentDate' => $commentDate, 'userImage' => $userImage, 'commentId' => $commentId]);
    }



    public function edit($id)
    {
        $comment = Comment::where('id', $id)->first();
        return view('edit-comment', compact('comment'));
    }

    
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'comment' => 'required',
        ]);

        $comment = Comment::where('id', $id)->first();
        $videoId = $comment->video->id;

        $comment->body = $request->comment;

        $comment->save();


        return redirect('videos/' . $videoId)->with(
            'success',
            'تم تعديل محتوى التعليق بنجاح'
        );
    }
    public function destroy($id)
    {
        $comment = Comment::where('id', $id)->first();

        $comment->delete();

        return back()->with('success', 'تم حذف التعليق بنجاح');
    }
}

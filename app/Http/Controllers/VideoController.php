<?php

namespace App\Http\Controllers;

use App\Jobs\ConvertVideoStreaming;
use App\Models\Convertedvideo;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;

class VideoController extends Controller
{
    public $video;

    public function __construct(Video $video)
    {
        $this->video = $video;

        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $videos = auth()->user()->videos->sortByDesc('created_at');
        $title = 'آخر الفيديوهات المرفوعة';
        return view('videos.my-videos', compact('videos', 'title'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('videos.uploader');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'image' => 'image|required',
            'video' => 'required',
        ]);
        $randomPath = Str::random(16);
        $videoPath = $randomPath . '.' . $request->video->getClientOriginalExtension();
        $imagePath = $randomPath . '.' . $request->image->getClientOriginalExtension();
        $image = Image::make($request->image)->resize(320, 180);
        $path = Storage::put($imagePath, $image->stream());
        $request->video->storeAs('/', $videoPath, 'public');

        // dd(public_path('storage\\'));
        $video = $this->video->create([
            'disk'        => 'public',
            'video_path'  => $videoPath,
            'image_path'  => $imagePath,
            'title'       => $request->title,
            'user_id'     => auth()->id(),
        ]);

        ConvertVideoStreaming::dispatch($video);
        return redirect()->back()->with(
            'success',
            'سيكون مقطع الفيديو متوفر في أقصر وقت عندما ننتهي من معالجته'
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Video $video)
    {
        return view('videos.show', compact('video'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Video $video)
    {
        return view('videos.edit', compact('video'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'title' => 'required',
        ]);

        $video = Video::where('id', $id)->first();

        if ($request->has('image')) {

            $randomPath = Str::random(16);
            $newPath =  $randomPath . '.' . $request->image->getClientOriginalExtension();

            Storage::delete($video->image_path);

            $image = Image::make($request->image)->resize(320, 180);
            $path = Storage::put($newPath, $image->stream());

            $video->image_path = $newPath;
        }

        $video->title = $request->title;

        $video->save();

        return redirect('/videos')->with(
            'success',
            'تم تعديل معلومات الفيديو بنجاح'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Video $video)
    {
        $convertedVideo = Convertedvideo::where('video_id', $video->id)->first();
        // dd($convertedVideo);
        Storage::delete([
            $convertedVideo->mp4_Format_240,
            $convertedVideo->mp4_Format_360,
            $convertedVideo->mp4_Format_480,
            $convertedVideo->mp4_Format_720,
            $convertedVideo->mp4_Format_1080,
            $convertedVideo->webm_Format_240,
            $convertedVideo->webm_Format_360,
            $convertedVideo->webm_Format_480,
            $convertedVideo->webm_Format_720,
            $convertedVideo->webm_Format_1080,
            $video->image_path
        ]);
        $video->delete();
        return back()->with('success', 'تم حذف مقطع الفيديو بنجاح');
    }

    public function search(Request $request)
    {
        $videos = $this->video->where('title', 'LIKE', "%{$request->search}%")->paginate(12);
        $title = ' عرض نتائج البحث عن: ' . $request->search;
        return view('videos.my-videos', compact('videos', 'title'));
    }
}

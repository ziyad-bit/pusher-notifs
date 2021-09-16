<?php

namespace App\Http\Controllers;

use App\Events\NewNotification;
use App\Models\Comment;
use App\Models\Notifications;
use App\Models\Post;
use Auth;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $comments_notifs=Comment::with('user')->whereHas('notifications',function($q){
            $q->where('notifiable_id',Auth::user()->id);
        })->get();

        $notifs_count=Notifications::where(['notifiable_id'=>Auth::user()->id,'view'=>0])
                                ->count();

        $posts = Post::with(['comments' => function ($q) {
            $q->select('id', 'post_id', 'comment');
        }])->get();

        return view('home', compact('posts','comments_notifs','notifs_count'));
    }

    public function update_notifs()
    {
        $notifs_id=Notifications::where(['notifiable_id'=>Auth::user()->id,'view'=>0])
                ->pluck('id')->toArray();

        Notifications::whereIn('id',$notifs_id)->update([
            'view'=>1
        ]);

        return response()->json();
    }

    public function saveComment(Request $request)
    {
        $post_id      = $request->post_id;
        $user_id      = Auth::id();
        $post_content = $request->post_content;
        

        $comment=Comment::create([
            'post_id' => $post_id ,
            'user_id' => $user_id,
            'comment' => $post_content,
        ]);

        $post         = Post::find($request->post_id);
        $post_user_id = $post->user_id;

        $data = [
            'user_id'      => $user_id,
            'user_name'    => Auth::user()->name,
            'comment'      => $post_content,
            'post_id'      => $post_id,
            'post_user_id' => $post_user_id,
        ];

        ///   save  notify in database table ////
        if ($user_id != $post_user_id) {
            Notifications::create([
                'notifiable_id'=>$post_user_id,
                'comment_id'=>$comment->id
            ]);
        }
        
        event(new NewNotification($data));

        return redirect()->back()->with(['success' => 'تم اضافه تعليقك بنجاح ']);
    }
}

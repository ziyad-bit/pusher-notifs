<?php

namespace App\Events;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NewNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    
    public $user_id;
    public $comment;
    public $user_name;
    public $post_id;
    public $time;
    public $post_user_id;

    /**
     * Create a new event instance.
     *
     * @return void
     */

    public function __construct($data = [])
    {
        $this->user_id      = $data['user_id'];
        $this->user_name    = $data['user_name'];
        $this->comment      = $data['comment'];
        $this->post_id      = $data['post_id'];
        $this->post_user_id = $data['post_user_id'];
        $this->date         = date("Y-m-d", strtotime(Carbon::now()));
        $this->time         = date("h:i A", strtotime(Carbon::now()));
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
    */

    public function broadcastOn()
    {
        return new PrivateChannel('new-notification.'.$this->post_user_id);
        
    }

}

<?php

namespace App\Http\Controllers\Admin;

use App\Models\Message;
use App\Models\Notification;
use App\Models\Replay;
use App\Models\Ticket;
use App\Models\User;
use App\Traits\GlobalTrait;
use App\Traits\Dashboard\MessageTrait;
use App\Traits\Dashboard\PublicTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Mail;
use App\Mail\NewAdminReplyMail;
use MercurySeries\Flashy\Flashy;
use Validator;
use DB;
use Auth;

class UserMessageController extends Controller
{
    use MessageTrait, PublicTrait, GlobalTrait;

    public function index()
    {
        $tickets = $this->getAllUserMessages();
        return view('admin.messages.users.index', compact('tickets'));
    }

    public function tickets()
    {
        $tickets = $this->getAllTickets();  //user and coach
        return view('admin.messages.index', compact('tickets'));
    }

    public function getReply($id)
    {
        $ticket = $this->getMessageById($id);  //get user Ticket
        if ($ticket == null)
            return abort('404');
        $replies = $ticket->replies()->get()->groupBy(function ($q) {
            return $q->created_at->format('Y M d');
        });
        if (isset($replies) && $replies->count() > 0)
            $ticket->replies()->update(['seen' => '1']); // update all tickets to seen
        $lastMessage = $ticket->replies()->where('FromUser', 1)->get()->last();
        return view('admin.messages.users.view', compact('ticket', 'replies', 'lastMessage'));
    }

    public function destroy($id)
    {
        try {
            $message = $this->getMessageById($id);
            if ($message == null)
                return abort('404');
            $message->delete();
            notify()->success('  تم حذف التذكرة بجميع ردودها بنجاح  ');
            return redirect()->back()->with(['success' => ' تم حذف التذكرة بجميع ردودها بنجاح ']);
        } catch (\Exception $ex) {
            return abort('404');
        }
    }

    public function reply(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "ticket_id" => "required",
            "replay_message" => "required",
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => 'لابد من اخال نص الرساله اولا ']);
        }
        DB::beginTransaction();
        $message = $this->getMessageById($request->ticket_id);   // get ticket
        $newMessage = Replay::create([
            'message' => $request->replay_message,
            'ticket_id' => $message->id,
            'FromUser' => 0,
        ]);

        /* GET ALL THE MESSAGE PREVIOUS THIS REPLAY TIME TO MARK THEM AS HASREPLAYFROMADMIN = 1
            $newMessageDate = $newMessage->created_at;
            $msgs = $message -> replies()  ->whereDate('created_at','<=',$newMessageDate)  -> get();

            if(isset($msgs) && $msgs -> count() > 0)
            {
                foreach ($msgs as $msg)
                {
                   $msg -> where('FromUser',1) -> update(['hasReplayFromAdmin' => 1]);
                }
            }*/
        $notif_data = array();
        $push_notif_title = $message->title;
        $push_notif_content = $newMessage->message;
        $notif_data['title'] = $push_notif_title;
        $notif_data['body'] = $push_notif_content;
        $notif_data['id'] = $request->ticket_id;
        $notif_data['notification_type'] = 4; // notify about new message reply
        $actor  = $message->ticketable;   //get user of this ticket

        $content = __('messages.there are replay on your ticket ') . ' ' .($message-> title) ;

        //send push notification to coach/user
        (new \App\Http\Controllers\PushNotificationController(['title' => 'رد من الاكاديمية', 'body' => $content]))->send($actor->device_token);

        //$this->saveNotification($user, $notif_data);
        DB::commit();
        $view = view('admin.includes.content.adminMsg', compact('newMessage'))->renderSections();
        return response()->json([
            'content' => $view['main'],
        ]);
    }

}

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
            $message->replays()->delete();
            $message->delete();
            notify()->success('  تم حذف التذكرة بجميع ردودها بنجاح  ');
            return redirect()->route('admin.users.tickets.all')->with(['success' => ' تم حذف التذكرة بجميع ردودها بنجاح ']);
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

            $notif_data = array();
            $push_notif_title = $message->title;
            $push_notif_content = $newMessage->message;
            $notif_data['title'] = $push_notif_title;
            $notif_data['body'] = $push_notif_content;
            $notif_data['id'] = $request->ticket_id;
            $notif_data['notification_type'] = 1; // notify about new message reply
             $user = $message->ticketable;   //get user of this ticket
            $this->sendPushNotification($user, $notif_data);
            $this->saveNotification($user, $notif_data);
            DB::commit();
            $view = view('admin.includes.content.adminMsg', compact('newMessage'))->renderSections();
            return response()->json([
                'content' => $view['main'],
            ]);


    }

    /////////////////// api //////

    public
    function newTicket(Request $request)
    {

        $validator = Validator::make($request->all(), [
            "importance" => "numeric|min:1|max:2",
            "type" => "numeric|min:1|max:4",
            "message" => "required",
            "title" => 'required',
            "actor_type" => "required|in:1"
        ]);
        DB::beginTransaction();

        try {
            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }
            //1 -> user 2 -> any thing in future
            $actor_type = $request->actor_type;
            /* if ($actor_type == 1 or $actor_type == '1') {
                 $user = $this->auth('provider-api');
             }*/

            $actor_type = ($actor_type == 1 || $actor_type == '1') ? 'App\Models\User' : '';
            $user = User::find(45);
            if (!$user) {
                return $this->returnError('D000', trans('messages.User not found'));
            }
            if (!isset($request->title) || empty($request->title))
                return $this->returnError('D000', trans('messages.Please enter message title'));

            if (!isset($request->type) || $request->type == 0 || !isset($request->importance) || $request->importance == 0)
                return $this->returnError('D000', trans('messages.Please enter importance and type'));

            $ticket = Ticket::create([
                'title' => $request->title ? $request->title : "",
                'ticketable_id' => $user->id,
                'ticketable_type' => $actor_type,
                'message_no' => 'M' . $user->id . uniqid(),
                'type' => $request->type,
                'importance' => $request->importance,
                'message' => $request->message,
                //'message_id' => $request->message_id != 0 ? $request->message_id : NULL,
                //'order' => $order
            ]);

            $replay = [
                "ticket_id" => $ticket->id,
                "message" => $request->message,
                "FromUser" => 1,
            ];

            $replay = new Replay($replay);
            $ticket->replies()->save($replay);

            event(new \App\Events\NewMessage($replay));   // fire pusher message event
            DB::commit();
            return $this->returnSuccessMessage(trans('messages.Message sent successfully, you can keep in touch with replies by view messages page'));
        } catch (\Exception $ex) {
            DB::rollback();
            return $ex;
        }

    }


    // event(new App\Events\NewMessage('Someone'));


}

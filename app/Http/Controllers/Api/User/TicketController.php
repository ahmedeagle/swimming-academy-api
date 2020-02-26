<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Replay;
use App\Models\Ticket;
use App\Models\User;
use App\Traits\GlobalTrait;
use App\Traits\TicketTrait;
use Illuminate\Http\Request;
use Validator;
use Auth;
use JWTAuth;
use DB;

class TicketController extends Controller
{
    use GlobalTrait, TicketTrait;

    public function __construct(Request $request)
    {

    }

    public
    function getTickets(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                "actor_type" => "required|in:1",  // 1 user for now
            ]);

            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }

            $actor_type = $request->actor_type;
            if ($actor_type == 1 or $actor_type == '1') {
                $user = $this->auth('user-api');
                if (!$user) {
                    return $this->returnError('D000', trans('messages.User not found'));
                }
                $tickets = $this->getTicketsByType($user->id, $actor_type);
            } else {
                //not other type until now
            }

            if (count($tickets->toArray()) > 0) {
                $total_count = $tickets->total();
                $tickets->getCollection()->each(function ($ticket) {
                    $replayCount = Replay::where('ticket_id', $ticket->id)->where('FromUser', 0)->count();   // user 0 means replay from admin
                    $lastReplay = Replay::where('ticket_id', $ticket->id)->orderBy('created_at', 'DESC')->first();   // user 0 means replay from admin
                    if ($replayCount == 0) {
                        $ticket->replay_status = 0;  // بانتظار الرد
                    } else {
                        $ticket->replay_status = 1;    //   تم الرد
                    }

                    $ticket->last_replay = $lastReplay->message;
                     if ($ticket->importance == 1)
                        $ticket->importance_text = trans('messages.Quick');
                    else if ($ticket->importance == 2)
                        $ticket->importance_text = trans('messages.Normal');
                    if ($ticket->type == 1)
                        $ticket->type_text = trans('messages.Inquiry');
                    else if ($ticket->type == 2)
                        $ticket->type_text = trans('messages.Suggestion');
                    else if ($ticket->type == 3)
                        $ticket->type_text = trans('messages.Complaint');
                    else if ($ticket->type == 4)
                        $ticket->type_text = trans('messages.Others');

                    unset($ticket -> ticketable_type);
                    unset($ticket -> ticketable_id);
                    return $ticket;
                });

                $tickets = json_decode($tickets->toJson());
                $ticketsJson = new \stdClass();
                $ticketsJson->current_page = $tickets->current_page;
                $ticketsJson->total_pages = $tickets->last_page;
                $ticketsJson->total_count = $total_count;
                $ticketsJson->data = $tickets->data;
                return $this->returnData('tickets', $ticketsJson);
            }
            return $this->returnError('E001', trans("messages.No messages founded"));
        } catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }

    public
    function newTicket(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "importance" => "numeric|min:1|max:2",
            "type" => "numeric|min:1|max:4",
            "message" => "required",
            "title" => 'required',
            "actor_type" => "required|in:1",
        ]);
        DB::beginTransaction();
        if ($validator->fails()) {
            $code = $this->returnCodeAccordingToInput($validator);
            return $this->returnValidationError($code, $validator);
        }

        $actor_type = $request->actor_type;
        if ($actor_type == 1 or $actor_type == '1') {
            $user = $this->auth('user-api');
        } else {
            //no other type untill now
        }

        if (!$user) {
            return $this->returnError('D000', trans('messages.User not found'));
        }


        if (!isset($request->type) || $request->type == 0 || !isset($request->importance) || $request->importance == 0)
            return $this->returnError('D000', trans('messages.Please enter importance and type'));

        $ticket = Ticket::create([
            'title' => $request->title ? $request->title : "",
            'ticketable_id' => $user->id,
            'ticketable_type' => ($actor_type == 1) ? 'App\Models\User' : '',
            'message_no' => 'M' . $user->id . uniqid(),
            'type' => $request->type,
            'importance' => $request->importance,
            'message' => $request->message,
            'academy_id' => $user->academy_id,
        ]);

        $replay = [
            "ticket_id" => $ticket->id,
            "message" => $request->message,
            "FromUser" => $actor_type
        ];

        $replay = new Replay($replay);
        $ticket->replies()->save($replay);
        event(new \App\Events\NewMessage($replay));   // fire pusher message event notification

        DB::commit();
        return $this->returnSuccessMessage(trans('messages.Message sent successfully, you can keep in touch with replies by view messages page'));
    }

    public
    function AddMessage(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "id" => "required|exists:tickets,id",
                "message" => "required",
                "actor_type" => "required|in:1"
            ]);

            DB::beginTransaction();
            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }

            $actor_type = $request->actor_type;
            if ($actor_type == 1 or $actor_type == '1')
                $user = $this->auth('user-api');
                else{
                    //no more uptill no
                }
            $id = $request->id;
            $message = $request->message;
            $ticket = Ticket::find($id);

            if (!$user) {
                return $this->returnError('D000', trans('messages.User not found'));
            }
            if ($ticket) {
                if ($ticket->ticketable_id != $user->id) {
                    return $this->returnError('D000', trans('messages.cannot replay for this converstion'));
                }
            }

            $replay = Replay::create([
                'message' => $message,
                "ticket_id" => $id,
                "FromUser" => $actor_type
            ]);

            event(new \App\Events\NewMessage($replay));   // fire pusher message event notification

            DB::commit();
            return $this->returnSuccessMessage(trans('messages.Reply send successfully'));
        } catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }

    }

    public
    function GetTicketMessages(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "id" => "required|exists:tickets,id",
                "actor_type" => "required|in:1,2"
            ]);

            DB::beginTransaction();
            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }
            $actor_type = $request->actor_type;
            if ($actor_type == 1 or $actor_type == '1')
                $user = $this->auth('user-api');


            if ($actor_type == 2 or $actor_type == '2')
                $user = $this->auth('coach-api');

            $id = $request->id;
            $ticket = Ticket::find($id);
            if (!$user) {
                return $this->returnError('D000', trans('messages.User not found'));
            }

             if ($ticket) {
                if ($ticket->	ticketable_id != $user->id) {
                    return $this->returnError('D000', trans('messages.cannot access this converstion'));
                }
            }

            $messages = Replay::where('ticket_id', $id)->paginate(10);

            if (count($messages->toArray()) > 0) {

                $total_count = $messages->total();

                $messages = json_decode($messages->toJson());
                $messagesJson = new \stdClass();
                $messagesJson->current_page = $messages->current_page;
                $messagesJson->total_pages = $messages->last_page;
                $messagesJson->total_count = $total_count;
                $messagesJson->data = $messages->data;
                //add photo

                foreach ($messages->data as $message) {
                    if ($message->FromUser == 0) {//admin
                        $message->logo = "";
                    } elseif ($message->FromUser == 1) { //user
                        $ticket = Ticket::find($id);
                        if ($ticket) {
                            $logo = User::where('id', $ticket->	ticketable_id)->value('photo');
                            $message->logo = $logo;
                        } else {

                            $message->logo = "";
                        }
                    } elseif ($message->FromUser == 2) { //coach
                     } else {
                     }
                }
                return $this->returnData('messages', $messagesJson);
            }

            return $this->returnError('E001', trans("messages.No messages founded"));

            DB::commit();


        } catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }


    }

}

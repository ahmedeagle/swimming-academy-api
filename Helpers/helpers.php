<?php

use Illuminate\Support\Facades\Storage;

function takeLastMessage($count)
{
    return \App\Models\Replay::with('ticket')->whereHas('ticket', function ($q) {
        $q->whereHasMorph('ticketable', 'App\Models\User');
    })->where('FromUser', 1)->latest()->take($count)->get();

}

/**
 * Checks if the given string is valid json string.
 *
 * @param json $string [description]
 * @return boolean         [description]
 */
function is_json($string)
{
    return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
}

/**
 * Checks if the given string is valid url string.
 *
 * @param json $string [description]
 * @return boolean         [description]
 */
function is_url($string)
{
    // Remove all illegal characters from a url
    $string = filter_var($string, FILTER_SANITIZE_URL);
    return (!filter_var($string, FILTER_VALIDATE_URL) === false);
}


function checkForShowImage($messageId, $ticketId)
{
    $id = $messageId - 1;
    $prevReplay = \App\Models\Replay::where('id', $id)->first();
    if (!$prevReplay) {
        return true;
    }
    if ($prevReplay->FromUser == 1 && ($prevReplay->ticket->id == $ticketId)) {
        return false;
    } else {
        return true;
    }

}


function currentWeekStartEndDate()
{
    $dt_min = new DateTime("last saturday"); // Edit
    $dt_max = clone($dt_min);
    $dt_max->modify('+6 days');
    $startOfWeek = $dt_min->format('d-m-Y');
    $endtOfWeek = $dt_max->format('d-m-Y');

    $data = [];
    $data['startWeek'] = $startOfWeek;
    $data['endWeek'] = $endtOfWeek;

    return $data;
}

<?php

use Illuminate\Support\Facades\Date;
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
    if (date('D') == 'Sat') {
        $dt_min = new DateTime("today"); // Edit
    }

    $dt_max = clone($dt_min);
    $dt_max->modify('+6 days');
    $startOfWeek = $dt_min->format('d-m-Y');
    $endtOfWeek = $dt_max->format('d-m-Y');

    $data = [];
    $data['startWeek'] = $startOfWeek;
    $data['endWeek'] = $endtOfWeek;

    return $data;
}


function getDiffBetweenTwoDate($startDate, $endDate, $formate = 'a')
{
    $fdate = $startDate;
    $tdate = $endDate;
    $datetime1 = new DateTime($fdate);
    $datetime2 = new DateTime($tdate);
    $interval = $datetime1->diff($datetime2);
    $days = $interval->format('%a');
    return $days;
}


function getDaysInMonth($month, $year)
{
    return new Date($year, $month + 1, 0) . getDate();
}

function unavailabledate($month_days, $unavailble_days)
{
    $unavaibledates = [];
    $index = 0;
    foreach ($unavailble_days as $dayName) {
        foreach ($month_days as $index => $monthDay) {
            if ($monthDay['day_name'] == $dayName) {
                $unavaibledates[$index]['day_name'] = $monthDay['day_name'];
                $unavaibledates[$index]['date'] = $monthDay['date'];
                $unavaibledates[$index]['classname'] = 'dangerc';
            }
            $index++;
        }
    }

    return array_values($unavaibledates);
}

function get_dates($month, $year)
{
    $start_date = "01-" . $month . "-" . $year;
    $start_time = strtotime($start_date);

    $end_time = strtotime("+1 month", $start_time);

    $index = 0;
    for ($i = $start_time; $i < $end_time; $i += 86400) {
        $name = date("l", $i);
        $list[$index]['day_name'] = strtolower($name);
        $list[$index]['date'] = date('Y-m-d', $i);
        $index++;
    }
    return $list;
}


function getAllDateBetweenTwoDate($date_from, $date_to, $teamDays = [])
{
    $dates = [];
    $date_from = strtotime($date_from);
    $date_to = strtotime($date_to);
    for ($i = $date_from; $i <= $date_to; $i += 86400) {
        $obj = new \stdClass();
        $obj->date = date("Y-m-d", $i);
        $obj->day_name = strtolower(date("l", $i));
        array_push($dates, $obj);
    }

    if (count($dates) > 0) {
        if (count($teamDays) > 0) {
            $subscriptionDays = collect($dates)->whereIn('day_name', $teamDays);
            return array_values($subscriptionDays->toArray());
        } else
            return $dates;
    } else
        return [];
}

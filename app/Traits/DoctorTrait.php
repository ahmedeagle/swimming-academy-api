<?php

namespace App\Traits;

use App\Models\Doctor;
use App\Models\DoctorTime;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\ReservedTime;
use App\Models\User;
use Carbon\Carbon;
use DB;

trait DoctorTrait
{
    public function findDoctor($id)
    {
        $doctor = Doctor::find($id);
        if ($doctor != null) {
            $doctor->name = $doctor->getTranslatedName();
            $doctor->information = $doctor->getTranslatedInformation();
        }
        return $doctor;
    }

    public function getDoctorByID($id)
    {
        $doctor = Doctor::with(['insuranceCompanies' => function ($q) {
            $q->select('insurance_companies.id as id', 'image', DB::raw('insurance_companies.name_' . $this->getCurrentLang() . ' as name'))->where('status', true);
        }, 'provider' => function ($qu) {
            $qu->select('id', 'provider_id', 'rate', 'longitude', 'latitude', 'mobile', 'logo', 'commercial_no', DB::raw('name_' . $this->getCurrentLang() . ' as name'));
            $qu->with(['main_provider' => function ($r) {
                $r->select('id', DB::raw('name_' . app()->getLocale() . ' as name'));
            }]);
        }, 'specification' => function ($q1) {
            $q1->select('id', DB::raw('name_' . $this->getCurrentLang() . ' as name'));
        }, 'nationality' => function ($q2) {
            $q2->select('id', DB::raw('name_' . $this->getCurrentLang() . ' as name'));
        }, 'nickname' => function ($q3) {
            $q3->select('id', DB::raw('name_' . $this->getCurrentLang() . ' as name'));
        }
        ])->find($id);
        if ($doctor != null) {
            $doctor->name = $doctor->getTranslatedName();
            $doctor->information = $doctor->getTranslatedInformation();
        }
        return $doctor;
    }

    public function getDoctorFavourite($id, $userId)
    {
        return Doctor::whereHas('favourites', function ($qu) use ($userId) {
            $qu->where('user_id', $userId)->select('provider_id');
        })->find($id);
    }

    public function getDoctorTimes($doctorId)
    {
        // effect by date
        $working_days = DoctorTime::where('doctor_id', $doctorId)->orderBy('created_at')->orderBy('order')->get();
        return $this->getDoctorTimePeriods($working_days);
    }

    public function checkIfCoupounPaid($promoCodeId, $userId)
    {
        $user = User::find($userId);
        //if login user , whose make payment
        $owner = Payment::where('offer_id', $promoCodeId)->where(function ($q) use ($userId, $user) {
            $q->where('invited_user_mobile', $user->mobile)
                ->orWhere('user_id', $userId);
        })->first();
        if ($owner) {
            return true;
        }
        return false;
    }

    public function getPromoAvailblePriceByIdandUser($promoCodeId, $userId)
    {
        $user = User::find($userId);
        $paymentData = Payment::where('offer_id', $promoCodeId)->where(function ($q) use ($userId, $user) {
            $q->where('invited_user_mobile', $user->mobile)
                ->orWhere('user_id', $userId);
        })->first();
        return $paymentData;
    }

    public function getDoctorTimesPeroids($working_days)
    {
        // effect by date
        return $this->getDoctorTimePeriods($working_days);
    }

    public function getDoctorTimePeriods($working_days)
    {
        $times = [];
        $j = 0;
        foreach ($working_days as $working_day) {
            $from = strtotime($working_day['from_time']);
            $to = strtotime($working_day['to_time']);
            $diffInterval = ($to - $from) / 60;
            $periodCount = $diffInterval / $working_day['time_duration'];
            for ($i = 0; $i < round($periodCount); $i++) {
                $times[$j]['day_code'] = $working_day['day_code'];
                $times[$j]['day_name'] = $working_day['day_name'];
                $times[$j]['from_time'] = Carbon::parse($working_day['from_time'])->addMinutes($working_day['time_duration'] * $i)->format('H:i');
                $times[$j]['to_time'] = Carbon::parse($working_day['from_time'])->addMinutes($working_day['time_duration'] * ($i + 1))->format('H:i');
                $times[$j++]['time_duration'] = $working_day['time_duration'];
            }
        }
        return $times;
    }

    public function getDoctorTimePeriodsInDay($working_day, $day_code, $count = false)
    {
        $times = [];
        $j = 0;
        if ($working_day['day_code'] == $day_code) {
            $from = strtotime($working_day['from_time']);
            $to = strtotime($working_day['to_time']);
            $diffInterval = ($to - $from) / 60;
            $periodCount = $diffInterval / $working_day['time_duration'];
            for ($i = 0; $i < round($periodCount); $i++) {
                $times[$j]['day_code'] = $working_day['day_code'];
                $times[$j]['day_name'] = $working_day['day_name'];
                $times[$j]['from_time'] = Carbon::parse($working_day['from_time'])->addMinutes($working_day['time_duration'] * $i)->format('H:i');
                $times[$j]['to_time'] = Carbon::parse($working_day['from_time'])->addMinutes($working_day['time_duration'] * ($i + 1))->format('H:i');
                $times[$j++]['time_duration'] = $working_day['time_duration'];
            }
        }
        if ($count)
            return count($times);
        return $times;
    }

    public function getDoctorDays($doctorId)
    {
        return DoctorTime::where('doctor_id', $doctorId)->groupBy('day_code')->orderBy('day_code')->pluck('day_code');
    }

    public function getDoctorWithTimes($id)
    {
        $doctor = Doctor::with(['times' => function ($q) {
            $q->orderBy('created_at')->orderBy('order');
        }])->find($id);
        if ($doctor != null) {
            $doctor->name = $doctor->getTranslatedName();
            $doctor->information = $doctor->getTranslatedInformation();
        }
        return $doctor;
    }

    public function getReservationInDate($doctorId, $dayDate, $count = false)
    {
        $reservation = Reservation::query();
        $reservation = $reservation->where('doctor_id', $doctorId)->where('day_date', $dayDate)->orderBy('from_time')->orderBy('to_time');
        if ($count)
            return $reservation->count();
        return $reservation->get();
    }

    public function getAvailableReservationInDate($doctorId, $dayDate, $count = false)
    {
        $reservation = Reservation::query();
        if ($dayDate instanceof Carbon)
            return $dayDate = date($dayDate->format('Y-m-d'));
        $query = "(SELECT COUNT(*)  FROM reservations WHERE day_date = '" . $dayDate . "' and doctor_id = '" . $doctorId . "') as reservation,";
        $query .= "(SELECT COUNT(*) FROM reserved_times rt WHERE rt.doctor_id = '" . $doctorId . "' and rt.day_date = '" . $dayDate . "' ) as day_reserved";
        $reservation = \Illuminate\Support\Facades\DB::select('SELECT ' . $query . ' FROM DUAL;')[0];
        // $reservation = $reservation->where('doctor_id', $doctorId)->where('day_date', $dayDate)->
        //doesnthave('doctor.reservedTimes', function($q) use($dayDate){
        //  $q->where('day_date','!=',$dayDate);
        //})->
        // orderBy('from_time')->orderBy('to_time');
        if ($reservation->day_reserved)
            return -1;
        else
            return $reservation->reservation;

    }

    public function getDoctorTimesInDay($doctorId, $dayName, $count = false)
    {
        // effect by date
        $doctorTimes = DoctorTime::query();
        $doctorTimes = $doctorTimes->where('doctor_id', $doctorId)->whereRaw('LOWER(day_name) = ?', strtolower($dayName))
            ->orderBy('created_at')->orderBy('order');

        $times = $this->getDoctorTimePeriods($doctorTimes->get());
        if ($count)
            if (!empty($times) && is_array($times))
                return count($times);
            else
                return 0;

        return $times;
    }

    public function getReservationInTime($doctorId, $date, $fromTime, $toTime)
    {
        // effect by date
        return Reservation::where('doctor_id', $doctorId)->where('day_date', $date)->where('from_time', $fromTime)->first();
    }

    public function getNextDayNameDoctorExists($doctorId, $dayName)
    {
        $doctorTimes = DoctorTime::where('doctor_id', $doctorId)->whereRaw('LOWER(day_name) != ?', strtolower($dayName))
            ->orderBy('created_at')->orderBy('order')->get();
        if (count($doctorTimes) > 0)
            return $doctorTimes->first()->day_name;

        else
            return $dayName;
    }

    public function getDoctorReservations($doctorId, $count = false)
    {
        $reservation = Reservation::query()->where('doctor_id', $doctorId);
        if ($count)
            return $reservation->count();

        return $reservation->get();
    }

    public function getFirstDayDoctorExists($doctorId)
    {
        $doctorTime = DoctorTime::where('doctor_id', $doctorId)->orderBy('created_at')->orderBy('order')->first();
        return $doctorTime;
    }

    public function geDaysDoctorExist($doctorId)
    {
        return DoctorTime::where('doctor_id', $doctorId)->select('day_name', 'day_code', 'reservation_period', 'from_time', 'to_time')
            ->orderBy('order')->get();
    }

    public function getMatchedDateToDayName($day_name, $day_format = null, $search_days = 30)
    {
        if ($day_format == null)
            $first_date = Carbon::now();
        else
            $first_date = Carbon::parse($day_format);

        $first_matched_date = null;
        for ($ii = 0; $ii < $search_days; $ii++) {
            if ($first_date->format('l') == $day_name) {
                $first_matched_date = date('Y-m-d', strtotime($first_date->format('Y-m-d')));
                break;
            }
            $first_date->addDay();
        }
        return $first_matched_date;
    }


    public function getMatchedDateToDays($days, $day_format = null, $search_days = 30)
    {
        $found = false;
        $index = 0;
        if ($day_format == null)
            $first_date = Carbon::now();
        else
            $first_date = Carbon::parse($day_format);

        $first_matched_date = null;
        for ($ii = 0; $ii < $search_days; $ii++) {
            $index = 0;
            foreach ($days as $day) {
                $day_name = $this->getDayByCode($day['day_code']);
                if ($first_date->format('l') == $day_name) {
                    $first_matched_date = $first_date;
                    $found = true;
                    break;
                }
                $index++;
            }
            if ($found)
                break;
            $first_date->addDay();
        }
        return ['index' => $index, 'day' => $day, 'date' => date($first_matched_date->format('Y-m-d'))];
    }

    public function getAllAvailableTime($doctorId, $timeCountInDay, $days, $timeDate, $count = 0)
    {
        // effect by date
        $getAllAvailableTime = [];
        if ($count > 60)
            return new \stdClass();
        $dayName = $this->getDayByCode($days[$count % count($days)]['day_code']);
        $reservationsCount = $this->getAvailableReservationInDate($doctorId, $timeDate, true);
        $doctorTimes = $this->getDoctorTimesInDay($doctorId, $dayName);
        foreach ($doctorTimes as $key => $dTime) {
            $reservation = $this->getReservationInTime($doctorId, $timeDate, $dTime['from_time'], $dTime['to_time']);
            if ($reservation != null)
                continue;
            else

                $avTime = ['date' => $timeDate, 'day_name' => trans('messages.' . $dayName),
                    'day_code' => trans('messages.' . $dayName . ' Code'), 'from_time' => $dTime['from_time'], 'to_time' => $dTime['to_time']];
            array_push($getAllAvailableTime, $avTime);
        }
        return $getAllAvailableTime;

    }

    public function getFirstAvailableTime($doctorId, $timeCountInDay, $days, $timeDate, $count = 0)
    {
        // effect by date
        if ($count > 60)
            return new \stdClass();

        $dayName = $this->getDayByCode($days[$count % count($days)]['day_code']);
        $reservationsCount = $this->getAvailableReservationInDate($doctorId, $timeDate, true);
        if ($timeCountInDay == $reservationsCount || $reservationsCount == -1) {
            $count++;
            $newDayName = $this->getDayByCode($days[$count % count($days)]['day_code']);
            if ($dayName == $newDayName) { // get next same day at the next week
                $timeDate = Carbon::parse($timeDate)->addWeek()->format('Y-m-d');
            } else { // get the next different day
                $dayName = $newDayName;
                $timeCountInDay = $this->getDoctorTimePeriodsInDay($days[$count % count($days)], $days[$count % count($days)]['day_code'], true);
                if ($timeDate instanceof Carbon)
                    $format = $timeDate->format('Y-m-d');
                else
                    $format = $timeDate;
                $timeDate = $this->getMatchedDateToDayName($dayName, $format, 7);
            }
            return $this->getFirstAvailableTime($doctorId, $timeCountInDay, $days, $timeDate, $count);
        } else {
            $doctorTimes = $this->getDoctorTimesInDay($doctorId, $dayName);
            foreach ($doctorTimes as $key => $dTime) {
                $reservation = $this->getReservationInTime($doctorId, $timeDate, $dTime['from_time'], $dTime['to_time']);
                if ($reservation != null)
                    continue;
                else
                    return ['date' => $timeDate, 'day_name' => trans('messages.' . $dayName),
                        'day_code' => trans('messages.' . $dayName . ' Code'), 'from_time' => $dTime['from_time'], 'to_time' => $dTime['to_time']];
            }
        }
    }

    public function checkReservationInDate($doctorId, $dayDate, $fromTime, $toTime)
    {
        // effect by date
        $reservation = Reservation::where([
            ['doctor_id', '=', $doctorId],
            ['day_date', '=', Carbon::parse($dayDate)->format('Y-m-d')],
            ['from_time', '=', $fromTime],
            ['to_time', '=', $toTime],
        ])->where('approved', '!=', 2)->first();
        if ($reservation != null)
            return true;

        else
            return false;
    }

    public function getReservedDay($doctor_id, $day_date)
    {
        // effect by date
        return ReservedTime::where('doctor_id', $doctor_id)->whereDate('day_date', $day_date)->first();
    }

}

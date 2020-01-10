<?php

namespace App\Traits;

use App\Models\CustomPage;
use App\Models\Doctor;
use App\Models\Favourite;
use App\Models\Message;
use App\Models\Provider;
use App\Models\Reservation;
use App\Models\User;
use App\Models\UserRecord;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

trait UserTrait
{
    public function getUserById($id)
    {
        return User::find($id);
    }

    public function getUserByIDNumber($id_number)
    {
        return User::where('id_number', $id_number)->first();
    }

    public function getUserByMobile($mobile)
    {
        $user = User::where('mobile', $mobile)->first();
        if ($user == null) {
            if (preg_match("~^0\d+$~", $mobile)) {
                $mobile = substr($mobile, 1);
            } else {
                $mobile = '0' . $mobile;
            }
            $user = User::where('mobile', $mobile)->first();
        }
        return $user;
    }

    public function getUserByMobileOrEmailOrID($mobile = '', $email = '', $id_number = '')
    {
        if (empty($mobile) && empty($email))
            return null;
        if (preg_match("~^0\d+$~", $mobile)) {
            $mobile2 = substr($mobile, 1);
        } else {
            $mobile2 = '0' . $mobile;
        }
        $user = User::query();
        if (!empty($id_number))
            $user->orWhere('id_number', $id_number);
        if (!empty($mobile)) {
            $user->orWhere('mobile', $mobile);
            $user->orWhere('mobile', $mobile2);
        }
        if (!empty($email))
            $user->orWhere('email', $email);
        return $user->first();
    }

    public function getUserByEmail($email)
    {
        return User::where('email', $email)->first();
    }

    public function authUserByIDNumber($id_number)
    {
        $user = User::where('id_number', $id_number)->first();
        $token = Auth::guard('user-api')->attempt(['id_number' => $id_number, 'password' => 'none']);
        if ($token) {
            $user->update(['api_token' => $token]);
            return $user;
        }
        return null;
    }

    /*public function authUserByIDOrMobile($id_number = '', $mobile = '', $email = '')
    {
        $mobile2 = '';
        if (preg_match("~^0\d+$~", $mobile)) {
            $mobile2 = substr($mobile, 1);
        } else {
            $mobile2 = '0' . $mobile;
        }

        $query = DB::table('users')
            ->select(DB::raw("*, if(id_number = '" . $id_number . "', 1, 0) as id_correct,
            if(email = '" . $email . "', 1, 0) as email_correct,
            if(mobile = '" . $mobile2 . "', 1, 0) as mobile2_correct,
            if(mobile = '" . $mobile . "', 1, 0) as mobile_correct"))
            ->limit(1);

        if (!empty($id_number))
            $query->where('id_number', $id_number);
        if (!empty($mobile)) {
            $query->orWhere('mobile', $mobile);
            $query->orWhere('mobile', $mobile2);
        }
        if (!empty($email))
            $query->orWhere('email', $email);

        $user = $query->first();

        if (!$user)
            return null;

        //$token = JWTAuth::getToken();
        //if ($token) {
        //  JWTAuth::setToken($token)->invalidate();
        //}

        if ($user->id_correct) {
            $token = Auth::guard('user-api')->attempt(['id_number' => $id_number, 'password' => 'none']);
        } else if ($user->email_correct) {
            $token = Auth::guard('user-api')->attempt(['email' => $email, 'password' => 'none']);
        } else if ($user->mobile_correct) {
            $token = Auth::guard('user-api')->attempt(['mobile' => $mobile, 'password' => 'none']);
        } else if ($user->mobile2_correct) {
            $token = Auth::guard('user-api')->attempt(['mobile' => $mobile2, 'password' => 'none']);
        } else
            return null;


        //   $query->update(['api_token'=>$token]);

        // to allow open  app on more device with the same account
        if ($token) {
            $newToken = new \App\Models\Token(['user_id' => $user->id, 'api_token' => $token]);

            return response() -> json($user);
            $user->tokens()->save($newToken);
            //last access token
            $user->update(['api_token' => $token]);

            $q = $query->first();
            $user = \App\Models\User::hydrate([$q])->first();
            $user->makeVisible(['insurance_company_id']);
            return $user;
        }

        return null;
    }*/


    public function authUserByIDOrMobile($id_number = '', $mobile = '', $email = '')
    {
        $mobile2 = '';
        if (preg_match("~^0\d+$~", $mobile)) {
            $mobile2 = substr($mobile, 1);
        } else {
            $mobile2 = '0' . $mobile;
        }

        $query = User::select(DB::raw("*, if(id_number = '" . $id_number . "', 1, 0) as id_correct,
            if(email = '" . $email . "', 1, 0) as email_correct,
            if(mobile = '" . $mobile2 . "', 1, 0) as mobile2_correct,
            if(mobile = '" . $mobile . "', 1, 0) as mobile_correct"))
            ->limit(1);


        if (!empty($id_number))
            $query->where('id_number', $id_number);
        if (!empty($mobile)) {
            $query->orWhere('mobile', $mobile);
            $query->orWhere('mobile', $mobile2);
        }
        if (!empty($email))
            $query->orWhere('email', $email);

        $user = $query->first();

        if (!$user)
            return null;

        //$token = JWTAuth::getToken();
        //if ($token) {
        //  JWTAuth::setToken($token)->invalidate();
        //}

        if ($user->id_correct) {
            $token = Auth::guard('user-api')->attempt(['id_number' => $id_number, 'password' => 'none']);
        } else if ($user->email_correct) {
            $token = Auth::guard('user-api')->attempt(['email' => $email, 'password' => 'none']);
        } else if ($user->mobile_correct) {
            $token = Auth::guard('user-api')->attempt(['mobile' => $mobile, 'password' => 'none']);
        } else if ($user->mobile2_correct) {
            $token = Auth::guard('user-api')->attempt(['mobile' => $mobile2, 'password' => 'none']);
        } else
            return null;


        //   $query->update(['api_token'=>$token]);

        // to allow open  app on more device with the same account
        if ($token) {
            $newToken = new \App\Models\UserToken(['user_id' => $user->id, 'api_token' => $token]);
            $user->tokens()->save($newToken);
            //last access token
            $user->update(['api_token' => $token]);

           /* $q = $query->first();
            $user = \App\Models\User::hydrate([$q])->first();*/
            $user->makeVisible(['insurance_company_id']);
            return $user;
        }

        return null;
    }

    public function authUserByMobile($mobile)
    {
        $query = User::where('mobile', $mobile);
        $user = $query->first();
        $token = Auth::guard('user-api')->attempt(['mobile' => $mobile, 'password' => 'none']);

        if ($token) {
            $newToken = new \App\Models\UserToken(['user_id' => $user->id, 'api_token' => $token]);
            $user->tokens()->save($newToken);
            //last access token
            $user->update(['api_token' => $token]);
            return $user;
        }

        if (preg_match("~^0\d+$~", $mobile)) {
            $mobile = substr($mobile, 1);
        } else {
            $mobile = '0' . $mobile;
        }
        $query2 = User::where('mobile', $mobile);
        $user2 = $query->first();

        $token = Auth::guard('user-api')->attempt(['mobile' => $mobile, 'password' => 'none']);
        if ($token) {
            $newToken = new \App\Models\UserToken(['user_id' => $user2->id, 'api_token' => $token]);
            $user2->tokens()->save($newToken);
            //last access token
            $user2->update(['api_token' => $token]);
            return $user;
        }
        return null;
    }

    public function authUserByEmail($email)
    {
        $query = User::where('email', $email);
        $user = $query->first();
        $token = Auth::guard('user-api')->attempt(['email' => $email, 'password' => 'none']);
        if ($token) {
            $newToken = new \App\Models\UserToken(['user_id' => $user->id, 'api_token' => $token]);
            $user->tokens()->save($newToken);
            //last access token
            $user->update(['api_token' => $token]);
            return $user;
        }
        return null;
    }

    public function getData($id)
    {
        $user = User::with(['insuranceCompany' => function ($q) {
            $q->select('id', 'image', DB::raw('name_' . app()->getLocale() . ' as name'));
        }, 'city' => function ($q) {
            $q->select('id', DB::raw('name_' . app()->getLocale() . ' as name'));
        }])->find($id);

        if ($user)
            return $user->makeHidden(['api_token', 'activation_code']);
        return null;
    }

    public function getAllData($id, $activation = 0)
    {
        $user = User::with(['insuranceCompany' => function ($q) {
            $q->select('id', 'image', DB::raw('name_' . app()->getLocale() . ' as name'));
        }, 'city' => function ($q) {
            $q->select('id', DB::raw('name_' . app()->getLocale() . ' as name'));
        }])->find($id);

        if ($user) {
            $user->makeVisible(['api_token']);
            if ($activation == 1)
                $user->makeVisible(['activation_code']);
            else
                $user->makeHidden(['activation_code']);
        }
        return $user;
    }

    public function getCurrentReservations($id)
    {
        return Reservation::current()->with([
            'doctor' => function ($q) {
                $q->select('id', 'photo', 'specification_id', DB::raw('name_' . app()->getLocale() . ' as name'))->with(['specification' => function ($qq) {
                    $qq->select('id', DB::raw('name_' . app()->getLocale() . ' as name'));
                }]);
            }, 'provider' => function ($que) {
                $que->join('reservations', 'providers.id', '=', 'reservations.provider_id')->select('providers.id', 'providers.provider_id', 'name_ar');
            }, 'coupon' => function ($qu) {
                $qu->select('id', 'coupons_type_id', 'title', 'code', 'photo', 'price');
            }
            ,
            'paymentMethod' => function ($qu) {
                $qu->select('id', DB::raw('name_' . app()->getLocale() . ' as name'));
            }, 'people'])->where('user_id', $id)->where('day_date', '>=', Carbon::now()->format('Y-m-d'))
            ->orderBy('day_date')->orderBy('order')->paginate(10);
    }

    public function getFinishedReservations($id)
    {
        return Reservation::finished()->with(['commentReport' => function ($q) use ($id) {
            $q->where('user_id', $id);
        }, 'doctor' => function ($q) {
            $q->select('id', 'specification_id', DB::raw('name_' . app()->getLocale() . ' as name'))->with(['specification' => function ($qq) {
                $qq->select('id', DB::raw('name_' . app()->getLocale() . ' as name'));
            }]);
        }, 'provider' => function ($que) {
            $que->select('id', 'provider_id', DB::raw('name_' . app()->getLocale() . ' as name'));
        }, 'coupon' => function ($qu) {
            $qu->select('id', 'coupons_type_id', 'title', 'code', 'photo', 'price');
        }
            , 'paymentMethod' => function ($qu) {
                $qu->select('id', DB::raw('name_' . app()->getLocale() . ' as name'));
            }, 'people'])
            ->where('user_id', $id)
            ->orderBy('day_date')->orderBy('order')->paginate(10);
    }

    public function getFavourDoctors($id)
    {

        return Doctor::with(['provider' => function ($qq) {
            $qq->select('id', 'provider_id', 'status', 'type_id', DB::raw('name_' . $this->getCurrentLang() . ' as name'))
                ->with(['type' => function ($q) {
                    $q->select('id', DB::raw('name_' . $this->getCurrentLang() . ' as name'));
                }]);
        }, 'specification' => function ($qu) {
            $qu->select('id', DB::raw('name_' . $this->getCurrentLang() . ' as name'));
        }, 'nickname' => function ($qu) {
            $qu->select('id', DB::raw('name_' . $this->getCurrentLang() . ' as name'));
        }
        ])
            ->whereIn('id', function ($q) use ($id) {
                $q->select('doctor_id')->from('user_favourites')->where('user_id', $id)->whereNotNull('doctor_id')->orderBy('created_at', 'DESC');
            })->select('id', 'rate', 'nickname_id', 'specification_id', 'provider_id', 'photo', 'status',
                DB::raw('name_' . $this->getCurrentLang() . ' as name'))->paginate(10);

    }

    public function getFavourProviders($id, $longitude = null, $latitude = null, $order = "ASC", $rate = 0)
    {
        $provider = Provider::query();
        $provider = $provider->with(['type' => function ($q) {
            $q->select('id', DB::raw('name_' . $this->getCurrentLang() . ' as name'));
        }, 'city' => function ($qu) {
            $qu->select('id', DB::raw('name_' . $this->getCurrentLang() . ' as name'));
        }, 'district' => function ($que) {
            $que->select('id', DB::raw('name_' . $this->getCurrentLang() . ' as name'));
        },
            'provider' => function ($qqqq) {
                $qqqq->select('id', DB::raw('name_' . app()->getLocale() . ' as name'));
            }
        ])
            ->whereIn('id', function ($quer) use ($id) {
                $quer->select('provider_id')->from('user_favourites')->where('user_id', $id)->whereNotNull('provider_id')->orderBy('created_at', 'DESC');
            });

        if ($longitude != null && !empty($longitude) && $latitude != null && !empty($latitude)) {
            $provider = $provider->select('id', 'rate', 'logo', 'longitude', 'latitude', 'type_id', 'street', 'address', 'city_id', 'district_id', 'provider_id', 'status',
                DB::raw('name_' . $this->getCurrentLang() . ' as name'),
                DB::raw('(3959 * acos(cos(radians(' . $latitude . ')) * cos(radians(latitude)) * cos(radians(longitude) - radians(' . $longitude . ')) + sin(radians(' . $latitude . ')) * sin(radians(latitude)))) AS distance'));
            if ($rate == 1) {
                $provider = $provider->orderBy('rate', 'DESC')->orderBy('distance', $order);
            } else {
                $provider = $provider->orderBy('distance', $order);
            }
        } else {
            $provider = $provider->select('id', 'rate', 'logo', 'longitude', 'latitude', 'type_id', 'street', 'address', 'city_id', 'district_id', 'provider_id', 'status',
                DB::raw('name_' . $this->getCurrentLang() . ' as name'), DB::raw("'0' as distance"));
            if ($rate == 1) {
                $provider = $provider->orderBy('rate', 'DESC');
            }
        }
        return $provider->paginate(10);
    }

    public function getProvidersBranch($userId = null, $longitude = null, $latitude = null, $order = "ASC", $rate = 0, $type_id = [])
    {
        $provider = Provider::query();
        $provider = $provider->with(['type' => function ($q) {
            $q->select('id', DB::raw('name_' . $this->getCurrentLang() . ' as name'));
        }, 'favourites' => function ($qu) use ($userId) {
            $qu->where('user_id', $userId)->select('provider_id');
        }, 'city' => function ($q) {
            $q->select('id', DB::raw('name_' . $this->getCurrentLang() . ' as name'));
        }, 'district' => function ($q) {
            $q->select('id', DB::raw('name_' . $this->getCurrentLang() . ' as name'));
        }])->where('provider_id', '!=', null);
        //$provider = $provider->get();
        if (is_array($type_id) && count($type_id) > 0) {
            $provider = $provider->whereIn('type_id', $type_id);
        }
        if ($longitude != null && !empty($longitude) && $latitude != null && !empty($latitude)) {
            $provider = $provider->select('id', 'rate', 'logo', 'longitude', 'latitude', 'type_id', 'street', 'address', 'city_id', 'district_id', 'provider_id', 'status',
                DB::raw('name_' . $this->getCurrentLang() . ' as name'),
                DB::raw('(3959 * acos(cos(radians(' . $latitude . ')) * cos(radians(latitude)) * cos(radians(longitude) - radians(' . $longitude . ')) + sin(radians(' . $latitude . ')) * sin(radians(latitude)))) AS distance'));
            if ($rate == 1) {
                $provider = $provider->orderBy('rate', 'DESC')->orderBy('distance', $order);
            } else {
                $provider = $provider->orderBy('distance', $order);
            }
        } else {
            $provider = $provider->select('id', 'rate', 'logo', 'longitude', 'latitude', 'type_id', 'street', 'address', 'city_id', 'district_id', 'provider_id', 'status',
                DB::raw('name_' . $this->getCurrentLang() . ' as name'), DB::raw("'0' as distance"));
            if ($rate == 1) {
                $provider = $provider->orderBy('rate', 'DESC');
            }
        }
        return $provider->paginate(10);
    }


    public function checkDoctorInFavourites($user_id, $doctor_id)
    {
        return Favourite::where('user_id', $user_id)->where('doctor_id', $doctor_id)->first();
    }

    public function checkProviderInFavourites($user_id, $provider_id)
    {
        return Favourite::where('user_id', $user_id)->where('provider_id', $provider_id)->first();
    }

    public function getUserMessageByID($id, $userId = null)
    {
        $message = Message::query();
        if ($userId != null)
            $message = $message->where('user_id', $userId)->whereNull('message_id');

        return $message->where('id', $id)->first();
    }

    public function getLastMessageForUser($id)
    {
        return Message::where('user_id', $id)->whereNull('message_id')->orderBy('order', 'DESC')->first();
    }

    public function checkUserMessageById($id, $msg_id)
    {
        return Message::where('user_id', $id)->with('messages')->where('id', $msg_id)->first();
    }

    public function getMessages($id)
    {
        return Message::where('user_id', $id)->whereNull('message_id')->orderBy('order')->paginate(10);
    }

    public function getMessageReplies($msg_id)
    {
        return Message::with(['user' => function ($q) {
            $q->select('id', 'name');
        }, 'manager' => function ($qu) {
            $qu->select('id', DB::raw('name_' . app()->getLocale() . ' as name'));
        }])->where('message_id', $msg_id)
            ->select('id', 'message', 'manager_id', 'user_id', 'message_no', 'order')->orderBy('order')->paginate(10);
    }

    public function getUserRecords($id)
    {
        return UserRecord::with(['specification' => function ($q) {
            $q->select('id', DB::raw('name_' . app()->getLocale() . ' as name'));
        }, 'attachments'])->where('user_id', $id)->paginate(10);
    }

    public function checkLogin($request)
    {
        try {
            $request->headers->set('Authorization', 'Bearer ' . $request->api_token);
            JWTAuth::parseToken()->authenticate();
            return true;
        } catch (\Exception $e) {
            return false;
        } catch (\Throwable $e) {
            return false;
        }
    }

}

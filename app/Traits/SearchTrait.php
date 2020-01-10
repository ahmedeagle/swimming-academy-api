<?php

namespace App\Traits;

use App\Models\Doctor;
use App\Models\DoctorTime;
use App\Models\Provider;
use Illuminate\Http\Request;
use DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use function foo\func;

trait SearchTrait
{
    use DoctorTrait;

    public function searchResult($userId = null, Request $request = null)
    {

        $order = (isset($request->order) && strtolower($request->order) == "desc") ? "DESC" : "ASC";
        $rate = $request->rate;
        $queryStr = $request->queryStr;
        $query = Provider::query();


        $provider = $query->with(['type' => function ($q) {
            $q->select('id', DB::raw('name_' . app()->getLocale() . ' as name'));
        }, 'favourites' => function ($qu) use ($userId) {
            $qu->where('user_id', $userId)->select('provider_id');
        }, 'city' => function ($q) {
            $q->select('id', DB::raw('name_' . app()->getLocale() . ' as name'));
        }, 'district' => function ($q) {
            $q->select('id', DB::raw('name_' . $this->getCurrentLang() . ' as name'));
        }])->where('providers.status', true)->whereNotNull('providers.provider_id');


        $provider = $provider->whereHas('provider', function ($qq) use ($queryStr) {
            $qq->where('name_en', 'LIKE', '%' . trim($queryStr) . '%')->orWhere('name_ar', 'LIKE', '%' . trim($queryStr) . '%');
        });

        /*  $doctors = Doctor::where(function ($dc) use ($queryStr) {
             $dc->where('name_en', 'LIKE', '%' . trim($queryStr) . '%');
             $dc->orWhere('name_ar', 'LIKE', '%' . trim($queryStr) . '%');
         })->orWhereHas('specification', function ($spc) use ($queryStr) {
             $spc->where('name_en', 'LIKE', '%' . trim($queryStr) . '%');
             $spc->orWhere('name_ar', 'LIKE', '%' . trim($queryStr) . '%');
         })  ;*/

        // City
        if (isset($request->city_id) && $request->city_id != 0) {
            $provider = $provider->where('city_id', $request->city_id);
            /* $doctors = $doctors->whereHas('provider', function ($cit) use ($request) {
                 $cit->where('city_id', $request->city_id);
             });*/
        }
        // District
        if (isset($request->district_id) && $request->district_id != 0) {
            $provider = $provider->where('district_id', $request->district_id);
            /*$doctors = $doctors->whereHas('provider', function ($dis) use ($request) {
                $dis->where('district_id', $request->district_id);
            });*/
        }
        // Type
        if (is_array($request->type_id) && count($request->type_id) > 0) {
            $type_id = $request->type_id;
            $provider = $provider->whereHas('provider', function ($q) use ($type_id) {
                $q->whereIn('type_id', $type_id);
            });
            /*  $doctors = $doctors->whereHas('provider', function ($type) use ($type_id) {
                  $type->whereHas('provider', function ($type) use ($type_id) {
                      $type->whereIn('type_id', $type_id);
                  });
              });*/
        }
        // Insurance Companies
        if (isset($request->insurance_company_id) && $request->insurance_company_id != 0) {
            $provider = $provider->whereHas('doctors', function ($que) use ($request) {
                $que->whereHas('manyInsuranceCompanies', function ($quer) use ($request) {
                    $quer->where('insurance_company_id', $request->insurance_company_id);
                });
            });

            /*  $doctors = $doctors->whereHas('manyInsuranceCompanies', function ($quer) use ($request) {
                  $quer->where('insurance_company_id', $request->insurance_company_id);
              });*/

        }
        //  Name
        if (isset($request->doctor_name) && !empty($request->doctor_name)) {
            $provider = $provider->whereHas('doctors', function ($query) use ($request) {
                $query->where('name_en', 'LIKE', '%' . trim($request->doctor_name) . '%')->orWhere('name_ar', 'LIKE', '%' . trim($request->doctor_name) . '%');
            });
        }

        // Doctor Nickname
        if (isset($request->nickname_id) && $request->nickname_id != 0) {
            $provider = $provider->whereHas('doctors', function ($query) use ($request) {
                $query->where('nickname_id', $request->nickname_id);
            });
        }

        // Doctor specification
        if (isset($request->specification_id) && $request->specification_id != 0) {
            $provider = $provider->whereHas('doctors', function ($query) use ($request) {
                $query->where('specification_id', $request->specification_id);
            });
            //  $doctors = $doctors->where('specification_id', $request->specification_id);
        }

        // Doctor Gender
        if (isset($request->doctor_gender) && ($request->doctor_gender == 1 || $request->doctor_gender == 2)) {
            $provider = $provider->whereHas('doctors', function ($query) use ($request) {
                $query->where('gender', $request->doctor_gender);
            });
            //$doctors = $doctors->where('gender', $request->doctor_gender);
        }

        // Price From
        if (isset($request->price_from)) {
            $provider = $provider->whereHas('doctors', function ($qque) use ($request) {
                $qque->where('price', '>=', $request->price_from);
            });
            // $doctors = $doctors->where('price', '>=', $request->price_from);
        }
        // Price To
        if (isset($request->price_to) && $request->price_to != 0) {
            $provider = $provider->whereHas('doctors', function ($qquer) use ($request) {
                $qquer->where('price', '<=', $request->price_to);
            });

            // $doctors = $doctors->where('price', '<=', $request->price_to);
        }
        // Reservation available by given Date
        if (isset($request->day_date)) {
            $dayCode = date('D', strtotime($request->day_date));
            $provider = $provider->whereHas('doctors', function ($qg) use ($request, $dayCode) {
                $qg->whereHas('times', function ($qqq) use ($request, $dayCode) {
                    $qqq->whereRaw('LOWER(day_code) = ?', strtolower($dayCode))->whereNotIn('doctor_id', function ($qqqu) use ($request) {
                        $qqqu->select('doctor_id')->from('reserved_times')->where('day_date', date('Y-m-d', strtotime($request->day_date)));
                    });
                });
            });

            /*
                        $doctors = $doctors->whereHas('times', function ($qqq) use ($request, $dayCode) {
                            $qqq->whereRaw('LOWER(day_code) = ?', strtolower($dayCode))->whereNotIn('doctor_id', function ($qqqu) use ($request) {
                                $qqqu->select('doctor_id')->from('reserved_times')->where('day_date', date('Y-m-d', strtotime($request->day_date)));
                            });
                        });*/
        }


        if (isset($request->longitude) && !empty($request->longitude) && isset($request->latitude) && !empty($request->latitude)) {
            $provider = $provider->select('id',
                'rate',
                'logo',
                'longitude',
                'latitude',
                'email',
                'street',
                'address',
                'mobile',
                'commercial_no',
                'branch_no',
                'type_id',
                'city_id', 'district_id', 'provider_id',
                DB::raw('name_' . $this->getCurrentLang() . ' as name'),
                DB::raw('(3959 * acos(cos(radians(' . $request->latitude . ')) * cos(radians(latitude)) * cos(radians(longitude) - radians(' . $request->longitude . ')) + sin(radians(' . $request->latitude . ')) * sin(radians(latitude)))) AS distance'),
              //  DB::raw("'0' as doctor"),
                DB::raw("'0' as price"),
                DB::raw("0 as specification_id")

            );

            /*    $doctors = $doctors->select('id',
                    'rate',
                    'photo as logo',
                    DB::raw("'0' as longitude"),
                    DB::raw("'0' as latitude"),
                    DB::raw("'0' as email"),
                    DB::raw("'0' as street"),
                    DB::raw("'0' as address"),
                    DB::raw("'0' as mobile"),
                    DB::raw("'0' as commercial_no"),
                    DB::raw("'0' as branch_no"),
                    DB::raw("'0' as type_id"),
                    DB::raw("'0' as city_id"),
                    DB::raw("'0' as district_id"),
                    'provider_id',
                    DB::raw('name_' . $this->getCurrentLang() . ' as name'),
                    DB::raw("'0' AS distance"),
                    DB::raw("'1' as doctor"),
                    'price',
                    'specification_id'
                );*/

            if ($request->rate == 1) {
                $provider = $provider->orderBy('rate', 'DESC')->orderBy('distance', $order);
                //  $doctors = $doctors->orderBy('rate', 'DESC')->orderBy('distance', $order);
            } else {
                $provider = $provider->orderBy('distance', $order);
            }

        } else {
            $provider = $provider
                ->select('id',
                    'rate',
                    'logo',
                    'longitude',
                    'latitude',
                    'email',
                    'street',
                    'address',
                    'mobile',
                    'commercial_no',
                    'branch_no',
                    'type_id',
                    'city_id',
                    'district_id',
                    'provider_id',
                    DB::raw('name_' . $this->getCurrentLang() . ' as name'),
                    DB::raw("'0' as distance"),
                  //  DB::raw("'0' as doctor"),
                    DB::raw("'0' as price"),
                    DB::raw("0 as specification_id")
                );


            /*$doctors = $doctors->select('id',
                'rate',
                'photo as logo',
                DB::raw("'0' as longitude"),
                DB::raw("'0' as latitude"),
                DB::raw("'0' as email"),
                DB::raw("'0' as street"),
                DB::raw("'0' as address"),
                DB::raw("'0' as mobile"),
                DB::raw("'0' as commercial_no"),
                DB::raw("'0' as branch_no"),
                DB::raw("'0' as type_id"),
                DB::raw("'0' as city_id"),
                DB::raw("'0' as district_id"),
                'provider_id',
                DB::raw('name_' . $this->getCurrentLang() . ' as name'),
                DB::raw("'0' as distance"),
                DB::raw("'1' as doctor"),
                'price',
                'specification_id'
            );*/

            if ($request->rate == 1) {
                $provider = $provider->orderBy('rate', 'DESC');
                //  $doctors = $doctors->orderBy('rate', 'DESC');
            } else
                $provider = $provider->orderBy('distance', $order);
        }

        if (isset($request->longitude) && !empty($request->longitude) && isset($request->latitude) && !empty($request->latitude)) {
            if ($request->rate == 1) {
                // $union = $provider->union($doctors)->orderBy('rate', 'DESC')->orderBy('distance', $order);
                $union = $provider->orderBy('rate', 'DESC')->orderBy('distance', $order);
            } else {
                //  $union = $provider->union($doctors)->orderBy('distance', $order);
                $union = $provider->orderBy('distance', $order);
            }
        } else {
            if ($request->rate == 1) {
                //$union = $provider->union($doctors)->orderBy('rate', 'DESC');
                $union = $provider->orderBy('rate', 'DESC');

            } else
                // $union = $provider->union($doctors)->orderBy('distance', $order);
                $union = $provider->orderBy('distance', $order);
        }

        $result = $union->paginate(10);

        return $result;
        /*  $currentPage = $request->filled('page') ? $request->input('page') : 1;
          $perPage = 10;
          $currentItems = array_slice($union, $perPage * ($currentPage - 1), $perPage);
          $result = new LengthAwarePaginator($currentItems, count($union), $perPage, $currentPage, ['path' => 'files', JSON_FORCE_OBJECT]
          );
          return $result;*/
    }


    public
    function searchDateSortedResult($userId = null, Request $request = null)
    {

        try {

            if (isset($request->order)) {
                if (strtolower($request->order) != "asc" && strtolower($request->order) != "desc")
                    $request->order = "ASC";
            } else {
                $request->order = "ASC";
            }


            $specification_id = $request->specification_id;
            $queryStr = $request->queryStr;
            $res = \App\Models\DoctorCalculation::with(['provider' => function ($provider) use ($request, $userId) {

                $provider->with(['type' => function ($q) {
                    $q->select('id', DB::raw('name_' . app()->getLocale() . ' as name'));

                }, 'favourites' => function ($qu) use ($userId) {
                    $qu->where('user_id', $userId)->select('provider_id');
                }, 'city' => function ($q) {
                    $q->select('id', DB::raw('name_' . app()->getLocale() . ' as name'));
                }, 'district' => function ($q) {
                    $q->select('id', DB::raw('name_' . $this->getCurrentLang() . ' as name'));
                }]);
            }])->whereHas('provider', function ($provider) use ($request,$queryStr) {
                $provider->where('providers.status', true);
                // $provider->whereNotNull('providers.provider_id');
                // Provider Name
                $provider = $provider->where(function ($qu) use ($request ,$queryStr) {
                    $qu->where('name_en', 'LIKE', '%' . trim($queryStr) . '%')->orWhere('name_ar', 'LIKE', '%' . trim($queryStr) . '%');
                });

                // City
                if (isset($request->city_id) && $request->city_id != 0) {
                    $provider = $provider->where('city_id', $request->city_id);
                }
                // District
                if (isset($request->district_id) && $request->district_id != 0) {
                    $provider = $provider->where('district_id', $request->district_id);
                }
                // Type
                if (is_array($request->type_id) && count($request->type_id) > 0) {
                    $provider = $provider->whereIn('type_id', $request->type_id);
                }
                // Insurance Companies
                if (isset($request->insurance_company_id) && $request->insurance_company_id != 0) {
                    $provider = $provider->whereHas('doctors', function ($que) use ($request) {
                        $que->whereHas('manyInsuranceCompanies', function ($quer) use ($request) {
                            $quer->where('insurance_company_id', $request->insurance_company_id);
                        });
                    });
                }
                // Doctor Name
                if (isset($request->doctor_name) && !empty($request->doctor_name)) {
                    $provider = $provider->whereHas('doctors', function ($query) use ($request) {
                        $query->where('name_en', 'LIKE', '%' . trim($request->doctor_name) . '%')->orWhere('name_ar', 'LIKE', '%' . trim($request->doctor_name) . '%');
                    });
                }
                // Doctor Nickname
                if (isset($request->nickname_id) && $request->nickname_id != 0) {
                    $provider = $provider->whereHas('doctors', function ($query) use ($request) {
                        $query->where('nickname_id', $request->nickname_id);
                    });
                }
                // Doctor Gender
                if (isset($request->doctor_gender) && ($request->doctor_gender == 1 || $request->doctor_gender == 2)) {
                    $provider = $provider->whereHas('doctors', function ($query) use ($request) {
                        $query->where('gender', $request->doctor_gender);
                    });
                }
                // Price From
                if (isset($request->price_from)) {
                    $provider = $provider->whereHas('doctors', function ($qque) use ($request) {
                        $qque->where('price', '>=', $request->price_from);
                    });
                }
                // Price To
                if (isset($request->price_to) && $request->price_to != 0) {
                    $provider = $provider->whereHas('doctors', function ($qquer) use ($request) {
                        $qquer->where('price', '<=', $request->price_to);
                    });
                }
                // Reservation in Date
                if (isset($request->day_date)) {
                    $dayCode = date('D', strtotime($request->day_date));
                    $provider = $provider->whereHas('doctors', function ($qg) use ($request, $dayCode) {
                        $qg->whereHas('times', function ($qqq) use ($request, $dayCode) {
                            $qqq->whereRaw('LOWER(day_code) = ?', strtolower($dayCode))->whereNotIn('doctor_id', function ($qqqu) use ($request) {
                                $qqqu->select('doctor_id')->from('reserved_times')->where('day_date', date('Y-m-d', strtotime($request->day_date)));
                            });
                        });
                    });
                }

                if (isset($request->longitude) && !empty($request->longitude) && isset($request->latitude) && !empty($request->latitude)) {
                    $provider = $provider->select('rate',
                        'logo',
                        'longitude',
                        'latitude',
                        'email',
                        'street',
                        'address',
                        'mobile',
                        'commercial_no',
                        'branch_no',
                        'type_id',
                        'city_id', 'district_id', 'provider_id',
                        DB::raw('name_' . $this->getCurrentLang() . ' as name'),
                        DB::raw('(3959 * acos(cos(radians(' . $request->latitude . ')) * cos(radians(latitude)) * cos(radians(longitude) - radians(' . $request->longitude . ')) + sin(radians(' . $request->latitude . ')) * sin(radians(latitude)))) AS distance'),
                      //  DB::raw("'0' as doctor"),
                        DB::raw("'0' as price"),
                        DB::raw("0 as specification_id")
                    );
                    if ($request->rate == 1) {
                        $provider->orderBy('rate', 'DESC')->orderBy('distance', $request->order);
                    } else {
                        $provider->orderBy('distance', $request->order);
                    }
                } else {
                    $provider = $provider->select('id',
                        'rate',
                        'logo',
                        'longitude',
                        'latitude',
                        'email',
                        'street',
                        'address',
                        'mobile',
                        'commercial_no',
                        'branch_no',
                        'type_id',
                        'city_id',
                        'district_id',
                        'provider_id',
                        DB::raw('name_' . $this->getCurrentLang() . ' as name'),
                        DB::raw("'0' as distance"),
                      //  DB::raw("'0' as doctor"),
                        DB::raw("'0' as price"),
                        DB::raw("0 as specification_id"));
                    if ($request->rate == 1) {
                        $provider->orderBy('rate', 'DESC');
                    } else
                        $provider->orderBy('id', $request->order);
                }
            })->select('id', 'name_ar', 'name_en', 'provider_id')->where('specification_id', $specification_id)->paginate(10);

            $providers = [];
            // foreach($res->data as $data){
            //   $providers =  $data->provider;
            //}
            //$providers = collect($providers);
            $sorted = $res->sortBy(function ($a) {
                return strtotime($a->available_time);
            })->values()->all();
            $matched = [];
            foreach ($sorted as $sort) {
                if (!in_array($sort->provider->id, $matched)) {
                    $sort->provider->favourite = count($sort->provider->favourites) > 0 ? 1 : 0;
                    $sort->provider->distance = (string)number_format($sort->provider->distance * 1.609344, 2);
                    unset($sort->provider->favourites);
                    if ($sort->provider && !empty($sort->provider)) {
                        $providers[] = $sort->provider;
                        $matched[] = $sort->provider->id;
                    }
                }
            }
            return [$res, $providers, count($matched)];

        } catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }

    }
}

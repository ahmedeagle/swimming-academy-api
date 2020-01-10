<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait PhraseTrait
{

    //get before chars  values  as array then convert string to show
    public function getBeforsChar(array $befors, array $chars)
    {
        $maxIndex = count($chars) - 1;
        $beforeChars = [];
        foreach ($befors as $before) {
            if ($before != $maxIndex)
                array_push($beforeChars, $chars[$before + 1]);
            else
                array_push($beforeChars, 'none');
        }
        $befors = implode(', ', $beforeChars);
        return $befors;
    }


    //get after chars  values  as array then convert string to show
    public function getAftersChar(array $afters, array $chars)
    {
        $maxIndex = count($chars) - 1;
        $afterChars = [];
        foreach ($afters as $after) {
            if ($after != 0)  // not first char in origin array of chars
                array_push($afterChars, $chars[$after - 1]);
            else
                array_push($afterChars, 'none');
        }

        $afters = implode(', ', $afterChars);

        return $afters;
    }

    //get indexs of this char occurences as array
    public function getBefore($char, array $chars)
    {
        $before = [];
        foreach ($chars as $key => $value) {
            if ($value == $char)
                array_push($before, $key);
        }
        return $this->getBeforsChar($before, $chars);
    }

    //get indexs of this char occurences as array
    public function getAfters($char, array $chars)
    {
        $after = [];
        foreach ($chars as $key => $value) {
            if ($value == $char)
                array_push($after, $key);
        }
        return $this->getAftersChar($after, $chars);
    }


    public function maxDistnce($char, array $chars)
    {

        $first = array_search($char, $chars);
        if ($first == 0)
            $first++;

        $last = array_search($char, array_reverse($chars, true)) - 1;

        $distance = abs(($last - $first));
        return $distance;
    }

}

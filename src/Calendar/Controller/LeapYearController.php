<?php

namespace Calendar\Controller;

use Calendar\Model\LeapYear;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class LeapYearController {

    public function index(Request $request, $year) {
        
        $leapYear = new LeapYear();
        if ($leapYear->is_leap_year($year)) {
            $response = new Response('Yep, this is a leap year! '.rand());
        }

        $response = new Response('Nope, this is not a leap year. '.rand());

        $response->setTtl(10);

        return $response;
    }
}
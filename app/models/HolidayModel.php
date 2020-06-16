<?php

namespace App\Model;


use iCal;

class HolidayModel extends BaseDbModel
{

    protected $table = 'holiday';

    function fetchHolidays(){

        try {

            $path = __DIR__.'/../../data/namedaysk-sk.ics';
            $iCal = new iCal($path);

            $events = $iCal->eventsByDateBetween('2020-01-01', '2020-12-31');
//            $events = $iCal->eventsByDate();

            dump( $events);
        } catch (Exception $e) {
            var_dump($e);
        }
    }
}
<?php

namespace App\Model;

use Tracy\Debugger;

class LogModel
{

    /**
     * @property-read \Dibi\Connection $connection
     */
    protected $connection;

    public function __construct(\Dibi\Connection $connection)
    {
        $this->connection = $connection;
    }

    public function save($params): int
    {
//        if($params['image']){
//            $image = $params['image'];
//            unset($params['image']);
//        }

        Debugger::log($params);
        return $this->connection->insert('log', $params)->execute('n');

    }


    static function sumIntervals($intervals){

        $sum = 0;
        foreach ($intervals as $interval){
            if(isset($interval['arrival']) AND isset($interval['leave']) ){
                $diff = $interval['arrival']['time']->diff($interval['leave']['time']);

                $hours = $diff->h;
                $minutes = $diff->i;

                if($minutes>45){
                    $hours++;
                }elseif ($minutes>29){
                    $hours+=0.75;
                }elseif ($minutes>14){
                    $hours+=0.5;
                }elseif ($minutes>0){
                    $hours+=0.25;
                }
                $sum+=$hours;
            }
        }

        return $sum;
    }

    /**
     * @param $logs
     * @return array
     */
    static function makeIntervals($logs)
    {
        //find first arrival
        $hasFirst = false;

        $arrival = false;
        $intervals = [];

        foreach ($logs as $key => $item){
            if (!$hasFirst AND $item['type'] == 'arrival') {
                $hasFirst = true;
                $arrival = $item;
            }

            if ($hasFirst AND $item['type'] == 'leave') {
                $intervals[] = [
                    'arrival' => $arrival,
                    'leave' => $item
                ];
                $arrival = false;
                $hasFirst = false;
            }
        }

        $isWorking = false;

        if ($arrival) {

            $isWorking = $arrival;
            $intervals[] = [
                'arrival' => $arrival
            ];
        }

        return [
            'isWorking' => $isWorking,
            'intervals' => $intervals
        ];
    }

    static function monthDays($year, $month)
    {
        $numOfDays = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        $days = [];
        for ($i = 1; $i <= $numOfDays; $i++) {
            $days[$i] = $i;
        }

        return $days;
    }
}
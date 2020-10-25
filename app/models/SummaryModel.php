<?php

namespace App\Model;

class SummaryModel
{

    /**
     * @property-read \Dibi\Connection $connection
     */
    protected $connection;

    public function __construct(\Dibi\Connection $connection)
    {
        $this->connection = $connection;
    }

    public function fetchAllByYear($year)
    {
        $list = $this->connection->select('*')
            ->from('summary')
            ->where('year = %i', $year)
            ->fetchAll();

        $res = [];
        foreach ($list as $l){
            $data = $l;
            $data['json'] = json_decode($data['json']);;
            $res[$data['month']] = $data;
        }

        return $res;
    }

    public function insert($year, $month, $json)
    {
        $this->connection->insert('summary', [
            'year' => $year,
            'month' => $month,
            'json' => $json,
            'version' => 1
        ])->execute();
    }

    public static function getMonths($year)
    {
        $months = [
            1 => [
                'name' => "Január",
                'days' => LogModel::monthDays($year, 1)
            ],
            [
                'name' => "Február",
                'days' => LogModel::monthDays($year, 2)
            ],
            [
                'name' => "Marec",
                'days' => LogModel::monthDays($year, 3)
            ],
            [
                'name' => "Apríl",
                'days' => LogModel::monthDays($year, 4)
            ],
            [
                'name' => "Máj",
                'days' => LogModel::monthDays($year, 5)
            ],
            [
                'name' => "Jún",
                'days' => LogModel::monthDays($year, 6)
            ],
            [
                'name' => "Júl",
                'days' => LogModel::monthDays($year, 7)
            ],
            [
                'name' => "August",
                'days' => LogModel::monthDays($year, 8)
            ],
            [
                'name' => "September",
                'days' => LogModel::monthDays($year, 9)
            ],
            [
                'name' => "Október",
                'days' => LogModel::monthDays($year, 10)
            ],
            [
                'name' => "November",
                'days' => LogModel::monthDays($year, 11)
            ], [
                'name' => "December",
                'days' => LogModel::monthDays($year, 12)
            ]
        ];

        return $months;
    }

    static function translateWeekDay($weekDay)
    {
        $lang = [
            'Mon' => 'Po',
            'Tue' => 'Ut',
            'Wed' => 'Str',
            'Thu' => 'Št',
            'Fri' => 'Pia',
            'Sat' => 'So',
            'Sun' => 'Ne',
        ];

        return $lang[$weekDay];
    }
}
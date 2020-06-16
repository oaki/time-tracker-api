<?php

namespace App\Model;

class UserModel
{

    /**
     * @property-read \Dibi\Connection $connection
     */
    protected $connection;

    public function __construct(\Dibi\Connection $connection)
    {
        $this->connection = $connection;
    }

    public function findUser($password)
    {
        return $this->connection->select('*')
            ->from('user')
            ->where('password = %s', $password)
            ->fetch();
    }

    public function fetch($id)
    {
        return $this->connection->select('*')
            ->from('user')
            ->where('id = %i', $id)
            ->fetch();
    }

    public function fetchAll()
    {
        return $this->connection->select('*')
            ->from('user')
            ->fetchAll();
    }

    public function fetchAllWithLogForToday()
    {

        $endDate = date("Y-m-d", strtotime('tomorrow'));

        $startDate = date('Y-m-d', strtotime('today'));
        $users = array_map(function ($user) use ($startDate, $endDate) {

            $logs = $this->connection->select('*')
                ->from('log')
                ->where('user_id = %i', $user['id'], 'AND time>=%s', $startDate, 'AND time<%s', $endDate)
                ->fetchAll();


            $logIntervals = LogModel::makeIntervals($logs);

            $user['today_logs'] = $logIntervals['intervals'];
            $user['is_working'] = $logIntervals['isWorking'];


            return $user;
        }, $this->fetchAll());
        return $users;
    }

    public function fetchAllLog($userId)
    {
        return $this->connection->select('*')
            ->from('log')
            ->where('user_id = %i', $userId)
            ->fetchAll();
    }

    public function filterLog($userId, $year, $month)
    {
        $queryDate = $year . '-' . $month . '-01';
        // First day of the month.
        $startDate = date('Y-m-01', strtotime($queryDate));

// Last day of the month.
        $endDate = date('Y-m-t', strtotime($queryDate));

        $list = $this->connection->select('*, (SELECT [image].id FROM [image] WHERE [log].id = [image].log_id LIMIT 1) AS image_id')
            ->from('log')
            ->where('user_id = %i', $userId)
            ->where('time >= %s', $startDate)
            ->where('time <= %s', $endDate)
            ->fetchAll();

        return $list;
    }

    public function filterNewLog($userId, $year, $month)
    {
        $queryDate = $year . '-' . $month . '-01';
        // First day of the month.
        $startDate = date('Y-m-01', strtotime($queryDate));

        $maxDays = date('t', strtotime($queryDate));


        // Last day of the month.
        $endDate = date('Y-m-t', strtotime($queryDate));

        $list = $this->connection->select('log.*, image.id as image_id, DAY(time) AS day')
            ->from('log')
            ->leftJoin('image')->on('(log.id = image.log_id)')
            ->where('user_id = %i', $userId)
            ->where('time >= %s', $startDate)
            ->where('time <= %s', $endDate)
            ->fetchAssoc('day,id');

        $newList = array_map(function ($day) use ($list){

            if(isset($list[$day])){
                $intervals = LogModel::makeIntervals($list[$day]);
                return [
                    'intervals'=>$intervals,
                    'sum'=>LogModel::sumIntervals($intervals)
                ];
            }
            return [
                'intervals'=>[]
            ];
        }, LogModel::monthDays($year, $month));
        dump($list);
        dump($newList);
        exit;

        $dayList = [];
        for ($i = 1; $i <= $maxDays; $i++) {
            if (isset($list[$i])) {
                $dayLogs = $list[$i];
                $count = count($dayLogs);

                $dayList[$i] = $list[$i];
            }
        }

        return $list;
    }




    public function add($values)
    {
        return $this->connection->insert('user', [
            'name' => $values['name'],
            'password' => $values['password'],
        ])->execute();
    }

    public function delete($id)
    {
        return $this->connection->delete('user')->where('id=%i', $id)->execute();
    }
}
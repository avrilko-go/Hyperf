<?php

declare(strict_types=1);

namespace App\Model\Cms;


use App\Model\Model;

class LinLog extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'lin_log';

    /**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'default';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['message', 'user_id', 'username', 'status_code', 'method', 'path'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];

    const CREATED_AT = 'create_time';

    const UPDATED_AT = 'update_time';

    /**
 * 日志列表
 *
 * @param $params
 *
 * @return array
 * @throws \App\Exception\Cms\ParameterException
 */
    public function getLogs($params) :array
    {
        $filter = [];
        if (isset($params['name']) && !empty($params['name'])) {
            $filter ['username'] = $params['name'];
        }

        list($start, $count) = $this->paginate();

        $logs = $this->query()->select(['username', 'message'])->selectRaw('create_time as time')->where($filter);
        if (isset($params['start']) && isset($params['end'])) {
            $logs->whereBetween('create_time', [$params['start'], $params['end']]);
        }
        if (isset($params['keyword']) && !empty($params['keyword'])) {
            $logs->where('message', 'like', "{$params['keyword']}%");
        }

        $totalNums = $logs->count();
        $logs = $logs->offset($start)->limit($count)->orderByDesc('create_time')->get();

        $result = [
            'items' => $logs,
            'total' => $totalNums,
            'count' => $count,
            'page' => $this->request->query('page'),
            'total_page' => ceil($totalNums / $count)
        ];
        return $result;
    }


    /**
     * 人员列表
     *
     * @param $params
     *
     * @return array
     * @throws \App\Exception\Cms\ParameterException
     */
    public function getUsers($params) :array
    {
        list($start, $count) = $this->paginate();

        $logs = $this->query()->select(['username']);

        $totalNums = $logs->count();
        $logs = $logs->offset($start)->limit($count)->get()->unique()->pluck('username');

        $result = [
            'items' => $logs,
            'total' => $totalNums,
            'count' => $count,
            'page' => $this->request->query('page'),
            'total_page' => ceil($totalNums / $count)
        ];
        return $result;
    }

}
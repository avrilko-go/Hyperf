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
    protected $fillable = ['message', 'user_id', 'user_name', 'status_code', 'method', 'path'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];

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
        if (isset($params['name'])) {
            $filter ['user_name'] = $params['name'];
        }

        list($start, $count) = $this->paginate();

        $logs = $this->query()->select(['user_name', 'message'])->selectRaw('created_at as time')->where($filter);
        if (isset($params['start']) && isset($params['end'])) {
            $logs->whereBetween('created_at', [$params['start'], $params['end']]);
        }
        if (isset($params['keyword'])) {
            $logs->where('message', 'like', "{$params['keyword']}%");
        }

        $totalNums = $logs->count();
        $logs = $logs->offset($start)->limit($count)->orderByDesc('created_at')->get();

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
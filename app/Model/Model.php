<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf-cloud/hyperf/blob/master/LICENSE
 */

namespace App\Model;

use App\Exception\Cms\ParameterException;
use Hyperf\DbConnection\Model\Model as BaseModel;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;

abstract class Model extends BaseModel
{
    /**
     * @Inject()
     * @var RequestInterface
     */
    protected $request;

    /**
     * 默认分页处理
     *
     * @return array
     *
     * @throws ParameterException
     */
    public function paginate()
    {
        $count = $this->request->query('count');
        $start = $this->request->query('page');
        $count = $count >= 15 ? 15 : $count;
        $start = $start * $count;
        if ($start < 0 || $count < 0) throw new ParameterException();

        return [$start, $count];
    }
}

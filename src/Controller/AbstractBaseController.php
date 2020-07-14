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
namespace Mzh\Helper\Controller;

use Hyperf\Contract\SessionInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Psr\Container\ContainerInterface;

abstract class AbstractBaseController
{
    /**
     * @Inject
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @Inject
     * @var RequestInterface
     */
    protected $request;

    /**
     * @Inject
     * @var ResponseInterface
     */
    protected $response;


    public function json($data = [], $code = 200, $msg = 'success', $thr = null)
    {
        $res = [];
        if ($thr != null) {
            $res = $thr;
        }
        $res['status'] = $code;
        $res['message'] = $msg;
        $res['data'] = $data;

        return $this->response->json($res);
    }

    public function error($msg, $code = 500)
    {
        $data['status'] = $code;
        $data['message'] = empty($msg) ? '错误' : $msg;
        $data['data'] = [];
        return $this->response->json($data);
    }

}

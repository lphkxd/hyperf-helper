<?php
declare(strict_types=1);


use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Redis\RedisFactory;
use Hyperf\Utils\ApplicationContext;
use Hyperf\Utils\Context;
use Mzh\Helper\Session\Session;
use Psr\Http\Message\ServerRequestInterface;
use Hyperf\Contract\SessionInterface;
use Hyperf\Session\FlashTrait;


if (!function_exists('redis')) {
    /**
     * Redis
     * @param string $name
     * @return \Hyperf\Redis\RedisProxy|Redis
     */
    function redis($name = 'default')
    {
        return ApplicationContext::getContainer()->get(RedisFactory::class)->get($name);
    }
}

if (!function_exists('Logger')) {
    /**
     * Redis
     * @return StdoutLoggerInterface
     */
    function Logger()
    {
        return ApplicationContext::getContainer()->get(StdoutLoggerInterface::class);
    }
}

if (!function_exists('getClientIp')) {
    function getClientIp()
    {
        /**
         * @var ServerRequestInterface $request
         */
        $request = Context::get(ServerRequestInterface::class);
        $ip_addr = $request->getHeaderLine('x-forwarded-for');
        if (verifyIp($ip_addr)) {
            return $ip_addr;
        }
        $ip_addr = $request->getHeaderLine('remote-host');
        if (verifyIp($ip_addr)) {
            return $ip_addr;
        }
        $ip_addr = $request->getHeaderLine('x-real-ip');
        if (verifyIp($ip_addr)) {
            return $ip_addr;
        }
        $ip_addr = $request->getServerParams()['remote_addr'] ?? '0.0.0.0';
        if (verifyIp($ip_addr)) {
            return $ip_addr;
        }
        return '0.0.0.0';
    }
}


if (!function_exists('getContainer')) {
    function getContainer($id)
    {
        return ApplicationContext::getContainer()->get($id);
    }
}

if (!function_exists('verifyIp')) {
    function verifyIp($realip)
    {
        return filter_var($realip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
    }
}
//输出控制台日志
if (!function_exists('p')) {
    function p($val, $title = null, $starttime = '')
    {
        print_r('[ ' . date("Y-m-d H:i:s") . ']:');
        if ($title != null) {
            print_r("[" . $title . "]:");
        }
        print_r($val);
        print_r("\r\n");
    }
}

if (!function_exists('uuid')) {
    function uuid($length)
    {
        if (function_exists('random_bytes')) {
            $uuid = bin2hex(\random_bytes($length));
        } else if (function_exists('openssl_random_pseudo_bytes')) {
            $uuid = bin2hex(\openssl_random_pseudo_bytes($length));
        } else {
            $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $uuid = substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
        }
        return $uuid;
    }
}
if (!function_exists('filterEmoji')) {
    function filterEmoji($str)
    {
        $str = preg_replace_callback(
            '/./u',
            function (array $match) {
                return strlen($match[0]) >= 4 ? '' : $match[0];
            },
            $str);
        $cleaned = strip_tags($str);
        return htmlspecialchars(($cleaned));
    }


}

if (!function_exists('convertUnderline')) {


    function convertUnderline($str)
    {
        $str = preg_replace_callback('/([-_]+([a-z]{1}))/i', function ($matches) {
            return strtoupper($matches[2]);
        }, $str);
        return $str;
    }
}
if (!function_exists('humpToLine')) {

    /*
        * 驼峰转下划线
        */
    function humpToLine($str)
    {
        $str = preg_replace_callback('/([A-Z]{1})/', function ($matches) {
            return '_' . strtolower($matches[0]);
        }, $str);
        return $str;
    }
}
if (!function_exists('convertHump')) {

    function convertHump(array $data)
    {
        $result = [];
        foreach ($data as $key => $item) {
            if (is_array($item) || is_object($item)) {
                $result[humpToLine($key)] = convertHump((array)$item);
            } else {
                $result[humpToLine($key)] = $item;
            }
        }
        return $result;
    }
}

if (!function_exists('session')) {
    /**
     * Session管理
     * @param string $name session名称
     * @param mixed $value session值
     * @param bool $sessionId
     * @return mixed
     */
    function session($name = '', $value = '', $sessionId = '')
    {
        if ($sessionId != '') {
            $session = new Session(ApplicationContext::getContainer()->get(config('session.handler')), (string)$sessionId);
            $session->set($name, $value);
            Context::set(SessionInterface::class, $session);
            return true;
        }

        /** @var SessionInterface $session */
        $session = Context::get(SessionInterface::class);
        if (empty($session)) {
            return null;
        }
        if (is_null($name)) {
            // 清除
            $session->clear();
        } elseif ('' === $name) {
            return $session->all();
        } elseif (is_null($value)) {
            // 删除
            $session->remove($name);
        } elseif ('' === $value) {
            // 判断或获取
            return 0 === strpos($name, '?') ? $session->has(substr($name, 1)) : $session->get($name);
        } else {
            // 设置
            $session->set($name, $value);
        }
    }
}

if (!function_exists('getSession')) {
    /**
     * Session管理
     * @return mixed
     */
    function getSession(): ?SessionInterface
    {
        /** @var SessionInterface $session */
        $session = Context::get(SessionInterface::class);
        if (empty($session)) {
            return null;
        }
        return $session;
    }
}

if (!function_exists('sessionDestroy')) {
    /**
     * Session管理
     * @param string $name session名称
     * @param mixed $value session值
     * @param bool $sessionId
     * @return mixed
     */
    function sessionDestroy($sessionId = '')
    {
        if ($sessionId != '') {
            $session = new Session(ApplicationContext::getContainer()->get(config('session.handler')), (string)$sessionId);
            $session->clear();
            return true;
        }
        /** @var SessionInterface $session */
        $session = Context::get(SessionInterface::class);
        $session->clear();
    }
}
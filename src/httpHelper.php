<?php
declare(strict_types=1);

namespace Mzh\Helper;

use Hyperf\Utils\Context;
use Psr\Http\Message\ServerRequestInterface;

class httpHelper
{
    public static function getClientIp()
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

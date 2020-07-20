<?php

namespace Mzh\Helper\QcloudSms;

use GuzzleHttp\Client;
use Hyperf\Guzzle\ClientFactory;
use Hyperf\Guzzle\HandlerStackFactory;
use Hyperf\Logger\LoggerFactory;
use Psr\Log\LoggerInterface as LoggerInterfaceAlias;

/**
 * 发送Util类
 *
 */
class SmsSenderUtil
{
    /**
     * @var LoggerInterfaceAlias
     */
    protected $logger;

    /**
     * @var \Hyperf\Guzzle\ClientFactory
     */
    private $clientFactory;


    public function __construct(ClientFactory $clientFactory, LoggerFactory $loggerFactory)
    {
        $this->clientFactory = $clientFactory;
        $this->logger = $loggerFactory->get('log', 'default');
    }

    const BASE_API = "https://yun.tim.qq.com";

    private static $http_client;

    /**
     * 生成随机数
     *
     * @return int 随机数结果
     */
    public function getRandom()
    {
        return rand(100000, 999999);
    }

    /**
     * 生成签名
     *
     * @param string $appkey sdkappid对应的appkey
     * @param string $random 随机正整数
     * @param string $curTime 当前时间
     * @param array $phoneNumbers 手机号码
     * @return string  签名结果
     */
    public function calculateSig($appkey, $random, $curTime, $phoneNumbers)
    {
        $phoneNumbersString = $phoneNumbers[0];
        for ($i = 1; $i < count($phoneNumbers); $i++) {
            $phoneNumbersString .= ("," . $phoneNumbers[$i]);
        }

        return hash("sha256", "appkey=" . $appkey . "&random=" . $random
            . "&time=" . $curTime . "&mobile=" . $phoneNumbersString);
    }

    /**
     * 生成签名
     *
     * @param string $appkey sdkappid对应的appkey
     * @param string $random 随机正整数
     * @param string $curTime 当前时间
     * @param array $phoneNumbers 手机号码
     * @return string  签名结果
     */
    public function calculateSigForTemplAndPhoneNumbers(
        $appkey,
        $random,
        $curTime,
        $phoneNumbers
    )
    {
        $phoneNumbersString = $phoneNumbers[0];
        for ($i = 1; $i < count($phoneNumbers); $i++) {
            $phoneNumbersString .= ("," . $phoneNumbers[$i]);
        }

        return hash("sha256", "appkey=" . $appkey . "&random=" . $random
            . "&time=" . $curTime . "&mobile=" . $phoneNumbersString);
    }

    public function phoneNumbersToArray($nationCode, $phoneNumbers)
    {
        $i = 0;
        $tel = array();
        do {
            $telElement = new \stdClass();
            $telElement->nationcode = $nationCode;
            $telElement->mobile = $phoneNumbers[$i];
            array_push($tel, $telElement);
        } while (++$i < count($phoneNumbers));

        return $tel;
    }

    /**
     * 生成签名
     *
     * @param string $appkey sdkappid对应的appkey
     * @param string $random 随机正整数
     * @param string $curTime 当前时间
     * @param string $phoneNumber 手机号码
     * @return string  签名结果
     */
    public function calculateSigForTempl($appkey, $random, $curTime, $phoneNumber)
    {
        $phoneNumbers = array($phoneNumber);

        return $this->calculateSigForTemplAndPhoneNumbers(
            $appkey,
            $random,
            $curTime,
            $phoneNumbers
        );
    }

    /**
     * 生成签名
     *
     * @param string $appkey sdkappid对应的appkey
     * @param string $random 随机正整数
     * @param string $curTime 当前时间
     * @return string 签名结果
     */
    public function calculateSigForPuller($appkey, $random, $curTime)
    {
        return hash("sha256", "appkey=" . $appkey . "&random=" . $random
            . "&time=" . $curTime);
    }

    /**
     * 生成上传文件授权
     *
     * @param string $appkey sdkappid对应的appkey
     * @param string $random 随机正整数
     * @param string $curTime 当前时间
     * @param array $fileSha1Sum 文件sha1sum
     * @return string  授权结果
     */
    public function calculateAuth($appkey, $random, $curTime, $fileSha1Sum)
    {
        return hash("sha256", "appkey=" . $appkey . "&random=" . $random
            . "&time=" . $curTime . "&content-sha1=" . $fileSha1Sum);
    }

    /**
     * 生成sha1sum
     *
     * @param string $content 内容
     * @return string  内容sha1散列值
     */
    public function sha1sum($content)
    {
        return hash("sha1", $content);
    }

    /**
     * 发送请求
     *
     * @param string $url 请求地址
     * @param array $dataObj 请求内容
     * @return string 应答json字符串
     */
    public function sendCurlPost($url, $dataObj)
    {
        //$response = self::getClient()->post($url, ['json' => $dataObj]);
        //p($response->getBody()->getContents());
        // $body = $response->getBody()->getContents();//
        //
        $body = '{"result":0,"errmsg":"OK","ext":"","sid":"2028:f825cdb73569ea124100","fee":1}';

        $result = json_decode($body, true);
        if (isset($result['result']) && $result['result'] == 0) {
            return true;
        }
        throw new \Exception($result['errmsg'], $result['result']);
    }


    /**
     * @return Client
     */
    public static function getClient()
    {
        if (isset(self::$http_client) && self::$http_client instanceof Client) {
            return self::$http_client;
        }
        $factory = new HandlerStackFactory();
        $stack = $factory->create();
        self::$http_client = make(Client::class, [
            'config' => [
                'handler' => $stack,
                'base_uri' => self::BASE_API,
                'use_pool' => true,
                'headers' => [
                    "user-agent" => 'Mozilla/5.0 (iPhone; CPU iPhone OS 11_0 like Mac OS X) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Mobile/15A372 Safari/604.1',
                    'Upgrade-Insecure-Requests' => '1'
                ]
            ],
        ]);
        return self::$http_client;
    }
}

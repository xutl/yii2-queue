<?php

namespace xutl\queue\qcloud;

use yii\queue\cli\Signal;
use yii\base\NotSupportedException;
use yii\base\InvalidConfigException;

/**
 * Class Queue
 * @package xutl\queue
 */
class Queue extends \yii\queue\cli\Queue
{
    /**
     * @var string 服务主机名
     */
    public $serverHost;

    /**
     * 区域参数
     * @var string
     */
    public $region;

    /**
     * @var string
     */
    public $secretId;

    /**
     * @var string
     */
    public $secretKey;

    /**
     * @var string queue name
     */
    public $queueName;

    /**
     * @var bool 是否使用安全连接
     */
    public $secureConnection = true;

    /**
     * @var string command class name
     */
    public $commandClass = Command::class;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if (empty ($this->serverHost)) {
            throw new InvalidConfigException ('The "serverHost" property must be set.');
        }
        if (empty ($this->secretId)) {
            throw new InvalidConfigException ('The "secretId" property must be set.');
        }
        if (empty ($this->secretKey)) {
            throw new InvalidConfigException ('The "secretKey" property must be set.');
        }
        if (empty ($this->queueName)) {
            throw new InvalidConfigException ('The "queueName" property must be set.');
        }
    }

    /**
     * Runs all jobs from queue.
     */
    public function run()
    {
        try {
            while ($payload = $this->getQueue()->receiveMessage()) {
                if ($payload->isSucceed()) {
                    $receiptHandle = $payload->getReceiptHandle();
                    if ($this->handleMessage(
                        $payload->getMessageId(),
                        $payload->getMessageBody(),
                        $payload->getNextVisibleTime(),
                        $payload->getDequeueCount()
                    )) {
                        $this->getQueue()->deleteMessage($receiptHandle);
                    }
                }
            }
        } catch (MnsException $e) {
        }
    }

    /**
     * Listens queue and runs new jobs.
     */
    public function listen()
    {
        while (!Signal::isExit()) {
            try {
                if ($payload = $this->getQueue()->receiveMessage(3)) {
                    if ($payload->isSucceed()) {
                        $receiptHandle = $payload->getReceiptHandle();
                        if ($this->handleMessage(
                            $payload->getMessageId(),
                            $payload->getMessageBody(),
                            $payload->getNextVisibleTime(),
                            $payload->getDequeueCount()
                        )) {
                            $this->getQueue()->deleteMessage($receiptHandle);
                        }
                    }
                }
            } catch (MnsException $e) {
            }
        }
    }

    /**
     * @inheritdoc
     */
    protected function pushMessage($message, $ttr, $delay, $priority)
    {
        $payload = new SendMessageRequest($message, $delay, $priority, false);
        return $this->getQueue()->sendMessage($payload);
    }

    /**
     * @inheritdoc
     */
    public function status($id)
    {
        throw new NotSupportedException('Status is not supported in the driver.');
    }

    private $_qcloud;

    /**
     * 获取队列
     * @return \AliyunMNS\Queue
     */
    public function getQueue()
    {
        if (!$this->_qcloud) {
            $client = new HttpClient($this->endPoint, $this->accessId, $this->accessKey, $this->securityToken);
            $this->_qcloud = new \AliyunMNS\Queue($client, $this->queueName, false);
        }
        return $this->_qcloud;
    }
}

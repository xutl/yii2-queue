<?php

namespace xutl\queue\aliyun;

use yii\queue\cli\SignalLoop;
use yii\base\NotSupportedException;
use yii\base\InvalidConfigException;
use AliyunMNS\Http\HttpClient;
use AliyunMNS\Requests\SendMessageRequest;
use AliyunMNS\Exception\MnsException;

/**
 * Class Queue
 * @package xutl\queue
 */
class Queue extends \yii\queue\cli\Queue
{
    /**
     * @var  string
     */
    public $endPoint;

    /**
     * @var string
     */
    public $accessId;

    /**
     * @var string
     */
    public $accessKey;

    /**
     * @var string queue name
     */
    public $queue;

    /**
     * @var null|string
     */
    public $securityToken = null;

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
        if (empty ($this->endPoint)) {
            throw new InvalidConfigException ('The "endPoint" property must be set.');
        }
        if (empty ($this->accessId)) {
            throw new InvalidConfigException ('The "accessId" property must be set.');
        }
        if (empty ($this->accessKey)) {
            throw new InvalidConfigException ('The "accessKey" property must be set.');
        }
        if (empty ($this->queue)) {
            throw new InvalidConfigException ('The "queue" property must be set.');
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
        while (!SignalLoop::isExit()) {
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

    /**
     * @var \AliyunMNS\Queue
     */
    private $_aliyun;

    /**
     * 获取队列
     * @return \AliyunMNS\Queue
     */
    public function getQueue()
    {
        if (!$this->_aliyun) {
            $client = new HttpClient($this->endPoint, $this->accessId, $this->accessKey, $this->securityToken);
            $this->_aliyun = new \AliyunMNS\Queue($client, $this->queue, false);
        }
        return $this->_aliyun;
    }
}

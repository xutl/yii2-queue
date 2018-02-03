<?php

namespace xutl\queue\qcloud;

use xutl\qcloud\Client;
use yii\queue\cli\SignalLoop;
use yii\base\NotSupportedException;
use yii\base\InvalidConfigException;

/**
 * Class Queue
 * @package xutl\queue
 */
class Queue extends \yii\queue\cli\Queue
{

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
    public $queue;

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
        if (empty ($this->region)) {
            throw new InvalidConfigException ('The "region" property must be set.');
        }
        if (empty ($this->secretId)) {
            throw new InvalidConfigException ('The "secretId" property must be set.');
        }
        if (empty ($this->accessId)) {
            throw new InvalidConfigException ('The "accessId" property must be set.');
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

    /**
     * @var Client
     */
    private $_qcloud;

    /**
     * 获取队列
     * @return Client
     */
    public function getQueue()
    {
        if (!$this->_qcloud) {
            $client = new HttpClient($this->endPoint, $this->secretId, $this->secretKey);
            $this->_qcloud = new \xutl\cmq\Queue($client, $this->queue, false);
        }
        return $this->_qcloud;
    }
}

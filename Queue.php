<?php
namespace xutl\queue;

use yii\base\InvalidParamException;
use yii\queue\cli\Queue as CliQueue;
use yii\queue\cli\Signal;
use AliyunMNS\Config;
use AliyunMNS\Http\HttpClient;
use AliyunMNS\Queue as QueueBackend;
use AliyunMNS\Requests\SendMessageRequest;
use AliyunMNS\Exception\MnsException;
use AliyunMNS\Model\SendMessageRequestItem;
use AliyunMNS\Requests\BatchSendMessageRequest;

class Queue extends CliQueue 
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
     * @var null|string
     */
    public $securityToken = null;

    /**
     * @var null|Config
     */
    public $config = null;

    /**
     * @var HttpClient
     */
    private $client;

    /**
     * @var bool 是否开启Base 64
     */
    public $base64 = true;
  
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
        $this->client = new HttpClient($this->endPoint, $this->accessId, $this->accessKey, $this->securityToken, $this->config);
    }
  
  /**
     * 获取队列
     * @param string $queueName
     * @return Queue
     */
    public function getQueueRef($queueName)
    {
        return new QueueBackend([
            'client' => $this->client,
            'queueName' => $queueName,
            'base64' => $this->base64
        ]);
    }
}

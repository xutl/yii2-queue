<?php

namespace xutl\queue\qcloud;

use yii\queue\cli\Command as CliCommand;

/**
 * Class Command
 * @package xutl\queue
 */
class Command extends CliCommand
{
    /**
     * @var Queue
     */
    public $queue;

    /**
     * Runs all jobs from beanstalk-queue.
     * It can be used as cron job.
     */
    public function actionRun()
    {
        $this->queue->run();
    }

    /**
     * Listens beanstalk-queue and runs new jobs.
     * It can be used as demon process.
     */
    public function actionListen()
    {
        $this->queue->listen();
    }

    /**
     * @param string $actionID
     * @return bool
     * @since 2.0.2
     */
    protected function isWorkerAction($actionID)
    {
        return in_array($actionID, ['run' ,'listen'], true);
    }
}

<?php

namespace xutl\queue;

use yii\queue\cli\Command as CliCommand;

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
}

<?php

namespace humhub\modules\profiler\controllers;

use humhub\modules\content\models\Content;
use humhub\modules\space\models\Space;
use humhub\modules\stream\models\ContentContainerStreamQuery;
use humhub\modules\user\models\User;
use SebastianBergmann\Timer\Timer;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;

class StreamController extends Controller
{
    public $count = 1000;

    public $title;

    public function options($actionID)
    {
        return array_merge(parent::options($actionID), [
            'count',
            'title',
        ]);
    }

    public function actionRun()
    {
        Yii::$app->user->switchIdentity(User::findOne(['id' => 1]));

        $space = Space::findOne(['id' => 1]);


        $contentCount = Content::find()->where(['contentcontainer_id' => $space->contentcontainer_id])->count();

        $timer = new Timer();
        $timer->start();

        for ($i = 0; $i < $this->count;$i++) {
            //$gallery->fileListQuery()->one();
            $query = new ContentContainerStreamQuery(['container' => $space]);
            $query->limit(20);
            $result = $query->all();
            // $this->stdout($query->createCommand()->rawSql,  Console::FG_GREEN);
        }

        $timeTotal = $timer->stop()->asSeconds();

        $timeAvg = $timeTotal / $this->count;

        if ($this->title) {
            $this->stdout("---------------------------------------------------------------\n", Console::FG_GREEN);
            $this->stdout($this->title . "\n", Console::FG_GREEN);
            $this->stdout("---------------------------------------------------------------\n", Console::FG_GREEN);
        }

        $this->printResultRow('Content count of space', $contentCount, false);
        $this->printResultRow('Result count per run', count($result), false);
        $this->printResultRow('ContentContainerStreamQuery(' . $this->count . ')', $timeTotal);
        $this->printResultRow('ContentContainerStreamQuery(avg)', $timeAvg);

        $this->stdout("\n");
        //$this->printResourceUsage();

        $this->cli_beep();
    }
}

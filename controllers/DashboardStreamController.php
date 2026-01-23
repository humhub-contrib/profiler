<?php

namespace humhub\modules\profiler\controllers;

use humhub\modules\content\models\Content;
use humhub\modules\dashboard\stream\DashboardStreamQuery;
use humhub\modules\dashboard\stream\DeprecatedDashboardStreamQuery;
use humhub\modules\profiler\models\ProfilerResult;

class DashboardStreamController extends ProfileController
{
    public $count = 1000;

    public $activity = 0;

    public function options($actionID)
    {
        return array_merge(parent::options($actionID), [
            'activity',
        ]);
    }

    public function actionRunGuest()
    {
        $result = $this->runProfiler(function (ProfilerResult $result): void {
            $query = new DashboardStreamQuery(['activity' => (bool) $this->activity]);
            $result->setResult($query->all());
        }, $this->title);

        $competing = $this->runProfiler(function (ProfilerResult $result): void {
            $query = new DeprecatedDashboardStreamQuery(['activity' => (bool) $this->activity]);
            $result->setResult($query->all());
        }, $this->title);

        $result->setTotalCount('Content entries', Content::find()->count());

        $this->printResult($result, $competing);
    }

    public function actionRunMember()
    {
        $user = $this->getUser();

        $result = $this->runProfiler(function (ProfilerResult $result) use ($user): void {
            $query = new DashboardStreamQuery(['activity' => (bool) $this->activity, 'user' => $user]);
            $result->setResult($query->all());
        }, $this->title);

        $competing = $this->runProfiler(function (ProfilerResult $result) use ($user): void {
            $query = new DeprecatedDashboardStreamQuery(['activity' => (bool) $this->activity, 'user' => $user]);
            $result->setResult($query->all());
        }, $this->title);



        $result->setTotalCount('Content entries', Content::find()->count());

        $this->printResult($result, $competing);
    }

    public function actionRunMemberDeprecated()
    {
        $user = $this->getUser();

        /* $result = $this->runProfiler(function(ProfilerResult $result) use ($user) {
             $query = new DashboardStreamQuery(['activity' => (boolean) $this->activity, 'user' => $user]);
             $result->setResult($query->all());
         }, $this->title);*/

        $competing = $this->runProfiler(function (ProfilerResult $result) use ($user): void {
            $query = new DeprecatedDashboardStreamQuery(['activity' => (bool) $this->activity, 'user' => $user]);
            $result->setResult($query->all());
        }, $this->title);

        //  $result->setTotalCount('Content entries', Content::find()->count());

        $this->printResult($competing);
    }
}

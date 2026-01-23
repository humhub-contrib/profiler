<?php

namespace humhub\modules\profiler\controllers;

use humhub\modules\profiler\faker\ContentContainerProvider;
use humhub\modules\profiler\faker\SpaceMemberProvider;
use Yii;
use yii\faker\FixtureController as FakerFixtureController;
use yii\helpers\Console;

class FixtureController extends FakerFixtureController
{
    /**
     * @inheritDoc
     */
    public $templatePath = '@profiler/fixtures/templates';

    /**
     * @inheritDoc
     */
    public $fixtureDataPath = '@profiler/fixtures/data';

    /**
     * @inheritDoc
     */
    public $language = 'en-US';

    /**
     * @inheritDoc
     */
    public $namespace = 'humhub\modules\profiler\fixtures';

    /**
     * @inheritDoc
     */
    public $count = 2000;

    public static $templateCount = [];

    public $defaultCount;

    public $providers = [
        ContentContainerProvider::class,
        SpaceMemberProvider::class,
    ];

    public function init()
    {
        parent::init();

        $this->defaultCount = $this->count;
        $generator = $this->getGenerator();
        $generator->addProvider(new ContentContainerProvider($generator));
        $generator->addProvider(new SpaceMemberProvider($generator));

        static::$templateCount =  [
            'content/content' => 80000, // 40.000 post, 40.000 activities
            'post/post' => 40000,
            'activity/activity' => 40000,
            'file/file' => 100,
            'content/contentcontainer' => fn() => count(ContentContainerProvider::$container),
            'user/group' => 1,
        ];
    }

    /**
     * Generates all fixtures template path that can be found.
     */
    public function actionGenerateAll()
    {
        parent::actionGenerateAll();

        if (!empty(ContentContainerProvider::$container)) {
            $this->count = count(ContentContainerProvider::$container);

            $templatePath = Yii::getAlias($this->templatePath);
            $fixtureDataPath = Yii::getAlias($this->fixtureDataPath);

            $this->generateFixtureFile('content/contentcontainer', $templatePath, $fixtureDataPath);
        }
    }

    public function generateFixtureFile($templateName, $templatePath, $fixtureDataPath)
    {
        if (isset(static::$templateCount[$templateName])) {
            $this->count = (is_callable(static::$templateCount[$templateName]))
                ? call_user_func(static::$templateCount[$templateName]) : static::$templateCount[$templateName];
        } else {
            $this->count = $this->defaultCount;
        }

        $this->stdout($templateName . '(' . $this->count . ")\n", Console::FG_GREEN);
        parent::generateFixtureFile($templateName, $templatePath, $fixtureDataPath);
    }

    public $excludeTemplates = ['content/contentcontainer'];

    protected function findTemplatesFiles(array $templatesNames = [])
    {
        $result = parent::findTemplatesFiles($templatesNames);
        $result = array_diff($result, $this->excludeTemplates);
        return $result;
    }

}

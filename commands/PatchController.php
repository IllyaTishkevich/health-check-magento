<?php

namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use yii\httpclient\Client;
use app\models\Message;
use app\models\Level;
use app\models\Project;
use app\models\ProjectSearch;
use app\models\MessageSearch;
use yii\httpclient\Exception;
use app\models\Sites;

/**
 * Patches Comands List
 *
 * Class CheckController
 * @package app\commands
 */
class PatchController extends Controller
{
    /**
     * Create JsLog Tables
     *
     * @throws \yii\base\InvalidConfigException
     */
    public function actionJsTables($id = null)
    {
        if ($id) {
            $project = Project::findOne(['id' => $id]);
            if (!isset($project->prefix)) {
                $project->prefix = $this->generateKey(8);
                $project->save();
            }
            $jsTablesGenerator = new \app\models\JSDatabase\JsLogTable($project->prefix);
            $jsTablesGenerator->safeUp();
        }
    }

    protected function generateKey($length = 16) {
        $max = ceil($length / 40);
        $random = '';
        for ($i = 0; $i < $max; $i ++) {
            $random .= sha1(microtime(true).mt_rand(10000,90000));
        }
        return substr($random, 0, $length);
    }
}
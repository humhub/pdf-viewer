<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\pdfViewer\controllers;

use humhub\components\Controller;
use humhub\modules\file\models\File;
use humhub\modules\pdfViewer\helpers\PdfHelper;
use Yii;
use yii\web\BadRequestHttpException;

class ViewController extends Controller
{
    public function actionIndex()
    {
        return $this->renderAjax('index', ['file' => $this->getFile()]);
    }

    public function actionOpen()
    {
        if ($this->getFile()->getUrl() !== Yii::$app->request->get('file')) {
            throw new BadRequestHttpException('Denied request with a wrong file guid!');
        }

        return $this->renderPartial('viewer');
    }

    private function getFile(): ?File
    {
        $guid = Yii::$app->request->get('guid', Yii::$app->request->post('guid'));

        if (empty($guid)) {
            throw new BadRequestHttpException('Wrong request without file guid!');
        }

        $file = File::findOne(['guid' => $guid]);

        if (!PdfHelper::isPdfFile($file)) {
            throw new BadRequestHttpException(Yii::t('PdfViewerModule.base', 'Could not view the requested file!'));
        }

        return $file;
    }
}

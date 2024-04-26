<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\pdfViewer;

use Yii;
use yii\web\ServerErrorHttpException;

class Module extends \humhub\components\Module
{
    private string $viewerSourcePath = '@pdf-viewer/vendor/clean-composer-packages/pdf-js/web/viewer.html';
    private string $viewerTemplatePath = '@pdf-viewer/views/view/viewer.php';

    /**
     * Initialize a viewer template file from source HTML
     *
     * @return void
     * @throws ServerErrorHttpException
     */
    public function initPdfViewer(): void
    {
        $sourcePath = Yii::getAlias($this->viewerSourcePath);

        if (!is_readable($sourcePath)) {
            throw new ServerErrorHttpException('File "' . $sourcePath . '" is not readable!');
        }

        $viewerHash = $this->settings->get('viewerHash');
        $sourceHash = sha1_file($sourcePath);
        $templatePath = Yii::getAlias($this->viewerTemplatePath);

        if ($viewerHash === $sourceHash && file_exists($templatePath)) {
            // No need to update a same version of the viewer.html
            return;
        }

        $sourceCode = file_get_contents($sourcePath);
        $sourceCode = preg_replace('/(<script.+?src="|<link.+?href=")/', '$1<?= \$assetsUrl ?>', $sourceCode);

        if (!file_put_contents($templatePath, $sourceCode)) {
            throw new ServerErrorHttpException('File "' . $templatePath . '" is not writable!');
        }

        $this->settings->set('viewerHash', $sourceHash);
    }
}

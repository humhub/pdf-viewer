<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\pdfViewer\assets;

use humhub\components\assets\AssetBundle;
use humhub\modules\file\models\File;

class PdfJsAssets extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@pdf-viewer/vendor/clean-composer-packages/pdf-js';

    public function getIframeUrl(File $file): string
    {
        return $this->baseUrl . '/web/viewer.html?file=' . urlencode($file->getUrl());
    }
}

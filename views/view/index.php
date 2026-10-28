<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\helpers\Html;
use humhub\modules\file\models\File;
use humhub\modules\pdfViewer\assets\PdfViewerAssets;
use humhub\modules\pdfViewer\helpers\Url;
use humhub\widgets\modal\Modal;
use yii\web\View;

/* @var File $file */
/* @var View $this */

PdfViewerAssets::register($this);
?>
<?php Modal::beginDialog([
    'title' => $file->file_name,
    'size' => Modal::SIZE_FULL_SCREEN,
]) ?>
    <iframe id="pdf-js-viewer" src="<?= Html::encode(Url::toViewer($file)) ?>" title="<?= Html::encode(Yii::t('PdfViewerModule.base', 'PDF viewer')) ?>" frameborder="0"></iframe>
<?php Modal::endDialog() ?>

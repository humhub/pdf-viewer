<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\modules\file\models\File;
use humhub\modules\pdfViewer\assets\PdfViewerAssets;
use humhub\modules\pdfViewer\helpers\Url;
use humhub\widgets\ModalDialog;
use yii\web\View;

/* @var File $file */
/* @var View $this */

PdfViewerAssets::register($this);
?>
<?php ModalDialog::begin([
    'header' => $file->file_name,
    'options' => ['id' => 'pdf-viewer-modal-dialog'],
]) ?>
    <div class="modal-body">
        <iframe id="pdf-js-viewer" src="<?= Url::toViewer($file) ?>" title="webviewer" frameborder="0"></iframe>
    </div>
<?php ModalDialog::end() ?>

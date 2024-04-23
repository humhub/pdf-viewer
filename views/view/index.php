<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\modules\file\models\File;
use humhub\widgets\ModalButton;
use humhub\widgets\ModalDialog;

/* @var File $file */
?>
<?php ModalDialog::begin(['options' => ['style' => 'width:95%']]) ?>

    <div class="modal-body">
        <strong><?= $file->file_name ?></strong>: PDF viewer is in progress...
    </div>

    <div class="modal-footer">
        <hr>
        <?= ModalButton::cancel(Yii::t('base', 'Close'))->right() ?>
    </div>

<?php ModalDialog::end() ?>

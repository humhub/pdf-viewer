<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\pdfViewer\filehandler;

use humhub\modules\file\handler\BaseFileHandler;
use Yii;
use yii\helpers\Url;

class ViewFileHandler extends BaseFileHandler
{
    /**
     * @inheritdoc
     */
    public function getLinkAttributes()
    {
        return [
            'label' => Yii::t('PdfViewerModule.base', 'View PDF'),
            'data-action-url' => Url::to(['/pdf-viewer/view', 'guid' => $this->file->guid]),
            'data-action-click' => 'ui.modal.load',
            'data-modal-id' => 'pdf-viewer-modal',
            'data-modal-close' => '',
        ];
    }
}

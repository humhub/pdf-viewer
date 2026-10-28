<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace pdfViewer;

use humhub\modules\file\models\File;
use humhub\modules\pdfViewer\helpers\PdfHelper;
use humhub\modules\pdfViewer\helpers\Url;
use tests\codeception\_support\HumHubDbTestCase;

class PdfHelperTest extends HumHubDbTestCase
{
    public function testIsPdfFile()
    {
        $this->assertFalse(PdfHelper::isPdfFile(null));
        $this->assertFalse(PdfHelper::isPdfFile(new File(['mime_type' => 'text/plain'])));
        $this->assertTrue(PdfHelper::isPdfFile(new File(['mime_type' => 'application/pdf'])));
    }

    public function testCanViewRequiresStoredContent()
    {
        $this->becomeUser('Admin');

        $file = new File(['file_name' => 'test.pdf', 'mime_type' => 'application/pdf', 'size' => 1]);
        $this->assertTrue($file->save());

        $this->assertFalse(PdfHelper::canView($file), 'File without stored content cannot be viewed');

        $file->setStoredFileContent('%PDF-1.4');
        $this->assertTrue(PdfHelper::canView($file));

        $textFile = new File(['file_name' => 'test.txt', 'mime_type' => 'text/plain', 'size' => 1]);
        $this->assertTrue($textFile->save());
        $textFile->setStoredFileContent('text');
        $this->assertFalse(PdfHelper::canView($textFile), 'Only PDF files can be viewed');

        $file->delete();
        $textFile->delete();
    }

    public function testUrls()
    {
        $file = new File(['guid' => 'test-guid', 'file_name' => 'test.pdf', 'mime_type' => 'application/pdf']);

        $this->assertStringContainsString('pdf-viewer/view', urldecode(Url::toView($file)));
        $this->assertStringContainsString('guid=test-guid', Url::toView($file));

        $viewerUrl = urldecode(Url::toViewer($file));
        $this->assertStringContainsString('pdf-viewer/view/open', $viewerUrl);
        $this->assertStringContainsString('guid=test-guid', $viewerUrl);
        $this->assertStringContainsString('file=', $viewerUrl);
    }
}

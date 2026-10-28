<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace pdfViewer;

use humhub\modules\content\models\Content;
use humhub\modules\file\models\File;
use humhub\modules\pdfViewer\controllers\ViewController;
use humhub\modules\post\models\Post;
use humhub\modules\space\models\Space;
use tests\codeception\_support\HumHubDbTestCase;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class ViewControllerTest extends HumHubDbTestCase
{
    private ?Space $space = null;
    private ?Post $post = null;
    private ?File $file = null;

    protected function _before()
    {
        parent::_before();

        $this->becomeUser('Admin');

        // Private space of the default fixtures, members: Admin and User1
        $this->space = Space::findOne(['id' => 5]);
        $this->assertEquals(Space::VISIBILITY_NONE, $this->space->visibility);

        $this->post = new Post($this->space, Content::VISIBILITY_PRIVATE);
        $this->post->message = 'PDF';
        $this->assertTrue($this->post->save());

        $this->file = $this->createFile('private.pdf', 'application/pdf');
        $this->post->fileManager->attach($this->file);
    }

    protected function _after()
    {
        $this->file?->delete();
        $this->post?->hardDelete();

        parent::_after();
    }

    private function createFile(string $name, string $mimeType, bool $withContent = true): File
    {
        $file = new File(['file_name' => $name, 'mime_type' => $mimeType, 'size' => 1]);
        $this->assertTrue($file->save());
        if ($withContent) {
            $file->setStoredFileContent('%PDF-1.4');
        }

        return $file;
    }

    private function runAction(string $action, array $params)
    {
        Yii::$app->request->setQueryParams($params);
        Yii::$app->request->setBodyParams([]);
        Yii::$app->request->setUrl('/pdf-viewer/view?' . http_build_query($params));

        // The layout is not rendered here, because it depends on the theme assets of the test environment
        $controller = new class ('view', Yii::$app->getModule('pdf-viewer')) extends ViewController {
            public function renderAjax($view, $params = [])
            {
                return $view . ':' . $params['file']->file_name;
            }
        };
        $controller->enableCsrfValidation = false;

        $previous = Yii::$app->controller;
        Yii::$app->controller = $controller;
        try {
            return $controller->$action();
        } finally {
            Yii::$app->controller = $previous;
        }
    }

    public function testMemberCanView()
    {
        $result = $this->runAction('actionIndex', ['guid' => $this->file->guid]);

        $this->assertEquals('index:private.pdf', $result);
    }

    public function testStrangerAndGuestCannotView()
    {
        $this->becomeUser('User3');
        $this->assertFalse($this->space->isMember(Yii::$app->user->id));

        try {
            $this->runAction('actionIndex', ['guid' => $this->file->guid]);
            $this->fail('A user without access must not open the PDF viewer');
        } catch (ForbiddenHttpException $e) {
            $this->assertStringNotContainsString('private.pdf', $e->getMessage());
        }

        $this->expectException(ForbiddenHttpException::class);
        $this->runAction('actionOpen', [
            'guid' => $this->file->guid,
            'file' => $this->file->getUrl(),
        ]);
    }

    public function testGuestCannotView()
    {
        $this->logout();

        $this->expectException(ForbiddenHttpException::class);
        $this->runAction('actionIndex', ['guid' => $this->file->guid]);
    }

    public function testFileWithoutContent()
    {
        $file = $this->createFile('empty.pdf', 'application/pdf', false);
        $this->post->fileManager->attach($file);

        try {
            $this->expectException(NotFoundHttpException::class);
            $this->runAction('actionIndex', ['guid' => $file->guid]);
        } finally {
            $file->delete();
        }
    }

    public function testWrongRequests()
    {
        $text = $this->createFile('test.txt', 'text/plain');
        $this->post->fileManager->attach($text);

        try {
            foreach ([[], ['guid' => 'unknown'], ['guid' => $text->guid]] as $params) {
                try {
                    $this->runAction('actionIndex', $params);
                    $this->fail('Request must be denied: ' . json_encode($params));
                } catch (BadRequestHttpException $e) {
                    $this->assertInstanceOf(BadRequestHttpException::class, $e);
                }
            }

            $this->expectException(BadRequestHttpException::class);
            $this->runAction('actionOpen', ['guid' => $this->file->guid, 'file' => 'http://example.com/other.pdf']);
        } finally {
            $text->delete();
        }
    }

    public function testViewerIsRendered()
    {
        $source = Yii::getAlias('@pdf-viewer/vendor/clean-composer-packages/pdf-js/web/viewer.html');
        if (!is_readable($source)) {
            $this->markTestSkipped('PDF.js is not installed, run "composer install".');
        }

        $html = $this->runAction('actionOpen', [
            'guid' => $this->file->guid,
            'file' => $this->file->getUrl(),
        ]);

        $this->assertStringContainsString('pdf-viewer-iframe.css', $html);
        $this->assertStringContainsString('/web/viewer.mjs', $html);
    }
}

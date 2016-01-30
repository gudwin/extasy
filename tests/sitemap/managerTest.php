<?php
use \Faid\DB;
use \Extasy\tests\sitemap\TestDocument;

/**
 * Тестируем работу основного класса модуля Sitemap - Sitemap
 * В данном тесте тестируется добавление, удаление и редактирование страниц
 * @author My luv
 *
 */
class SitemapManagerTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();
        CDumper::importFile(dirname(__FILE__) . '/import.sql');
        include __DIR__ . '/import.php';

    }

    /**
     * Тестируем добавление страницы, предок которой не определен
     * @expectedException SitemapException
     */
    public function testAddPageWithUknownParent()
    {
        Sitemap::addPage('Test inserting', 'xx', TestDocument::ModelName, 1, -1);
    }

    /**
     * Тестирует добавление документа
     */
    public function testAddPage()
    {
        Sitemap::addPage('Test inserting', 'xx', TestDocument::ModelName, 1, 0);
    }

    /**
     * Тестирует добавление скрипты, путь к которому не существует
     * @expectedException SitemapException
     */
    public function testAddScriptWithUnreadableScript()
    {
        Sitemap::addScript('Test script', 'scripts/non_readable.php', '/non_readable', 0, '', '');
    }

    /**
     * Тестирует добавление скрипты, предок которого не существу
     * @expectedException SitemapException
     */
    public function testAddScriptWithUnknownParent()
    {
        Sitemap::addScript('Test script', 'scripts/non_readable.php', '/non_readable', -1, '', '');
    }

    /**
     * Тестирует добавление скрипта
     */
    public function testAddScript()
    {
        Sitemap::addScript('Test script', 'tests/sitemap/sitemap_module_test_script.php', '/sitemap_module_test_script',
            0, '', '');
    }

    /**
     * Тестируем двойного добавления скрипта (с одинаковыми url_key)
     * @expectedException SitemapException
     */
    public function testAddScriptTwice()
    {
        Sitemap::addScript('Test script', 'tests/sitemap/sitemap_module_test_script.php', '/sitemap_module_test_script',
            0, '', '');
        Sitemap::addScript('Test script', 'tests/sitemap/sitemap_module_test_script.php', '/sitemap_module_test_script',
            0, '', '');
    }

    /**
     * Тестируем двойного добавления скрипта (с разными url_key)
     */
    public function testAddScriptTwiceWithDifferentUrlKeys()
    {
        Sitemap::addScript('Test script', 'tests/sitemap/sitemap_module_test_script.php', '/sitemap_module_test_script',
            0, '', '');
        Sitemap::addScript('Test script', 'tests/sitemap/sitemap_module_test_script.php',
            '/sitemap_module_test_script2', 0, '', '');
    }

    /**
     * Тестируем удаление несуществующего (в реестре) документа
     * @expectedException SitemapException
     */
    public function testRemoveDocumentUnexistedDocument()
    {
        Sitemap::removeDocument('sitemap_unexisted_exception', 1);
    }

    /**
     * Тестируем удаление несуществующего (в карте сайта) документа
     * @expectedException SitemapException
     */
    public function testRemoveDocumentUnexistedDocumentInSitemap()
    {
        Sitemap::removeDocument(TestDocument::ModelName, 1);
    }

    /**
     * Тестируем удаление
     */
    public function testRemoveDocument()
    {

        Sitemap::addPage('Test inserting', 'xx', TestDocument::ModelName, 1, 0);
        Sitemap::removeDocument(TestDocument::ModelName, 1);
    }

    /**
     * Тестируем удаление из Sitemap + удаление из таблицы бд
     */
    public function testFullRemoveDocument()
    {
        $document = new TestDocument();
        $document->name = 'Test inserting';
        $document->insert();
        Sitemap::addPage('Test inserting', 'xx', TestDocument::ModelName, 1, 0);
        //
        Sitemap::removeDocument(TestDocument::ModelName, 1);
        // Проверяем удаление из бд
        $result = DB::get(sprintf('SELECT COUNT(*) as `count` FROM `%s`', TestDocument::TableName));
        $this->assertEquals($result['count'], 0);
    }

    /**
     * Тестируем каскадное удаление
     */
    public function testRecursiveRemoving()
    {
        $document = new TestDocument();
        $document->name = 'Test inserting';
        $document->insert();
        $nRoot = Sitemap::addPage('Test inserting', 'xx', TestDocument::ModelName, 1, 0);
        $document = new TestDocument();
        $document->name = 'Test inserting2';
        $document->insert();
        Sitemap::addPage('Test inserting2', 'xx', TestDocument::ModelName, 2, $nRoot);
        //
        Sitemap::removeDocument(TestDocument::ModelName, '1');
        // Проверяем удаление из бд
        $result = DB::get(sprintf('SELECT COUNT(*) as `count` FROM `%s`', TestDocument::TableName));
        $this->assertEquals($result['count'], 0);
    }

    /**
     * Попытка удаления несуществующего скрипта
     * @expectedException SitemapException
     */
    public function testRemoveUknownScript()
    {
        Sitemap::removeScript('xxxx.php', '/');
    }

    /**
     * Тестирует удаление скрипта
     */
    public function testRemoveScript()
    {
        Sitemap::removeScript('scripts/index.php', '');
    }

    /**
     * Тестируем обновление несуществующего документа
     * @expectedException SitemapException
     */
    public function testUpdateUknownDocument()
    {
        Sitemap::updatePage('xx', 1, 'Uknown document', '/xx', 0);
    }

    /**
     * Тестируем обновление документа, который замещает url другого документа
     * @expectedException SitemapException
     */
    public function testUpdateDocumentWithDuplicateUrl()
    {
        Sitemap::addPage('Test inserting', 'xx', TestDocument::ModelName, 1, 0);
        Sitemap::addPage('Test inserting2', 'xx2', TestDocument::ModelName, 2, 0);
        Sitemap::updatePage(TestDocument::ModelName, 2, 'Test updating', 'xx', 0);
    }

    /**
     * Тестируем обновление документа, который не обозначен в карте сайта
     * @expectedException SitemapException
     */
    public function testUpdateDocumentThatNotDefinedInSitemap()
    {
        Sitemap::updatePage(TestDocument::ModelName, 2, 'Test updating', 'xx', 0);
    }

    /**
     * Тестируем обновление документа
     */
    public function testUpdateDocument()
    {
        $nId = Sitemap::addPage('Test inserting', 'xx', TestDocument::ModelName, 1, 0);
        Sitemap::updatePage(TestDocument::ModelName, 1, 'Test updating', 'xxx', 0);
        $urlInfo = Sitemap_Sample::get($nId);

        $this->assertEquals($urlInfo['full_url'], '///xxx/');
        $this->assertEquals($urlInfo['name'], 'Test updating');
    }

    /**
     * Тестирует сохранение страницы (вставку) с отстуствующими индексами в массиве
     * @expectedException SitemapException
     */
    public function testSavePageWithIncorrectIndexes()
    {
        $data = array();
        Sitemap::savePage($data);
    }

    /**
     * Тестирует вставку несуществующего документа
     * @expectedException SitemapException
     */
    public function testSavePageWithUknownDocument()
    {
        $data = array(
            'document_name' => 'unknown_document',
            'document_id' => 1,
            'name' => 'test update',
            'url_key' => 'test',
        );
        Sitemap::savePage($data);
    }

    /**
     * Тестируем обновление документа, который не найден
     * @expectedException SitemapException
     */
    public function testSavePageWithUknownDocumentPage()
    {
        $data = array(
            'document_name' => TestDocument::ModelName,
            'document_id' => 1,
            'name' => 'test update',
            'url_key' => 'test',
        );
        Sitemap::savePage($data);
    }

    /**
     * Тестирует вставку документа
     */
    public function testSavePage()
    {
        $nId = Sitemap::addPage('Test inserting', 'xx', TestDocument::ModelName, 1, 0);
        $data = array(
            'document_name' => TestDocument::ModelName,
            'document_id' => 1,
            'name' => 'test update',
            'url_key' => 'test',
        );
        Sitemap::savePage($data);
        // Получаем запись в бд
        $urlInfo = Sitemap_Sample::get($nId);
        $this->AssertEquals($urlInfo['name'], $data['name']);
        $this->AssertEquals($urlInfo['full_url'], '///' . $data['url_key'] . '/');
    }

}
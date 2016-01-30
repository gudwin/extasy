<?

use \Faid\Configure\Configure;
use \DAO_FileSystem;
use \SitemapTemplateHelper;

class SitemapTemplateTest extends PHPUnit_Framework_TestCase
{
    const ViewPath = 'tests/data/views/';

    protected function getViewPath()
    {
        return EXTASY_PATH . self::ViewPath;
    }

    public function setUp()
    {
        $fs = DAO_FileSystem::getInstance();
        $fs->createPath(self::getViewPath());
        Configure::write(SitemapTemplateHelper::ConfigureKey, $this->getViewPath());
        CDumper::importFile(dirname(__FILE__) . '/import.sql');
    }

    public function tearDown()
    {
        $fs = DAO_FileSystem::getInstance();
        $fs->delete($this->getViewPath());
    }

    public function testHelperConfigurable()
    {
        Configure::write(SitemapTemplateHelper::ConfigureKey, '');
        $this->assertEquals(SitemapTemplateHelper::getViewPath(), SitemapTemplateHelper::getViewPath());
        //
        Configure::write(SitemapTemplateHelper::ConfigureKey, self::getViewPath());
        $this->assertEquals($this->getViewPath(), SitemapTemplateHelper::getViewPath());
    }

    /**
     * Создаем некорректный путь к файлу (вне папки VIEW_PATH)
     * @expectedException SitemapTemplateException
     */
    public function testcreateWithIncorrectPath()
    {
        SitemapTemplateHelper::create('../xx.tpl');

    }

    /**
     * Создаем некорректный путь (пустой) к файлу
     * @expectedException SitemapTemplateException
     */
    public function testCreateWithEmptyPath()
    {
        SitemapTemplateHelper::create('');
    }

    /**
     * Создаем шаблон в папку которой нет
     * @expectedException SitemapTemplateException
     */
    public function testCreateTemplateInUnexistedDirectory()
    {
        SitemapTemplateHelper::create('xxx/y.tpl');
    }

    /**
     * Создаем шаблон
     */
    public function testCreate()
    {
        SitemapTemplateHelper::create('test_template');
        $this->assertEquals(file_exists(SitemapTemplateHelper::getViewPath() . 'test_template.tpl'), true);
    }

    /**
     * Пытаемся создать папку вне шаблонной папки
     * @expectedException SitemapTemplateException
     */
    public function testCreateDirectoryWithIncorrectPath()
    {
        SitemapTemplateHelper::createDirectory('../templates');
    }

    /**
     * Пытаем создать папку на существующую папку
     * @expectedException SitemapTemplateException
     */
    public function testCreateDirectoryOnExistedDirectory()
    {
        mkdir(SitemapTemplateHelper::getViewPath(). 'test_template');
        SitemapTemplateHelper::createDirectory('test_template/');
    }

    /**
     * Создаем папку шаблона
     */
    public function testCreateDirectory()
    {
        SitemapTemplateHelper::createDirectory('test_template');
        $this->assertEquals(file_exists(SitemapTemplateHelper::getViewPath() . 'test_template'), true);
    }


    /**
     * Указываем путь к шаблону, а он вне папки VIEW_PATH
     * @expectedException SitemapTemplateException
     */
    public function testUpdateWithOuterViewPath()
    {
        SitemapTemplateHelper::update('../test_template', 'szContent', 'szComment', array());
    }

    /**
     * Указываем некорректный путь к шаблону, а он не существует
     * @expectedException SitemapTemplateException
     */
    public function testUpdateIncorrectPath()
    {
        SitemapTemplateHelper::update('test_template_XX', 'szContent', 'szComment', array());
    }

    /**
     * Обновляем с пустым путем
     */
    public function testUpdate()
    {
        SitemapTemplateHelper::create('test_template');
        SitemapTemplateHelper::update('test_template', 'szContent', '', array());
        //

        if (!file_exists(SitemapTemplateHelper::getViewPath() . 'test_template.tpl')) {
            $this->fail();
        }
        $this->assertEquals(file_get_contents(SitemapTemplateHelper::getViewPath() . 'test_template.tpl'), 'szContent');
        //
    }

    /**
     * Удаляем файл с несуществующим путем
     * @expectedException SitemapTemplateException
     */
    public function testRemoveUnExistsTemplate()
    {
        SitemapTemplateHelper::delete('xxx');
    }

    /**
     * Удаляем файл с некорректным путем (вне папки VIEW_PATH)
     * @expectedException SitemapTemplateException
     */
    public function testRemoveWithOuterViewPath()
    {
        SitemapTemplateHelper::delete('../test');
    }

    /**
     * Проверяем удаление
     */
    public function testRemoveTemplate()
    {
        SitemapTemplateHelper::create('test_template');
        SitemapTemplateHelper::delete('test_template');
        $this->assertEquals(file_exists(SitemapTemplateHelper::getViewPath() . 'test_template.tpl'), false);
    }

    /**
     * Удаляем папку с несуществующим путем
     * @expectedException SitemapTemplateException
     */
    public function testRemoveDirectoryWithUnexistedPath()
    {
        SitemapTemplateHelper::deleteDirectory('xxx');
    }

    /**
     * Удаляем папку вне пути VIEW_PATH
     * @expectedException SitemapTemplateException
     */
    public function testRemoveDirectoryOuterViewPath()
    {
        SitemapTemplateHelper::deleteDirectory('../settings');
    }

    /**
     * Удаляем папку
     */
    public function testRemoveDirectory()
    {
        SitemapTemplateHelper::createDirectory('test_template');
        $this->assertEquals(file_exists(SitemapTemplateHelper::getViewPath() . 'test_template/'), true);
    }

    /**
     * Создаем шаблон и к нему сохраняем коммент, проверяем, что коммент сохранился
     */
    public function testUpdateTemplateWithComment()
    {
        SitemapTemplateHelper::create('test_template');
        SitemapTemplateHelper::update('test_template', 'szContent', 'szComment', array());
        //
        $aInfo = SitemapTemplateHelper::getInfo('test_template');
        $this->assertEquals($aInfo['comment'], 'szComment');
        $this->assertEquals($aInfo['content'], 'szContent');
    }

}

class SitemapTemplateHelperFunctionTest
{
    public static $isCalled = false;

    public static function test($aUrlInfo, $aData = array())
    {
        self::$isCalled = true;
    }
}

?>
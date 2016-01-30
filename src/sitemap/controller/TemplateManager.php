<?
namespace Extasy\sitemap\controller;

use \SitemapTemplateHelper;
use \CSelect;
use \CInput;
use \CMSDesign;
use \CMS;
use \DAO;
use \SitemapTemplateException;
use \Exception;


class TemplateManager extends \AdminPage
{
	public function __construct()
	{
		parent::__construct();

		$this->addPost('tplFile,content,comment','post');
		$this->addPost('tplFile,content,comment','post');
		$this->addPost('tpl','create');
		$this->addPost('type,path','create');
		$this->addGet('tplFile','show');
	
	}
	/**
	 * Выводит дерево шаблонов
	 */ 
	public function main() 
	{
		$szTitle = 'Менеджер шаблонов';
		$aBegin = array($szTitle => '#');
		$aTabSheet = array(
			array('id' => 'tab_create','title' => 'Доступные шаблоны'));
		$this->outputHeader($aBegin,$szTitle);
		//
		$design = CMSDesign::getInstance('design');
		$design->tabSheetBegin($aTabSheet);
		// Создание файла (либо директории)
		$design->TabContentBegin($aTabSheet[0]['id']);
		$this->outputCreate();
		$design->tabContentEnd();
		//
		$design->tabSheetEnd();
		// 
		
		$this->outputFooter();
		$this->output();
	}
	
	/**
	 * Отображается форма создания шаблона или папки шаблонов
	 */
	public function outputCreate()
	{
		// Выводим селект выбора типа (шаблон/папка) и поле для ввода пути
		$select = new CSelect();
		$select->values = array(
			array('id' => 'template','name' => 'Шаблон'),
			array('id' => 'folder','name' => 'Папка'),
			);
		$select->name = 'type';

		$path = new CInput();
		$path->name = 'path';
		$path->value = '';
		$design = CMSDesign::getInstance();
		$design->formBegin();
		$design->tableBegin();
		$design->row2cell('Тип',$select->generate());
		$design->row2cell('Путь',$path->generate());
		$design->tableEnd();
		// Кнопка
		$design->submit('create','Создать');
		// Выводим дерево папки VIEW_PATH
		$design->tableBegin();
		$this->generateTemplateTree(); 
		$design->tableEnd();
		$design->formEnd();
	}
	private function generateTemplateTree($key = '',$nLevel = 0)
	{
		$path = VIEW_PATH.$key;
		$view_path = $key;
		
		$aFiles = \DAO_FileSystem::getInstance()->getDirContent($path);
		
		foreach ($aFiles as $row)
		{
			if ($row == '.svn') { continue;}
			if (is_file($path.$row))
			{
				$szRow = sprintf( '%s<span style="font-size:100%%">%s</span> // <a href="template-manager.php?tplFile=%s" target="_blank">Редактировать</a>',
							str_repeat('<span style="margin-left:20px;">&nbsp;</span>',$nLevel),
							$view_path.$row,
							preg_replace('#\.tpl$#','',$view_path.$row)
				);

				// Следовательно это папка
				CMSDesign::getInstance()->fullRow($szRow);
			}
			else
			{
				$szRow = sprintf('%s<span style="font-size:150%%">[%s]</span>',
							str_repeat('--',$nLevel),
							$key.$row
				);
				// Следовательно это папка
				CMSDesign::getInstance()->fullRow($szRow);
				$this->generateTemplateTree($key.$row.'/',$nLevel + 1);
			}
		}
	}
	/**
	 * Создает новый шаблон, либо папку под шаблоны
	 */
	public function create($type,$path)
	{
		//
		try 
		{
			switch ($type)
			{
				case 'template':
					SitemapTemplateHelper::create($path);
					break;
				case 'folder':
					SitemapTemplateHelper::createDirectory($path);
					break;
			}
		}
		catch (SitemapTemplateException $e)
		{
			$this->addError($e->getMessage());
		}
		$this->jump('./template-manager.php');
	}

	/**
	 * Сохраняет информацию о шаблоне
	 */ 
	public function post($tplFile,$szContent,$szComment)
	{
		try {
			SitemapTemplateHelper::update($tplFile,$szContent,$szComment);
		}
		catch (Exception $e)
		{
			$this->addError($e->getMessage());
		}

		$this->jumpBack();
	}
	/**
	 * Показывает шаблон
	 */
	public function show($tplFile)
	{
		$tplFile = htmlspecialchars($tplFile);
		try 
		{

			$szTitle = sprintf('Редактирование шаблона "%s"',$tplFile);

			$aBegin = array(
				'Менеджер шаблонов' => 'template-manager.php',
				$szTitle => '#'
				);
			// Получаем информацию о шаблоне
			$aInfo = SitemapTemplateHelper::getInfo($tplFile);
			// Сохраняем в объекты
			$oContent = new CINput();
			$oComment = new CInput();
			$oContent->name = 'content';
			$oContent->value = $aInfo['content'];

			$oContent->rows = 30;
			$oContent->style = 'width:99%;';
			$oComment->name = 'comment';
			$oComment->value = $aInfo['comment'];
			$oComment->rows = 10;
			$oComment->style = 'width:99%;';
			// Выводим
			$this->outputHeader($aBegin,$szTitle);
			$design = CMSDesign::getInstance();
			$design->buttons(array('К менеджеру' => 'template-manager.php'));
			$design->formBegin();
			$design->tableBegin();
			$design->row2cell('Шаблон',$oContent->generate());
			$design->row2cell('Комментарий',$oComment->generate());
			$design->tableEnd();
			$design->submit('submit','Сохранить');
			$design->hidden('tplFile',$tplFile);
			$design->formEnd();
			// Завершаем вывод
			$this->outputFooter();
			$this->output();
		}
		catch (Exception $e)
		{
			$this->addError($e->getMessage());
			$this->jump('./template-manager.php');
		}
	}
}
?>
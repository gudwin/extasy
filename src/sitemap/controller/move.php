<?
require_once LIB_PATH.'sitemap/additional/restore-url.php';
/**
 * 
 * @author Gisma
 * @todo Сделать пересчет сортировки при переносе документа
 */
class Sitemap_MoveController extends AdminPage {
	public function __construct()
	{
		parent::__construct();
		$this->addGet('id,to','move');
		$this->addPost('id,to','move');
		$this->AddGet('id','showMove');
	}
	public function move($id,$to)
	{
		try {
			$aDocument = Sitemap_Sample::get($id);
			if (empty($aDocument['document_name']))
			{
				throw new Exception('Requested id isn`t document');
			}
			
			Sitemap::updatePage($aDocument['document_name'],$aDocument['document_id'],$aDocument['name'],$aDocument['url_key'],$to);
			Sitemap::updateParentCount($aDocument['parent']);
			Sitemap::updateParentCount($to);
			// Если у переносимого документа есть дочерние элементы
			$child = Sitemap_Sample::selectChild($aDocument['id']);
			if (!empty($child)) {
				foreach ($child as $row) { 
					Sitemap_RestoreUrl::restoreOne($row['id'],$aDocument);	
				}
			}
			$this->addAlert('Документ перемещен');
		}
		catch (Exception $e) {
			$this->addError('Ошибка при перемещении документа');
		}
		$this->jumpBack();
	}
	/** 
	 * Выводит форму переноса документа
	 */
	public function showMove($id)
	{
		$aDocument = Sitemap_Sample::get($id);
		if (empty($aDocument['document_name']))
		{
			throw new Exception('Requested id isn`t document');
		}
		$szTitle = 'Перемещение документа - '.$aDocument['name'];
		$szTitle2 = 'Текущий URL - '.$aDocument['full_url'];
		$aButton = array(
			'Закрыть' => array(
				'id' => 'close_window',
				'value' => '#',
			));
		// Получаем список возможных скриптов, куда можем перенести
		require_once LIB_PATH.'sitemap/additional/cms.php';

		$aMove = Sitemap_CMS::whereToMove($aDocument['document_name']);
		
		$this->outputForm($aDocument,$aMove,$szTitle,$szTitle2,$aButton);
		

	}
	public function outputForm($aDocument,$aMove,$szTitle,$szTitle2,$aButton)
	{
		$design = CMSDesign::getInstance();;
		$design->popupBegin($szTitle);
		$design->header($szTitle);
		$design->header($szTitle2);
		$design->buttons($aButton);
		?>
		<script type="text/javascript" >
jQuery(document).ready(function () {
	jQuery('#close_window').click(function () {
		window.close();
		return false;
	});
		});
		</script>
		<?
		// Получаем список возможных скриптов
		$aTableHeader = array(
			array('&nbsp;',5),
			array('Страница',25),
			array('URL',55),
			);;
		$design->formBegin();
		$design->TableBegin();
		$design->tableHeader($aTableHeader);
		foreach ($aMove as $row)
		{
			$szRadio = sprintf('<input type="radio" name="to" value="%d" %s>',
				$row['id'],
				$aDocument['parent'] == $row['id']?' checked ':'' );

			$design->rowBegin();
			$design->listCell($szRadio);
			$design->listCell(htmlspecialchars($row['name']));
			$design->listCell(htmlspecialchars($row['full_url']));
			$design->rowEnd();
		}
		$design->tableEnd();
		$design->hidden('id',$aDocument['id']);
		$design->submit('submit','Переместить');;
		$design->formEnd();
		$design->popupEnd();
		parent::output();
	}
}
?>
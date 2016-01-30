<?php
use \Extasy\CMS;
/**
 * Класс для отображения ссылок
 * @author Gisma
 *
 */
class CMSDesignLinks {
	/**
	 * Отображает ссылку, открывающую попап
	 * @param string $href
	 * @param string $title
	 */
	public function popupLink($href,$title) {
		?>
		<a href="<?=\Extasy\CMS::getDashboardWWWRoot()?><?php print $href?>" onclick="
		window.open('<?=\Extasy\CMS::getDashboardWWWRoot()?><?php print $href?>',
				'_blank',
				'location=no,resizable=no,scrollbars=yes,titlebar=no,toolbar=no,menubar=no,width=800,height=800'); 
		return false;
		"><?php print $title?></a>
		<?php 
	}
	/**
	 * Возвращает код ссылки в стиле для редактирования
	 * @param string $link
	 */
	public function editLink($link = '#') {
		$strings = CMS_Strings::getInstance();
		$szEdit = $strings->getMessage('CMS_EDIT');
		$szHTTP_ROOT = \Extasy\CMS::getWWWRoot();
		return '<nobr><img alt="'.$szEdit.'" src="'. CMS::getResourcesUrl() .'extasy/pic/icons/edit.gif" /><a href="'.$link.'">'.$szEdit.'</a></nobr>';
	}
	/**
	 * Возвращает код ссылки для удаления в станд. стиле
	 * @param string $link
	 */
	public function deleteLink($link = '#') {
		$strings = CMS_Strings::getInstance();
		$szDelete = $strings->getMessage('CMS_DELETE');
		$szResult = '<nobr><img alt="'.$szDelete.'" src="'. CMS::getResourcesUrl() .'extasy/pic/icons/delete.gif" /><a href="'.$link.'" onclick="return confirm(\''.$strings->getMessage('CMS_CONFIRM_DELETE').'\')">'.$szDelete.'</a></nobr>'."\r\n";
		return $szResult;
	}
}
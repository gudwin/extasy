<?
use \Faid\DB;
/**
 *
 */
class SitemapXML 
{
	/**
	 * Обновляет запись об частоте
	 */
	public static function update($id,$priority,$change) 
	{
		$sql = 'UPDATE `%s` SET `sitemap_xml_priority`="%f",`sitemap_xml_change`="%s" WHERE `id`="%d"';
		$sql = sprintf($sql,SITEMAP_TABLE,
			floatval($priority),
			\Faid\DB::escape($change),
			intval($id)
		);

		DB::post($sql);
		$register = new SystemRegister('System/Sitemap/');
		if ($register->get('sitemap.xml.disable')->value == 0 ) {
			self::generate();
		}
	}
	/**
	 * Генерирует файл сайтмапа
	*/
	public static function generate()
	{
		$register = new SystemRegister('System/Sitemap');
		if ($register->get('sitemap.xml')->value == 0) {
			return;	
		}
		// Получаем все url сайта
		$sql = 'SELECT * FROM `'.SITEMAP_TABLE.'` where `visible`="1"';
		$aSitemap = DB::query($sql);
		//
		$xmlDocument = new DOMDocument('1.0','utf-8');
		//
		$urlset = $xmlDocument->createElement('urlset');
		$urlset->setAttribute('xmlns','http://www.sitemaps.org/schemas/sitemap/0.9');
		foreach ($aSitemap as $row)
		{
			$url = $xmlDocument->createElement('url');
			$loc = $xmlDocument->createElement('loc');
			$lastmod = $xmlDocument->createElement('lastmod');
			$changefreq = $xmlDocument->createElement('changefreq');
			$priority = $xmlDocument->createElement('priority');
			$loc->nodeValue = \Extasy\CMS::getWWWRoot().substr($row['full_url'],1);
			$lastmod->nodeValue = $row['date_updated'];
			$changefreq->nodeValue = $row['sitemap_xml_change'];
			$priority->nodeValue = $row['sitemap_xml_priority'];
			$url->appendChild($loc);
			$url->appendChild($lastmod);
			$url->appendChild($changefreq);
			$url->appendChild($priority);
			$urlset->appendChild($url);
		}
		$xmlDocument->appendChild($urlset);
		// Пишем в папку xml 
		$xmlContents = $xmlDocument->saveXML();
		file_put_contents(FILE_PATH.'sitemap.xml',$xmlContents);

	}
}
?>
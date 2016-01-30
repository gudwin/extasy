<?php
namespace Extasy\sitemap\Jobs;

use \Faid\DB;
use \DOMDocument;

class GenerateSitemapFile extends \Extasy\Schedule\Job {
	const SitemapLimit = 3000;

	protected function createSitemapDocument() {
		$result = new DOMDocument( '1.0', 'utf-8' );
		$urlset = $result->createElement( 'urlset' );
		$urlset->setAttribute( 'xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9' );
		$result->appendChild( $urlset );
		return $result;
	}

	protected function addToSitemap( $index, $sitemap, $url ) {
		if ( !empty( $url[ 'script' ] ) ) {
			if ( in_array( $url[ 'script' ],
						   array(
							   'scripts/support.php',
							   'scripts/externalLinks.php',
							   'scripts/last_comments.php',
							   'scripts/login.php',
							   'scripts/signup.php',
							   'scripts/forgot.php',
							   'scripts/profile.php'
						   ) )
			) {
				return;
			}
		}
		$failure = false;
		// Проверяем на размер
		if ( $sitemap->getElementsByTagName( 'url' )->length + 1 > self::SitemapLimit) {
			// Записываем в файл
			$failure = true;
		}
		//
		$urlElement = $sitemap->createElement( 'url' );
		$loc        = $sitemap->createElement( 'loc', 'http:'. $url[ 'full_url' ] );
		$lastmod    = $sitemap->createElement( 'lastmod', substr( $url[ 'date_updated' ], 0, 10 ) );
		$changefreq = $sitemap->createElement( 'changefreq', $url[ 'sitemap_xml_change' ] );
		$priority   = $sitemap->createElement( 'priority', $url[ 'sitemap_xml_priority' ] );
		$urlElement->appendChild( $loc );
		$urlElement->appendChild( $lastmod );
		$urlElement->appendChild( $changefreq );
		$urlElement->appendChild( $priority );
		$sitemap->getElementsByTagName( 'urlset' )->item( 0 )->appendChild( $urlElement );
	}

	/**
	 *
	 * @param DomDocument $index
	 * @param DomDocument $sitemap
	 */
	protected function storeSitemap( $index, $sitemap ) {

		// Записываем текущий файл
		$name = sprintf( 'sitemap_list_%d.xml',
						 $index->getElementsByTagName( 'sitemap' )->length );

		$result = $sitemap->saveXML();
		file_put_contents( FILE_PATH . $name, $result );
		//
		//
		$sitemapEl = $index->createElement( 'sitemap' );
		$loc       = $index->createElement( 'loc', 'http:'.\Extasy\CMS::getWWWRoot(). 'files/' . $name );
		$lastMod   = $index->createElement( 'lastmod', date( 'Y-m-d' ) );
		$sitemapEl->appendChild( $loc );
		$sitemapEl->appendChild( $lastMod );
		$index->getElementsByTagName( 'sitemapindex' )->item( '0' )->appendChild( $sitemapEl );
	}

	/**
	 *
	 * @param DomDocument $sitemapIndex
	 * @param DomDocument $sitemap
	 * @param array       $urls
     * @todo define how get list of documents to skip
	 */
	protected function processUrls( $sitemapIndex, $sitemap, $urlList ) {
		$skipDocuments = [
		];
		foreach ( $urlList as $url ) {
			if ( !in_array( $url['document_name'], $skipDocuments ) ) {
				$this->addToSitemap( $sitemapIndex, $sitemap, $url );
			}
		}
		$sitemapIndex->saveXML();
	}

	protected function action() {
		set_time_limit( 0 );

		$sitemapIndex = new DOMDocument( '1.0', 'utf-8' );
		$el           = $sitemapIndex->createElement( 'sitemapindex' );
		$el->setAttribute( 'xmlns', "http://www.sitemaps.org/schemas/sitemap/0.9" );
		$sitemapIndex->appendChild( $el );


		$sitemapUrlCount = DB::getField( 'select count(*) as `count` from `sitemap`', 'count' );
		$counter         = 0;

		while ( $counter < $sitemapUrlCount ) {
			$sql     = 'select * from `sitemap` where `document_name` <> "statistic_page" and `document_name` <> "externallink" order by `id` asc limit %d,%d';
			$sql     = sprintf( $sql, $counter, self::SitemapLimit );
			$urlList = DB::query( $sql );

			// Создаем первый документ
			$sitemap = $this->createSitemapDocument();
			// Обрабатываем его
			$this->processUrls( $sitemapIndex, $sitemap, $urlList );
			// СОхраняем результат
			$this->storeSitemap( $sitemapIndex, $sitemap );
			$counter +=  self::SitemapLimit;
		}

		file_put_contents( FILE_PATH . 'sitemap.xml', $sitemapIndex->saveXML() );


		self::restart();
	}

	public static function restart() {
		$job = new GenerateSitemapFile();
		$job->actionDate->setTime( '+1 Day' );
		$job->insert();
	}
} 
<?
use \Extasy\tests\system_register\Restorator;

Restorator::restore();


Restorator::restorePath('/System/', array(
    'Sitemap' => array(
        'visible' => 0,
        'sitemap.xml.disable' => 1,
        'sitemap.xml' => 1,
    ),
));
SystemRegisterSample::createCache();
?>
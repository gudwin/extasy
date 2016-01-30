<?php


namespace Extasy\Dashboard\Controllers;


use Extasy\Model\Model;
use Extasy\ORM\DBSimple;

class SectionIndex extends \Sitemap_Controller_Data_List
{
    const PagingSize = 50;
    /**
     * @var \Extasy\Model\Model
     */
    protected $modelName = null;

    public function __construct()
    {
        if (empty($this->modelName)) {
            throw new \RuntimeException('Please specify `modelName` property to be defined');
        }
        $sitemapInfo = $this->seekIndexDocument();

        if (!empty($sitemapInfo)) {
            $title = call_user_func([$this->modelName, 'getLabel'], Model::labelName);
            $this->title = $title;
            $this->begin = array(
                $title => '#'
            );
            $this->parent = intval($sitemapInfo['id']);

            $this->paging_size = static::PagingSize;
        } else {
            throw new \NotFoundException(sprintf('Instance of model `%s` not found', $this->modelName));
        }

        parent::__construct();
    }

    protected function seekIndexDocument()
    {
        $found = DBSimple::get(SITEMAP_TABLE, [
            'document_name' => $this->modelName,
        ]);

        return $found;
    }
} 
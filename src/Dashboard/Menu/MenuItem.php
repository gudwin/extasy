<?php
namespace Extasy\Dashboard\Menu;

use \Extasy\acl\ACLUser;
use Blogs\Models\Blog;
use \Extasy\Model\Model as extasyDocument;

class MenuItem extends \Extasy\Model\Model implements MenuItemInterface
{
    const ModelName = '\\Extasy\\Dashboard\\Menu\\MenuItem';

    const TableName = '';

    protected $items = null;

    public function __construct($documentData = array())
    {
        parent::__construct($documentData);

        $this->items = new ItemList();
    }

    public function isVisible()
    {
        try {
            ACLUser::checkCurrentUserGrants($this->rights->getValue());

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getChildren()
    {
        return $this->items;
    }

    public function getParseData()
    {
        $result = array(
            'name' => $this->name->getViewValue(),
            'link' => $this->link->getViewValue(),
            'code' => $this->code->getViewValue()
        );
        if ($this->items->getLength() > 0) {
            $result['children'] = $this->items->getParseData();
        }

        return $result;
    }

    public static function getFieldsInfo()
    {
        return array(
            'table' => self::TableName,
            'fields' => array(
                'id' => '\\Extasy\\Columns\\Index',
                'order' => '\\Extasy\\Columns\\Number',
                'rights' => '\\GrantColumn',
                'name' => '\\Extasy\\Columns\\Input',
                'code' => '\\Extasy\\Columns\\Html',
                'link' => '\\Extasy\\Columns\\Input',
            )
        );
    }
}

<?php


namespace Extasy\sitemap\Models;


use Extasy\Model\Model;

class SearchResultModel extends Model implements \JsonSerializable
{
    const TableName = '';
    const ModelName = '\\Extasy\\sitemap\\Models\\SearchResultModel';

    public function jsonSerialize()
    {
        return [
            'title' => $this->title->getValue(),
            'link' => $this->link->getValue(),
            'icon' => $this->link->getValue(),
            'text' => $this->text->getValue(),
            'html' => $this->html->getValue()
        ];
    }

    public static function getFieldsInfo()
    {
        return [
            'table' => static::TableName,
            'fields' => [
                'id' => '\\Extasy\\Columns\\Index',
                'title' => '\\Extasy\\Columns\\Input',
                'text' => '\\Extasy\\Columns\\Text',
                'html' => '\\Extasy\\Columns\\Html',
                'link' => '\\Extasy\\Columns\\Input',
                'icon' => '\\Extasy\\Columns\\Input',
            ]
        ];
    }
} 
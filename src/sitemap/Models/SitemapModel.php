<?php


namespace Extasy\sitemap\Models;

use Extasy\CMS;
use Extasy\Model\Model;

class SitemapModel extends Model {

    const PermissionName = 'Administrator/Sitemap';
    const TableName = SITEMAP_TABLE;
    const ModelName = '\\Extasy\\sitemap\\Models\\SitemapModel';
    public function getEditLink() {
        return sprintf('%ssitemap/edit.php?id=%d', CMS::getDashboardWWWRoot(), $this->id->getValue());
    }
    public function linkToModel( Model $model ) {
        $this->document_name = get_class( $model );
        $this->document_id = $model->id->getValue();

    }
    public static function getFieldsInfo() {
        return [
            'table' => self::TableName,
            'fields' => [
                'id' => '\\Extasy\\Columns\\Index',
                'name' => '\\Extasy\\Columns\\Input',
                'url_key' => '\\Extasy\\Columns\\Input',
                'full_url' => '\\Extasy\\Columns\\Input',
                'document_name' => [
                    'class' => '\\Extasy\\Columns\\Input',
                ],
                'document_id' => '\\Extasy\\Columns\\Integer',
                'date_created' => '\\Extasy\\Columns\\Datetime',
                'date_updated' => '\\Extasy\\Columns\\Datetime',
                'revision_count' => '\\Extasy\\Columns\\Input',
                'script' => '\\Extasy\\Columns\\Input',
                'order' => '\\Extasy\\Columns\\Integer',
                'parent' => [
                    'class' => '\\Extasy\\Columns\\BelongsTo',
                    'model' => static::ModelName,
                ],
                'count' => '\\Extasy\\Columns\\Integer',
                'script_admin_url' => '\\Extasy\\Columns\\Input',
                'script_manual_order' => '\\Extasy\\Columns\\Boolean',
                'sitemap_xml_priority' => '\\Extasy\\Columns\\Number',
                'sitemap_xml_change' => [
                    'class' => '\\Extasy\\Columns\\StaticSelect',
                    'values' => [
                        'always' => 'always',
                        'hourly' => 'hourly',
                        'daily' => 'daily',
                        'weekly' => 'weekly',
                        'month' => 'month'
                    ]
                ]
            ]
        ];
    }
} 
<?php


namespace Extasy\StaticApplication;


use Extasy\Model\Model;
use Faid\View\View;

class Page extends Model
{
    const EventName = 'StaticApplication.Page';

    public function generate()
    {
        $parseData = \EventController::callFilter(self::EventName, $this->data->getValue());

        $view = new View($this->tpl->getValue());
        $view->addHelper(new ViewHelper());
        $view->set($parseData);

        $content = $view->render();

        $writer = new Writer($this->url->getValue());
        $writer->write($content);
    }

    public static function getFieldsInfo()
    {
        return [
            'fields' => [
                'id' => '\\Extasy\\Columns\\Index',
                'url' => '\\Extasy\\Columns\\Input',
                'tpl' => '\\Extasy\\Columns\\Input',
                'data' => '\\Extasy\\Columns\\Serializeable',
            ]
        ];
    }
} 
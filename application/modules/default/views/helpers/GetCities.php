<?php
/**
 * @copyright Copyright (c) 2009 Tanasiychuk Stepan (http://stfalcon.com)
 */

/**
 * Хелпер отдает список городов
 */
class Default_View_Helper_GetCities extends Zend_View_Helper_Placeholder_Container_Standalone
{
    public function GetCities()
    {
        return array(
            'Актау',
            'Актобе',
            'Алматы',
            'Астана',
            'Атырау',
			'Жезказган',
			'Кокшетау',
            'Караганда',
            'Костанай',
            'Кызылорда',
            'Павлодар',
            'Петропавловск',
            'Семей',
            'Талдыкорган',
            'Тараз',
			'Уральск',
            'Усть-Каменогорск',
            'Шымкент'
        );
    }
}
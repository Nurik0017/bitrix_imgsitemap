<?php

namespace N7\Data;

use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Entity\IntegerField;
use Bitrix\Main\Entity\StringField;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Type;

Loc::loadMessages(__FILE__);

class FileDataTable extends DataManager
{
    // название таблицы
    public static function getTableName()
    {
        return 'n7_img_sitemap_files';
    }
    // создаем поля таблицы
    public static function getMap()
    {
        return array(
            new IntegerField('ID', array(
                'autocomplete' => true,
                'primary' => true
            )), // autocomplite с первичным ключом

            new StringField(
                // имя сущности
                "filename",
                array(
                    // обязательное поле
                    "required" => true,
                )
            ),

            new StringField(
                // имя сущности
                "path",
                array(
                    // обязательное поле
                    "required" => true,
                )
            ),

        );
    }
}

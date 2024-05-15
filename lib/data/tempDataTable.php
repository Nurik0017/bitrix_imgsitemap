<?php

namespace N7\Data;

use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Entity\IntegerField;
use Bitrix\Main\Entity\StringField;
use Bitrix\Main\Entity\TextField;
use Bitrix\Main\Entity\Validator;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Type;

Loc::loadMessages(__FILE__);

class TempDataTable extends DataManager
{
    // название таблицы
    public static function getTableName()
    {
        return 'n7_img_sitemap_temp';
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
                "url",
                array(
                    // обязательное поле
                    "required" => true,
                )
            ),

            new TextField('imgs', array(
                'save_data_modification' => function () {
                    return array(
                        function ($value) {
                            return serialize($value);
                        }
                    );
                },
                'fetch_data_modification' => function () {
                    return array(
                        function ($value) {
                            return unserialize($value);
                        }
                    );
                }
            )),


            /*
            new TextField('EDITIONS_ISBN', array(
                'save_data_modification' => function () {
                    return array(
                        function ($value) {
                            return serialize($value);
                        }
                    );
                },
                'fetch_data_modification' => function () {
                    return array(
                        function ($value) {
                            return unserialize($value);
                        }
                    );
                }
            )),*/
        );
    }

}

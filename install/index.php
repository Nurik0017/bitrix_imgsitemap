<?php
//подключаем основные классы для работы с модулем
use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Entity\Base;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use GetDdefaultValues as GlobalGetDdefaultValues;
use N7\Data\DataTable;
use N7\Data\FileDataTable;
use N7\Settings\GetDdefaultValues;
use N7\Sitemap\Xml;

Loc::loadMessages(__FILE__);
//в названии класса пишем название директории нашего модуля, только вместо точки ставим нижнее подчеркивание
class imgsitemap extends CModule
{
    public function __construct()
    {
        $arModuleVersion = array();
        //подключаем версию модуля (файл будет следующим в списке)
        include __DIR__ . '/version.php';
        //присваиваем свойствам класса переменные из нашего файла
        if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }
        //пишем название нашего модуля как и директории
        $this->MODULE_ID = 'imgsitemap';
        // название модуля
        $this->MODULE_NAME = Loc::getMessage('MYMODULE_MODULE_NAME');
        //описание модуля
        $this->MODULE_DESCRIPTION = Loc::getMessage('MYMODULE_MODULE_DESCRIPTION');
        //используем ли индивидуальную схему распределения прав доступа, мы ставим N, так как не используем ее
        $this->MODULE_GROUP_RIGHTS = 'N';
        //название компании партнера предоставляющей модуль
        $this->PARTNER_NAME = Loc::getMessage('MYMODULE_MODULE_PARTNER_NAME');
        $this->PARTNER_URI = 'https://вашсайт'; //адрес вашего сайта
    }
    //здесь мы описываем все, что делаем до инсталляции модуля, мы добавляем наш модуль в регистр и вызываем метод создания таблицы
    public function doInstall()
    {
        ModuleManager::registerModule($this->MODULE_ID);
        $this->installDB();
        $this->InstallFiles();
        $this->CreateDefaultValues(new GetDdefaultValues());
    }
    //вызываем метод удаления таблицы и удаляем модуль из регистра
    public function doUninstall()
    { 
        $this->uninstallDB();
        $this->UnInstallFiles(); 
        ModuleManager::unRegisterModule($this->MODULE_ID);
    }
    //вызываем метод создания таблицы из выше подключенного класса
    public function installDB()
    {
        if (Loader::includeModule($this->MODULE_ID)) {
            DataTable::getEntity()->createDbTable();
            FileDataTable::getEntity()->createDbTable();
        }
    }
    //вызываем метод удаления таблицы, если она существует
    public function uninstallDB()
    {
        if (Loader::includeModule($this->MODULE_ID)) {
            if (Application::getConnection()->isTableExists(Base::getInstance('\N7\Data\DataTable')->getDBTableName())) {
                $connection = Application::getInstance()->getConnection();
                $connection->dropTable(DataTable::getTableName());
            }


            if (Application::getConnection()->isTableExists(Base::getInstance('\N7\Data\FileDataTable')->getDBTableName())) {

                Xml::delete();

                $connection = Application::getInstance()->getConnection();
                $connection->dropTable(FileDataTable::getTableName());
            }
        }
    }


    public function InstallFiles()
    {
        // скопируем файлы на страницы админки из папки в битрикс, копирует одноименные файлы из одной директории в другую директорию
        CopyDirFiles(
            __DIR__ . "/admin",
            $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin",
            true, // перезаписывает файлы
            true  // копирует рекурсивно
        );
        // для успешного завершения, метод должен вернуть true
        return true;
    }
    // метод для удаления файлов модуля при удалении
    public function UnInstallFiles()
    {
        // удалим файлы из папки в битрикс на страницы админки, удаляет одноименные файлы из одной директории, которые были найдены в другой директории, функция не работает рекурсивно
        DeleteDirFiles(
            __DIR__ . "/admin",
            $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin"
        );
        // для успешного завершения, метод должен вернуть true
        return true;
    }


    public function CreateDefaultValues(GetDdefaultValues $values)
    {


        // подключаем модуль для видимости ORM класса
        Loader::includeModule($this->MODULE_ID);
        // добавляем запись в таблицу БД
        DataTable::add(
            array(
                "MAIN_SETINGS" => $values->Get("main"),
                "FILLES_SETINGS" => $values->Get("filles"),
                "IBLOCK_SETINGS"=> $values->Get("iblock"),
            )
        );
        // для успешного завершения, метод должен вернуть true
        return true;
    }
}

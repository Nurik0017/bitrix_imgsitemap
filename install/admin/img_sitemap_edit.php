<?
// определяем в какой папке находится модуль, если в bitrix, инклудим файл с меню из папки bitrix
if (is_dir($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/imgsitemap/")) {
    // присоединяем и копируем файл
    require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/imgsitemap/admin/img_sitemap_edit.php");
}
// определяем в какой папке находится модуль, если в local, инклудим файл с меню из папки local
if (is_dir($_SERVER["DOCUMENT_ROOT"] . "/local/modules/imgsitemap/")) {
    // присоединяем и копируем файл
    require_once($_SERVER["DOCUMENT_ROOT"] . "/local/modules/imgsitemap/admin/img_sitemap_edit.php");
}
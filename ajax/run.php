<?

use \Bitrix\Main\Loader;
use N7\Sitemap\GetImages;
use N7\Sitemap\Xml;
use N7\Settings\GetValues;
use N7\Data\TempDataTable;
use Bitrix\Main\Application;
use Bitrix\Main\Entity\Base;


require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");


if (!check_bitrix_sessid()) {
	return;
}


/// подготовка
Loader::includeModule("imgsitemap");
$step = intval($_REQUEST['step']);
$part = intval($_REQUEST['part']);
$progress = intval($_REQUEST['progress']);

$settings = new GetValues();
$resuslt = array(
	"part" => $part,
	"step" => $step
);



//часть 1 собираем картинки из инфоблока

if ($part === 1) {


	if (!Application::getConnection()->isTableExists(Base::getInstance('\N7\Data\TempDataTable')->getDBTableName())) {
		TempDataTable::getEntity()->createDbTable();
	}


	$ibIds = $settings->getIbIds();
	$data = GetImages::List($ibIds[$step]);
	$resuslt['progress'] = $progress + (400 / count($ibIds));

	$resuslt['progress'] = floor($resuslt['progress']);

	$resuslt['progressBarText'] = "Собираем данные из инфоблока(" .$ibIds[$step].")";

	foreach ($data->get() as $url => $imgs) {
		TempDataTable::add(
			array(
				"url" =>  $url,
				"imgs" => $imgs,
			)
		);
	}

	if (array_key_last($ibIds) == $step) {
		$resuslt['status'] = "endPart1";
		$resuslt['part'] = 2;
		$resuslt['step'] = 0;
	}
}


//часть 2 удаляем файлы 
elseif ($part == 2) {
	Xml::delete();


	$_SESSION['sitemapImgFile'] = array(
		"fileName" => $settings->new_sitemap_name(),
		"origFilename" => $settings->new_sitemap_name(),
		"count" => 1,
		"urlCount" => 0,
		"size" => 0,
	);

	$resuslt['progressBarText'] = "Удаляем старые карты изображений";
	$resuslt['progress'] = $progress + 100;
	$resuslt['progress'] = floor($resuslt['progress']);
	$resuslt['status'] = "endPart2";
	$resuslt['part'] = 3;
	$resuslt['step'] = 0;
}



//часть 3 создаем файлы 
elseif ($part == 3) {

	$offset = 200;

	$resData = TempDataTable::getList(array(
		'select' => array('*'),
		'limit' => $offset,
		'offset' => ($step == 0) ? 0 : $offset * ($step-1),
	))->fetchAll();


	foreach ($resData as $val) {


		if ($_SESSION['sitemapImgFile']['urlCount'] >= $settings->url_max_count() || $_SESSION['sitemapImgFile']['size'] >= $settings->img_max_size()) {
			$_SESSION['sitemapImgFile']['fileName'] = $_SESSION['sitemapImgFile']['origFilename'] . $_SESSION['sitemapImgFile']['count'];
			$_SESSION['sitemapImgFile']['count']++;
		}

		$res = Xml::write($_SESSION['sitemapImgFile']['fileName'], $val['url'], $val['imgs']);

		$_SESSION['sitemapImgFile']['urlCount'] = $res['urlCount'];
		$_SESSION['sitemapImgFile']['size'] = $res['fileSize'];
	}


	$resuslt['progress'] = $progress + (490 / ceil(TempDataTable::getCount() / $offset));
	$resuslt['progress'] = floor($resuslt['progress']);
	$resuslt['progressBarText'] = "Создаем карты изображений";

	if (empty($resData)) {
		$resuslt['test'] = TempDataTable::getCount();
		$resuslt['status'] = "endPart3";
		$resuslt['part'] = 4;
		$resuslt['step'] = 0;
	}



	/*
	$offset = 200;
	$resuslt['progress'] = $progress +490;
	$resuslt['progress'] = floor($resuslt['progress']);


	

*/
}



//финиш
elseif ($part == 4) {

	
	if (Application::getConnection()->isTableExists(Base::getInstance('\N7\Data\TempDataTable')->getDBTableName())) {
		$connection = Application::getInstance()->getConnection();
		$connection->dropTable(TempDataTable::getTableName());
	}

	unset($_SESSION['sitemapImgFile']);
	
	$resuslt['progress'] = 1000;
	$resuslt['progressBarText'] = "Готово";
	$resuslt['status'] = "finish";
}


/*
if ($_REQUEST['step'] == 2){
	$res['status'] = "ok";
}
*/
echo json_encode($resuslt);

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php';

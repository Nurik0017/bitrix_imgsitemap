<?
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php';
// пространство имен для автозагрузки модулей
use \Bitrix\Main\Loader;
use N7\MainFunction;
use \N7\Settings\GetValues;
use \N7\Data\SaveFields;

// получим права доступа текущего пользователя на модуль
$POST_RIGHT = $APPLICATION->GetGroupRight("hmarketing.d7");
// если нет прав - отправим к форме авторизации с сообщением об ошибке
if ($POST_RIGHT == "D") {
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}
// вывод заголовка
$APPLICATION->SetTitle("Настройки генерации img sitemap");
$APPLICATION->SetAdditionalCSS("/bitrix/panel/seo/sitemap.css");

// подключаем языковые файлы
IncludeModuleLangFile(__FILE__);
$aTabs = array(
	array(
		"DIV" => "seo_sitemap_main",
		// название вкладки в табах 
		"TAB" => "Настройки",
		// заголовок и всплывающее сообщение вкладки
		"TITLE" => "Общие настройки"
	),
	array(
		"DIV" => "img_sitemap_filess",
		// название вкладки в табах 
		"TAB" => "Статические страницы",
		// заголовок и всплывающее сообщение вкладки
		"TITLE" => "Файловая структура"
	),
	array(
		"DIV" => "img_sitemap_iblock",
		// название вкладки в табах 
		"TAB" => "Инфоблоки",
		// заголовок и всплывающее сообщение вкладки
		"TITLE" => "Структура информационных блоков"
	),

	array(
		"DIV" => "generation",
		// название вкладки в табах 
		"TAB" => "Генерация",
		// заголовок и всплывающее сообщение вкладки
		"TITLE" => "Генерация карты сайта"
	)

);
// отрисовываем форму, для этого создаем новый экземпляр класса CAdminTabControl, куда и передаём массив с настройками
$tabControl = new CAdminTabControl(
	"tabControl",
	$aTabs
);


\Bitrix\Main\UI\Extension::load("ui.progressbar");

Loader::includeModule("imgsitemap");

$setings =  new GetValues();




if (
	// проверка метода вызова страницы
	$REQUEST_METHOD == "POST"
	&&
	// проверка нажатия кнопок Сохранить
	$save != ""
	&&
	// проверка наличия прав на запись для модуля
	$POST_RIGHT == "W"
	&&
	// проверка идентификатора сессии
	check_bitrix_sessid()
) {

	$res = new SaveFields($fields);

	// если обновление прошло успешно
	if ($res->save()) {
		// перенаправим на новую страницу, в целях защиты от повторной отправки формы нажатием кнопки Обновить в браузере
		if ($save != "") {
			// если была нажата кнопка Сохранить, отправляем обратно на форму
			LocalRedirect("/bitrix/admin/img_sitemap_edit.php?mess=ok&lang=" . LANG . "&"
				. $tabControl->ActiveTabParam());
		}
	} else {
		// если в процессе сохранения возникли ошибки - получаем текст ошибки
		$mess = implode(", <br>", $res->getErrors());
		$message = new CAdminMessage("Ошибка сохранения: " . $mess);
	}
}




require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php';
// eсли есть сообщения об успешном сохранении, выведем их
if ($_REQUEST["mess"] == "ok") {
	CAdminMessage::ShowMessage(array("MESSAGE" => "Сохранено успешно", "TYPE" => "OK"));
}
// eсли есть сообщения об не успешном сохранении, выведем их
if ($message) {
	echo $message->Show();
}
// eсли есть сообщения об не успешном сохранении от ORM, выведем их
if ($bookTable->LAST_ERROR != "") {
	CAdminMessage::ShowMessage($bookTable->LAST_ERROR);
}
?>



<script>
	function changeVal(check) {
		check.value = check.checked ? "Y" : "N";
		console.log(check.value);
	}
</script>

<form method="POST" action="<?= $APPLICATION->GetCurPage() ?>" ENCTYPE="multipart/form-data" name="post_form">
	<?
	// проверка идентификатора сессии
	echo bitrix_sessid_post();
	// отобразим заголовки закладок
	$tabControl->Begin();




	$tabControl->BeginNextTab();
	?>




	<? /*foreach ($data['MAIN_SETINGS'] as $key => $value) : ?>
		<tr>
			<td width="40%"><?= $value['name'] ?></td>
			<td width="60%"><input type="<?= $value['type'] ?>" name="<?= "main_setings[" . $key . "][value]" ?>" value="<?= $value['value'] ?>" <? if ($value['type'] == "checkbox" and $value['value'] == "Y") echo " checked" ?>></td>
		</tr>
	<? endforeach; */ ?>


	<tr>
		<td width="40%" class="adm-detail-content-cell-l">Активность</td>
		<td width="60%" class="adm-detail-content-cell-r">
			<input type="hidden" name="fields[MAIN_SETINGS][active]" value="N">
			<input type="checkbox" name="fields[MAIN_SETINGS][active]" value="Y" <?= ($setings->active() === "Y") ? "checked" : "" ?> id="designed_checkbox_active" class="adm-designed-checkbox">
			<label class="adm-designed-checkbox-label" for="designed_checkbox_active" title=""></label>
		</td>
	</tr>
	<tr>
		<td width="40%" class="adm-detail-content-cell-l">Путь к файлу индекса Sitemap</td>
		<td width="60%" class="adm-detail-content-cell-r"><input type="text" name="fields[MAIN_SETINGS][adressSitemap]" value="<?= $setings->adressSitemap() ?>"></td>
	</tr>
	<tr>
		<td width="40%" class="adm-detail-content-cell-l">Название генерируемого файла</td>
		<td width="60%" class="adm-detail-content-cell-r"><input type="text" name="fields[MAIN_SETINGS][new_sitemap_name]" value="<?= $setings->new_sitemap_name() ?>"></td>
	</tr>

	<tr>
		<td width="40%" class="adm-detail-content-cell-l">Форматы изображений (расширения через запятую)</td>
		<td width="60%" class="adm-detail-content-cell-r"><input type="text" name="fields[MAIN_SETINGS][img_expansions]" value="<?= $setings->img_expansions() ?>"></td>
	</tr>	

	<tr>
		<td width="40%" class="adm-detail-content-cell-l">содержит не более * URL</td>
		<td width="60%" class="adm-detail-content-cell-r"><input type="number" name="fields[MAIN_SETINGS][url_max_count]" value="<?= $setings->url_max_count() ?>"></td>
	</tr>
	<tr>
		<td width="40%" class="adm-detail-content-cell-l">содержит не более * изображений для каждого URL</td>
		<td width="60%" class="adm-detail-content-cell-r"><input type="number" name="fields[MAIN_SETINGS][img_max_count]" value="<?= $setings->img_max_count() ?>"></td>
	</tr>
	<tr>
		<td width="40%" class="adm-detail-content-cell-l">Имеет размер не более * MB</td>
		<td width="60%" class="adm-detail-content-cell-r"><input type="number" name="fields[MAIN_SETINGS][img_max_size]" value="<?= $setings->img_max_size() ?>"></td>
	</tr>






	<? $tabControl->BeginNextTab(); ?>


	<? $arDirs = \CSeoUtils::getDirStructure(true, "s1", "/"); ?>



	<?
	if (isset($_REQUEST['dir'])  && check_bitrix_sessid()) {
		global $APPLICATION;

		$APPLICATION->RestartBuffer();

		$subdirs = \CSeoUtils::getDirStructure(true, "s1", $_REQUEST['dir']);
		\N7\MainFunction::getSubdirs($subdirs, $setings);

		die();
	}
	?>



	<script>
		function loaddir(check, dir, cont) {


			if (check.classList.contains('sitemap-opened')) {
				check.classList.remove("sitemap-opened");
				BX(cont).style.display = 'none';
			} else {
				BX.showWait('adm-detail-tabs-block');
				BX.ajax.get('<?= $APPLICATION->GetCurPageParam('', array()) ?>', {
					dir: dir,
					sessid: BX.bitrix_sessid()
				}, function(res) {
					check.classList.add("sitemap-opened");
					console.log(cont);

					BX(cont).innerHTML = res;
					BX(cont).style.display = 'block';

					BX.closeWait();

				});

			}


		}
	</script>


	<tr>
		<td width="40%" class="adm-detail-content-cell-l">Блок c изображениями</td>
		<td width="60%" class="adm-detail-content-cell-r"><input type="text" name="fields[FILLES_SETINGS][html_block]" value="<?= $setings->html_block() ?>"></td>
	</tr>


	<tr>
		<td width="40%" valign="top" class="adm-detail-content-cell-l">Структура сайта:</td>
		<td width="60%" class="adm-detail-content-cell-r">
			/
			<div id="subdirs_/">

				<? foreach ($arDirs as $arDir) : ?>
					<div class="sitemap-dir-item">
						<? if ($arDir['TYPE'] == "D") : ?>
							<span onclick="loaddir(this, '/<?= $arDir['FILE'] ?>', 'subdirs_/<?= $arDir['FILE'] ?>');" class="sitemap-tree-icon"></span>
						<? endif; ?>
						<span class="sitemap-dir-item-text">
							<input type="hidden" name="fields[FILLES_SETINGS][dirs][/<?= $arDir['FILE'] ?>]" value="N">
							<input type="checkbox" value="Y" <?= ($setings->dirs('/' . $arDir['FILE']) === "Y") ? "checked" : "" ?> name="fields[FILLES_SETINGS][dirs][/<?= $arDir['FILE'] ?>]" id="DIR_/<?= $arDir['FILE'] ?>" class="adm-designed-checkbox"><label class="adm-designed-checkbox-label" for="DIR_/<?= $arDir['FILE'] ?>"></label>
							<label for="DIR_/<?= $arDir['FILE'] ?>"><?= $arDir['NAME'] ?>(<?= $arDir['FILE'] ?>)</label>
						</span>
						<div id="subdirs_/<?= $arDir['FILE'] ?>" class="sitemap-dir-item-children" style="display: none;"></div>
					</div>
				<? endforeach; ?>
			</div>
		</td>
	</tr>


	<?
	if (isset($_REQUEST['type'])  || isset($_REQUEST['iblock']) && check_bitrix_sessid()) {
		global $APPLICATION;

		$APPLICATION->RestartBuffer();

	?>
		<td colspan="6" align="center">
			<table class="internal" style="width: 100%;">
				<tbody>
					<tr class="heading">
						<td align="left">Свойства <?= ($_REQUEST['type'] == "sect") ? "раздела" : "элемента"; ?></td>
					</tr>
					<tr>
						<td align="left">
							<?
							if ($_REQUEST['type'] == "sect") {
								\N7\MainFunction::getSectProps($_REQUEST['iblock'], $setings);
							} else {
								\N7\MainFunction::getElemProps($_REQUEST['iblock'], $setings);
							}

							?>
						</td>
					</tr>
				</tbody>
			</table>
		</td>
	<?
		die();
	}
	?>

	<? $tabControl->BeginNextTab(); ?>


	<tr>
		<td width="40%" class="adm-detail-content-cell-l">Только активные элементы и разделы</td>
		<td width="60%" class="adm-detail-content-cell-r">
			<input type="hidden" name="fields[IBLOCK_SETINGS][hide_no_active]" value="N">
			<input type="checkbox" name="fields[IBLOCK_SETINGS][hide_no_active]" value="Y" <?= ($setings->hide_no_active() === "Y") ? "checked" : "" ?> id="designed_checkbox_hide_no_active" class="adm-designed-checkbox">
			<label class="adm-designed-checkbox-label" for="designed_checkbox_hide_no_active" title=""></label>
		</td>
	</tr>

	<tr>
		<td colspan="2" align="center">

			<table class="internal" style="width: 80%;">
				<tbody>
					<tr class="heading">
						<td>Инфоблок</td>
						<td width="100">Разделы</td>
						<td width="120">Элементы</td>
					</tr>
					<? $iblockList = \N7\MainFunction::getIblokList(); ?>

					<? foreach ($iblockList as $iblock) : ?>
						<tr>
							<td style="text-decoration: none;">
								<a href="iblock_edit.php?lang=ru&amp;ID=<?= $iblock['ID'] ?>&amp;type=<?= $iblock['IBLOCK_TYPE_ID'] ?>&amp;admin=Y"><?= "[" . $iblock['ID'] . "] " . $iblock["NAME"] .  " (" . $iblock['IBLOCK_TYPE_ID'] . ")"; ?></a>
							</td>
							<td align="center">
								<input type="hidden" value="N" name="fields[IBLOCK_SETINGS][iblock][<?= $iblock['ID'] ?>][sections]">
								<input type="checkbox" name="fields[IBLOCK_SETINGS][iblock][<?= $iblock['ID'] ?>][sections]" value="Y" <?= ($setings->iblockSect($iblock['ID']) === "Y") ? "checked" : "" ?> data-type="sect" iblock-id="<?= $iblock['ID'] ?>" id="Sect<?= $iblock['ID'] ?>" onclick="setIblockActive(this, 'Sect<?= $iblock['ID'] ?>')" class="adm-designed-checkbox">
								<label class="adm-designed-checkbox-label" for="Sect<?= $iblock['ID'] ?>" title=""></label>
							</td>
							<td align="center">


								<span data-type="elem" iblock-id="<?= $iblock['ID'] ?>" onclick="setIblockActive2(this, 'Elem<?= $iblock['ID'] ?>')" class="sitemap-tree-icon-iblock"></span>


								<input type="hidden" value="N" name="fields[IBLOCK_SETINGS][iblock][<?= $iblock['ID'] ?>][elements]]">
								<input type="checkbox" name="fields[IBLOCK_SETINGS][iblock][<?= $iblock['ID'] ?>][elements]" value="Y" <?= ($setings->iblockElem($iblock['ID']) === "Y") ? "checked" : "" ?> data-type="elem" iblock-id="<?= $iblock['ID'] ?>" id="Elem<?= $iblock['ID'] ?>" onclick="setIblockActive(this, 'Elem<?= $iblock['ID'] ?>')" class="adm-designed-checkbox">
								<label class="adm-designed-checkbox-label" for="Elem<?= $iblock['ID'] ?>" title=""></label>



							</td>
						</tr>
						<tr style="display: none;" id="subdirs_row_Sect<?= $iblock['ID'] ?>">

						</tr>
						<tr style="display: none;" id="subdirs_row_Elem<?= $iblock['ID'] ?>">
						</tr>
					<? endforeach; ?>
				</tbody>
			</table>
		</td>
	</tr>



	<? $tabControl->BeginNextTab(); ?>

	<div id="bar">

	</div>

	<p>
		<input type="button" class="adm-btn-save" value="Запустить" onclick="run()" name="save" id="sitemap_run_button">
	</p>



	<script>
		var n7ProgressBar = new BX.UI.ProgressBar({
			size: BX.UI.ProgressBar.Size.LARGE,
			color: BX.UI.ProgressBar.Color.PRIMARY,
			statusType: BX.UI.ProgressBar.Status.PERCENT,
			maxValue: 1000,
			column: true,
		});






		function run() {

			BX('bar').append(n7ProgressBar.getContainer());
			BX('sitemap_run_button')
			BX.adminPanel.showWait(BX('sitemap_run_button'));
			generateSitemap(1, 0, 0);
		}


		function setIblockActive2(check, cont) {

			var iblockId = check.getAttribute('iblock-id');
			var type = check.getAttribute('data-type');

			if (check.classList.contains('sitemap-opened')) {
				console.log(check);
				BX('subdirs_row_' + cont).style.display = 'none';
			} else {
				

				BX.showWait('img_sitemap_iblock');
				BX.ajax.get('<?= $APPLICATION->GetCurPageParam('', array()) ?>', {
					iblock: iblockId,
					type: type,
					sessid: BX.bitrix_sessid()
				}, function(res) {
					BX.closeWait();
					BX('subdirs_row_' + cont).innerHTML = res;
					BX('subdirs_row_' + cont).style.display = 'table-row';
				});



				//row.cells[1].style.textDecoration = 'none';
			}

		}




		function setIblockActive(check, cont) {

			var iblockId = check.getAttribute('iblock-id');
			var type = check.getAttribute('data-type');

			if (!check.checked) {
				BX('subdirs_row_' + cont).style.display = 'none';
			} else {
				BX.showWait('img_sitemap_iblock');
				BX.ajax.get('<?= $APPLICATION->GetCurPageParam('', array()) ?>', {
					iblock: iblockId,
					type: type,
					sessid: BX.bitrix_sessid()
				}, function(res) {
					BX.closeWait();
					BX('subdirs_row_' + cont).innerHTML = res;
					BX('subdirs_row_' + cont).style.display = 'table-row';
				});



				//row.cells[1].style.textDecoration = 'none';
			}

		}


		function generateSitemap(part, step, progress) {
			BX.ajax.get('/run.php', {
				part: part,
				step: step,
				progress: progress,
				sessid: BX.bitrix_sessid()
			}, function(res) {
				var obj = JSON.parse(res);
				var step = obj.step + 1;
				console.log(res);
				n7ProgressBar.update(obj.progress);
				n7ProgressBar.setTextAfter(obj.progressBarText);

				if (obj.status != "finish") {
					generateSitemap(obj.part, step, obj.progress);
				} else {
					BX.adminPanel.closeWait(BX('sitemap_run_button'));
					n7ProgressBar.setColor(BX.UI.ProgressBar.Color.SUCCESS);
				}

			});
		}
	</script>

	<?
	// выводит стандартные кнопки отправки формы
	$tabControl->Buttons();


	?>
	<input class="adm-btn-save" type="submit" name="save" value="Сохранить настройки" />
	<?
	// завершаем интерфейс закладки
	$tabControl->End();
	require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php';





	?>
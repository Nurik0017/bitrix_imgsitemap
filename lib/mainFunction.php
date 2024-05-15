<?

namespace N7;

class MainFunction
{

	public static function getIblokList()
	{

		$res = \CIBlock::GetList(
			array(),
			array(
				'ACTIVE' => 'Y',
			),
			true
		);

		while ($ar_res = $res->Fetch()) {
			$resut[] = $ar_res;
		}

		return $resut;
	}

	public static function getElemProps($id, $setings)
	{

		$res = \CIBlock::GetProperties($id, array(), array("PROPERTY_TYPE" => "F"));
		$i = 0;


		while ($res_arr = $res->Fetch()) : ?>
			<p>
				<input type="hidden" value="N" name="fields[IBLOCK_SETINGS][iblock][<?= $id ?>][props][<?= $res_arr["ID"] ?>]">
				<input type="checkbox" value="Y" <?= ($setings->iblockElemProps($id,$res_arr["ID"]) === "Y") ? "checked" : "" ?>   name="fields[IBLOCK_SETINGS][iblock][<?= $id ?>][props][<?= $res_arr["ID"] ?>]" id="elem_prop_<?= $res_arr["ID"] ?>" class="adm-designed-checkbox">
				<label class="adm-designed-checkbox-label" for="elem_prop_<?= $res_arr["ID"] ?>"></label>
				<?= $res_arr["NAME"] . " [" . $res_arr["CODE"] . "]"; ?>
			</p>
		<?
			$i++;
		endwhile;

		if (!$i) {
			echo "<h4>Cвойства типом файл отсутствует</h4>";
		}
	}

	public static function getSectProps($id, $setings)
	{
		$dbUserFields = \Bitrix\Main\UserFieldTable::getList(array(
			'filter' => array('ENTITY_ID' => 'IBLOCK_' . $id . '_SECTION', 'USER_TYPE_ID' => 'file')
		));
		$i = 0;
		while ($arUserField = $dbUserFields->fetch()) : ?>
			<p>
				<input type="hidden" value="N" name="fields[IBLOCK_SETINGS][iblock][<?= $id ?>][sectProps][<?= $arUserField["FIELD_NAME"] ?>]">
				<input type="checkbox"  value="Y" <?= ($setings->iblockSectProps($id,$arUserField["FIELD_NAME"]) === "Y") ? "checked" : "" ?>   name="fields[IBLOCK_SETINGS][iblock][<?= $id ?>][sectProps][<?= $arUserField["FIELD_NAME"] ?>]" id="elem_sect_<?= $arUserField["ID"] ?>" class="adm-designed-checkbox">
				<label class="adm-designed-checkbox-label" for="elem_sect_<?= $arUserField["ID"] ?>"></label>
				<?= $arUserField["FIELD_NAME"] ?>
			</p>
		<? $i++;
		endwhile;

		if (!$i) {
			echo "<h4>Cвойства типом файл отсутствует</h4>";
		}
	}

	public static function getSubdirs($dirs ,$setings)
	{
		?>
		<? foreach ($dirs as $arDir) : ?>
			<div class="sitemap-dir-item">
				<? if ($arDir['TYPE'] == "D") : ?>
					<span onclick="loaddir(this, '<?= $arDir['DATA']['ABS_PATH'] ?>', 'subdirs_<?= $arDir['DATA']['ABS_PATH'] ?>');" class="sitemap-tree-icon"></span>
				<? endif; ?>
				<span class="sitemap-dir-item-text">
					<input type="hidden" name="fields[FILLES_SETINGS][dirs][<?= $arDir['DATA']['ABS_PATH'] ?>]" value="N">
					<input type="checkbox" name="fields[FILLES_SETINGS][dirs][<?= $arDir['DATA']['ABS_PATH'] ?>]" value="Y"  <?=($setings->dirs($arDir['DATA']['ABS_PATH'] )==="Y") ? "checked":""?>  id="DIR_<?= $arDir['DATA']['ABS_PATH'] ?>" class="adm-designed-checkbox"><label class="adm-designed-checkbox-label" for="DIR_<?= $arDir['DATA']['ABS_PATH'] ?>" title=""></label>
					<label for="DIR_<?= $arDir['DATA']['ABS_PATH'] ?>"><?= $arDir['NAME'] ?>(<?= $arDir['DATA']['ABS_PATH'] ?>)</label>
				</span>
				<div id="subdirs_<?= $arDir['DATA']['ABS_PATH'] ?>" class="sitemap-dir-item-children" style="display: none;"></div>
			</div>
		<? endforeach; ?>
<?
	}
}

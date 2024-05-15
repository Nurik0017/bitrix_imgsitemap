<?

namespace N7\Sitemap;

use \Bitrix\Main\FileTable;
use \Bitrix\Iblock\ElementPropertyTable;


class IblockImages extends GetImages
{
	private $result;
	private $elemIds;
	private $elemUrl;
	private $imgIds;
	private $upload_dir;
	private $imgPath;


	public function __construct(int $id)
	{

		$this->upload_dir = \COption::GetOptionString("main", "upload_dir", "upload");

		$this->GetElements($id);

		foreach ($this->getSettings()->iblockElemProps($id) as $propId) {
			$this->GetProps($propId);
		}


		$this->GetSection($id, $this->getSettings()->iblockSectProps($id));
		
		if ($this->imgIds) {
			$this->GetImgPath($this->imgIds);
		}


	}



	private function GetElements($id)
	{

		$arFilter['IBLOCK_ID'] = $id;

		if ($this->getSettings()->hide_no_active() === "Y") {
			$arFilter['ACTIVE'] = "Y";
		}

		$dbItems = \CIBlockElement::GetList( // Достаём все элементы инфоблока и их картинки
			[],
			$arFilter,
			false,
			false,
			['ID', 'IBLOCK_ID', 'DETAIL_PICTURE', 'DETAIL_PAGE_URL']
		);

		while ($arItem = $dbItems->GetNext()) {

			$this->elemIds[] = $arItem['ID'];
			$this->elemUrl[$arItem['ID']] = $arItem['DETAIL_PAGE_URL'];


			if ($arItem['DETAIL_PICTURE']) {
				$this->imgIds[] = $arItem['DETAIL_PICTURE'];

				$this->result[$arItem['ID']]["URL"] = $arItem['DETAIL_PAGE_URL'];
				$this->result[$arItem['ID']]["IMG_PATH"][] = $arItem['DETAIL_PICTURE'];
			}
		}
	}


	private function GetProps($id)
	{
		$dbItems = ElementPropertyTable::getList([ // Достаём доп. картинки
			'filter' => ['IBLOCK_ELEMENT_ID' => $this->elemIds, 'IBLOCK_PROPERTY_ID' => $id],
			'select' => ['VALUE', 'IBLOCK_ELEMENT_ID'],
		]);
		while ($arItem = $dbItems->fetch()) {
			if ($arItem['VALUE']) {

				$this->imgIds[] = $arItem['VALUE'];

				if (isset($this->result[$arItem['IBLOCK_ELEMENT_ID']]['IMG_PATH'])) {
					$this->result[$arItem['IBLOCK_ELEMENT_ID']]['IMG_PATH'][] = $arItem['VALUE'];
				} else {
					$this->result[$arItem['IBLOCK_ELEMENT_ID']]['URL'] = $this->elemUrl[$arItem['IBLOCK_ELEMENT_ID']];
					$this->result[$arItem['IBLOCK_ELEMENT_ID']]['IMG_PATH'][] = $arItem['VALUE'];
				}
			}
		}
		//return $this->result;
	}


	private function GetSection($id, $props)
	{


		$arFilter['IBLOCK_ID'] = $id;

		if ($this->getSettings()->hide_no_active() === "Y") {
			$arFilter['ACTIVE'] = "Y";
		}

		$arSelect = ["ID", "SECTION_PAGE_URL", "DETAIL_PICTURE"];

		if (is_array($props)) {
			$arSelect = array_merge($arSelect, $props);
		}



		$rsSections = \CIBlockSection::GetList(
			[],
			$arFilter,
			[],
			$arSelect
		);
		while ($arSction = $rsSections->GetNext()) {


			if ($arSction['DETAIL_PICTURE']) {
				$this->imgIds[] = $arSction['DETAIL_PICTURE'];

				$this->result[$arSction['ID']]['URL'] = $arSction['SECTION_PAGE_URL'];
				$this->result[$arSction['ID']]['IMG_PATH'][] = $arSction['DETAIL_PICTURE'];
			}

			foreach ($props as $prop) {

				if (!empty($arSction[$prop])) {
					$this->result[$arSction['ID']]['URL'] = $arSction['SECTION_PAGE_URL'];

					if (is_array($arSction[$prop])) {

						foreach ($arSction[$prop] as $propVal) {
							$this->imgIds[] = $propVal;
							$this->result[$arSction['ID']]['IMG_PATH'][] = $propVal;
						}
					} else {
						$this->imgIds[] = $arSction[$prop];
						$this->result[$arSction['ID']]['IMG_PATH'][] = $arSction[$prop];
					}
				}
			}
		}
	}



	private function GetImgPath(array $ids)
	{
		$dbItems = FileTable::getList([ // Данные о картинках одним запросом
			'filter' => ['ID' => $ids],
			'select' => ['ID', 'SUBDIR', 'FILE_NAME']
		]);
		while ($arItem = $dbItems->fetch()) {
			$src = "/" . $this->upload_dir . "/" . $arItem["SUBDIR"] . "/" . $arItem["FILE_NAME"];
			$src = str_replace("//", "/", $src);
			if (defined("BX_IMG_SERVER")) {
				$src = BX_IMG_SERVER . $src;
			}

			$this->imgPath[$arItem['ID']] = $src;
		}
	}

	public function get()
	{
		foreach ($this->result as $val) {
			foreach ($val['IMG_PATH'] as $key => $path) {
				$path_parts = pathinfo($this->imgPath[$path]);

				if(in_array($path_parts['extension'], $this->getSettings()->get_img_expansions())){
					$result[$val['URL']][] = $this->imgPath[$path];

					if ($this->max_coun_imgs($key + 1)) {
						break;
					}
				}	
			}
		}
		return $result;
	}
}

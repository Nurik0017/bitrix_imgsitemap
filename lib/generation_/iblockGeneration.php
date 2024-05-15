<?
namespace N7\Generation;
use \Bitrix\Main\FileTable;
use \Bitrix\Iblock\ElementPropertyTable;


class IblockGeneration 
{	
	protected $iblock;
	protected $result;
	protected $elemIds;
	protected $elemUrl;
	protected $imgIds;
	protected $imgPath;
	protected $upload_dir;
	protected $active = "Y";

	public function __construct(array $array, string $active, )
	{
		$this->iblock = $array;
		$this->active = $active;

		$this->upload_dir = \COption::GetOptionString("main", "upload_dir", "upload");

		if($this->iblock['elements'] == "Y"){
			$this->GetElements();
		}
		
		if($this->iblock['sections'] == "Y"){
			$this->GetSection();
		}

	
		foreach ($this->iblock['props'] as $id =>$name)
		{
			$this->GetProps($id);
		}
		
		$this->GetImgPath($this->imgIds);
	}


	protected function GetElements ()
	{
		$dbItems = \CIBlockElement::GetList( // Достаём все элементы инфоблока и их картинки
			[],
			['IBLOCK_ID' => $this->iblock["id"], 'ACTIVE' => $this->active],
			false,
			false,
			['ID', 'DETAIL_PICTURE', 'DETAIL_PAGE_URL']
		);

		while ($arItem = $dbItems->GetNext()) {	

			$this->elemIds[] = $arItem['ID'];
			$this->elemUrl[$arItem['ID']] = $arItem['DETAIL_PAGE_URL'];


			if ($arItem['DETAIL_PICTURE']) {
				$this->imgIds[] =$arItem['DETAIL_PICTURE'];

				$ids[$arItem['ID']]["URL"] = $arItem['DETAIL_PAGE_URL'];
				$ids[$arItem['ID']]["IMG_PATH"][] = $arItem['DETAIL_PICTURE'];
			}		
		}	

		//return $this->result = $ids;
	}

	protected function GetSection ()
	{
		$rsSections = \CIBlockSection::GetList(
			[], 
			['IBLOCK_ID' => $this->iblock["id"]],
			[],
			["ID", "SECTION_PAGE_URL" ,"DETAIL_PICTURE"]
		
		
		);
		while ($arSction = $rsSections->GetNext())
		{
			
			if ($arSction['DETAIL_PICTURE']) {
				$this->imgIds[] =$arSction['DETAIL_PICTURE'];

				$this->result[$arSction['ID']]['URL']=$arSction['SECTION_PAGE_URL'];
				$this->result[$arSction['ID']]['IMG_PATH'][]=$arSction['DETAIL_PICTURE'];	
			}
		}
	}


	protected function GetProps($id)
	{
		$dbItems = ElementPropertyTable::getList([ // Достаём доп. картинки
			'filter' => ['IBLOCK_ELEMENT_ID' => $this->elemIds, 'IBLOCK_PROPERTY_ID' => $id],
			'select' => ['VALUE', 'IBLOCK_ELEMENT_ID'],
		]);
		while ($arItem = $dbItems->fetch()) {
			if ($arItem['VALUE']) {		

				$this->imgIds[] =$arItem['VALUE'];

				if(isset($this->result[$arItem['IBLOCK_ELEMENT_ID']]['IMG_PATH'])){
					$this->result[$arItem['IBLOCK_ELEMENT_ID']]['IMG_PATH'][]=$arItem['VALUE'];
				}
				else{
					$this->result[$arItem['IBLOCK_ELEMENT_ID']]['URL']=$this->elemUrl[$arItem['IBLOCK_ELEMENT_ID']];
					$this->result[$arItem['IBLOCK_ELEMENT_ID']]['IMG_PATH'][]=$arItem['VALUE'];
				}
				

			}
		}
		//return $this->result;
	}


	protected function GetImgPath(array $ids)
	{
		$dbItems = FileTable::getList([ // Данные о картинках одним запросом
			'filter' => ['ID' => $ids],
			'select' => ['ID', 'SUBDIR', 'FILE_NAME']
		]);
		while ($arItem = $dbItems->fetch()) {
			$src = "/".$this->upload_dir."/".$arItem["SUBDIR"]."/".$arItem["FILE_NAME"];
			$src = str_replace("//", "/", $src);
			if (defined("BX_IMG_SERVER")) {
				$src = BX_IMG_SERVER . $src;
			}
			$this->imgPath[$arItem['ID']] = $src;
		}
	}

	public function GetResult(){	
		foreach ($this->result as $val){
			foreach ($val['IMG_PATH'] as $key => $path){
				$result[$val['URL']][] = $this->imgPath[$path];
			}		
		}

		return $result;

	}

}
?>
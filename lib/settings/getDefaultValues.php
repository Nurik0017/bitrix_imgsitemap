<?
namespace N7\Settings;

class GetDdefaultValues
{
	protected $MainValues = array(
		"active" => "N",
		"adressSitemap" => "/sitemap.xml",
		"new_sitemap_name" => "sitemap-images",
		"url_max_count" => 50000,
		"img_max_count" => 1000,
		"img_max_size" => 50,
		"img_expansions" => "jpg, gif, bmp, png, jpeg, webp",
	);

	protected $FillesValues = array(
		//Блок c изображениями
		"html_block" => "body",

		"dirs" => array(
			//"/test2/index.php" => "test2"
		),
	);


	protected $IblockValues = array(
		//Не включать неактивные элементы и разделы"
		"hide_no_active" => "Y",
		"iblock" => array(),

		/*"iblock" => array(
			"11" => array(
				"id" => 11,
				"elements" => "Y",
				"sections" => "Y",
				"props" => array(
					"116" =>"Картинки",
					"125" => "Фотогалерея"
				)
			),
		),*/
	);

	public function Get($key)
	{
		switch ($key) {
			case "main":
				return $this->MainValues;
			case "filles":
				return $this->FillesValues;
			case "iblock":
				return $this->IblockValues;
			default:
				return false;
		}
		
	}
}

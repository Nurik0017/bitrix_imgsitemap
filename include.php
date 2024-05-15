<?
CModule::AddAutoloadClasses(
	"imgsitemap",
	array(
		"\N7\Data\DataTable" => "lib/data/dataTable.php",
		"\N7\Data\FileDataTable" => "lib/data/fileDataTable.php",
		"\N7\Data\TempDataTable" => "lib/data/tempDataTable.php",
		"\N7\Data\SaveFields" => "lib/data/saveFields.php",
		
		"\N7\Settings\GetValues" => "lib/settings/getValues.php",
		"\N7\Settings\GetDdefaultValues" => "lib/settings/getDefaultValues.php",
		//"\N7\Generation\IblockGeneration" => "lib/generation/iblockGeneration.php",
		//"\N7\Generation\StaticFileGeneration" => "lib/generation/staticFileGeneration.php",
		//"\N7\Generation\Xml" => "lib/generation/xml.php",
		"\N7\Sitemap\GetImages" => "lib/sitemap/getImages.php",
		"\N7\Sitemap\IblockImages" => "lib/sitemap/iblockImages.php",
		"\N7\Sitemap\StaticFileImages" => "lib/sitemap/staticFileImages.php",
		"\N7\Sitemap\Xml" => "lib/sitemap/xml.php",
		"\N7\MainFunction" => "lib/mainFunction.php",
	)
);

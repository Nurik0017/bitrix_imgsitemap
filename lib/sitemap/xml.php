<?

namespace N7\Sitemap;

use N7\Data\FileDataTable;
use \N7\Settings\GetValues;

class Xml
{

	private static $xmlNamespace = "http://www.google.com/schemas/sitemap-image/1.1";


	/*
	public function __construct($filename)
	{

		$this->filename = $filename;

		$this->path = self::setPath($this->filename);

		if (file_exists($this->path)) {
			$this->xml = new \SimpleXMLElement(file_get_contents($this->path));
		} else {
			$this->xml = $this->createNewXml();
		}
	}
	*/



	private static function setPath($filename)
	{
		return $_SERVER['DOCUMENT_ROOT'] . "/" . $filename . ".xml";
	}


	private static function indexXmlDelNode($path, $key)
	{
		$xml = new \SimpleXMLElement(file_get_contents($path));

		$count = count($xml->sitemap);

		$j = 0;

		for ($i = 0; $i < $count; $i++) {
			if (strpos($xml->sitemap[$j]->loc, $key) !== false) {

				unset($xml->sitemap[$j]);
				$j = $j - 1;
			}
			$j = $j + 1;
		}

		file_put_contents($path, $xml->asXML());
	}

	private static function getFiles()
	{
		return  FileDataTable::getList(array(
			'select' => array('*')
		))->fetchAll();
	}


	public static function delete()
	{
		$files =  self::getFiles();
		$setings = new GetValues();


		foreach ($files as $file) {
			if (file_exists($file['path'])) {	
				unlink($file['path']);
			}

			if (file_exists($setings->adressSitemap())) {
				self::indexXmlDelNode($setings->adressSitemap(),str_replace($_SERVER['DOCUMENT_ROOT'], "https://" . $_SERVER['SERVER_NAME'], $file['path']));
			}

			FileDataTable::delete($file['ID']);
		}
	}





	public static function getFileSize($path)
	{
		// Получаем размер файла в байтах
		$sizeInBytes = filesize($path);

		// Конвертируем в мегабайты
		$sizeInMB = $sizeInBytes / (1024 * 1024);

		return round($sizeInMB, 3);
	}

	private  static function createNewXml()
	{
		$newXml = '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"></urlset>';
		return 	new \SimpleXMLElement($newXml);
	}

	private  static function createNewIndexXml()
	{
		$newXml = '<?xml version="1.0" encoding="UTF-8"?><sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></sitemapindex>';
		return 	new \SimpleXMLElement($newXml);
	}



	public static function writeIndex($path)
	{
		$files =  self::getFiles();

		if (file_exists($path)) {
			$xml = new \SimpleXMLElement(file_get_contents($path));
		} else {
			$xml = self::createNewIndexXml();
		}

		foreach ($files as $file) {
			$sitemapParam = $xml->addChild('sitemap');
			$sitemapParam->addChild('loc',  str_replace($_SERVER['DOCUMENT_ROOT'], "https://" . $_SERVER['SERVER_NAME'], $file['path']));
		}

		file_put_contents($path, $xml->asXML());
	}


	public static function write(string $filename, string $url, array $imgs)
	{

		$path = self::setPath($filename);

		if (file_exists($path)) {
			$xml = new \SimpleXMLElement(file_get_contents($path));
		} else {
			$xml = self::createNewXml();
			FileDataTable::add(
				array(
					"filename" => $filename,
					"path" => $path,
				)
			);
		}

		$urlParam = $xml->addChild('url');
		$urlParam->addChild('loc', "https://" . $_SERVER['SERVER_NAME'] . $url);


		foreach ($imgs as $img) {
			$image = $urlParam->addChild('image:image', null, self::$xmlNamespace);
			$image->addChild("image:loc", "https://" . $_SERVER['SERVER_NAME'] . $img, self::$xmlNamespace);
		}

		file_put_contents($path, $xml->asXML());



		$result['urlCount'] = count($xml->url);
		$result['fileSize'] = self::getFileSize($path);
		$result['filiname'] = $filename;
		$result['path'] = $path;


		return $result;
	}
}

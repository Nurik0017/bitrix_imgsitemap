<?

namespace N7\Generation;

class Xml
{

	private \SimpleXMLElement $xml;

	public function __construct($file)
	{
		$this->xml = simplexml_load_file($file);
	}


	public function create($name)
	{
		$path = $_SERVER['DOCUMENT_ROOT'] . "/" . $name . ".xml";

		$dom = new \domDocument("1.0", 'utf-8');
		$urlset = $dom->createElement("urlset");
		$urlset->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
		$urlset->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:image', 'http://www.google.com/schemas/sitemap-image/1.1');

		$dom->appendChild($urlset);
		$dom->save($path);

		return $path;
	}


	public function write($url, $imgs)
	{	
		
		$urlset = $this->xml->urlset;

		$url = $this->xml->createElement("url");
		$login = $this->xml->createElement("loc", $url);
		$image = $this->xml->createElement("image:image");

		foreach ($imgs as $img){
			$imageLocation = $this->xml->createElement("image:loc", $this->xml . $img);
 			$image->appendChild($imageLocation);
		}

		$url->appendChild($login);
 		$url->appendChild($image);
		$urlset->appendChild($url);

		/*
		foreach ($books as $book) {
			$id = $book->id;
			$title = $book->name;
			$price = $book->price;
			print_r ("The title of the book $id is $title and it costs $price." . "\n");
		}
		*/

		/*
		//Добавим новый узел в имеющийся XML
		$sXML = new \SimpleXMLElement($path); // загрузка в XML
		$newchild = $sXML->addChild("urlset");
		//Добавление параметров записи
		$newchild->addChild("name", "Банан");
		$newchild->addChild("price", "$3.00");
		$newchild->addChild("discount", "0.3%");
		*/
	}
}

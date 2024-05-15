<?
namespace N7\Generation;

class StaticFileGeneration
{	

	protected $htmlBlock;
	protected $url;
	public $result;

	public function __construct($url, $htmlBlock)
	{	
		include_once 'phpQuery.php';
		$this->url = $url;
		$this->$htmlBlock = $htmlBlock;
		
		$output = $this->getPage($this->url);

		$this->getImgs($output);
	}


	protected function getPage($url)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$output = curl_exec($ch);
		curl_close($ch);

		return $output;
	}

	
	protected function getImgs($output)
	{
		
		$doc = \phpQuery::newDocument($output);
		$entry = $doc->find('.row img');
		



		foreach ($entry as $row) {		
			$link = pq($row);
			$imgSrc = pq($link)->attr('src');
			if(!empty($imgSrc)){
				$res[$this->url][]=$imgSrc;
			}
		}

		$this->result = $res;
	}

}

?>
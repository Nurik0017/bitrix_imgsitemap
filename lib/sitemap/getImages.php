<?
namespace N7\Sitemap;
use \N7\Settings\GetValues;

abstract class GetImages
{
	public static function getSettings()
	{
		return new GetValues();
	}

	protected function max_coun_imgs($num)
	{	
		if ($num == $this->getSettings()->img_max_count())
		{
			return true;
		}
		else
		{
			return false;
		}
	}


	public static function List($val)
	{
		
		if(is_int($val)){
			return new IblockImages($val);
		}
		elseif(preg_match('/\//', $val)){
			return new StaticFileImages($val);
		}
		else{
			return false;
		}
		

		//return $test->Get();
	}

	abstract public function get();
}

?>
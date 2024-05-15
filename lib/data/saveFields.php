<?

namespace N7\Data;

use \N7\Settings\GetValues;

class SaveFields
{
	private $fields;
	private $result;
	private $datTable;
	private $dataVal;
	private $reflectionClass;
	private $test;
	private $errors;

	public function __construct($fields)
	{
		$this->fields = $fields;
		$setting = new GetValues();
		$this->dataVal = $setting->getAll();
		$this->reflectionClass = new \ReflectionClass($this);
		$this->datTable = new DataTable;
	}


	private function addError($text)
	{
		$this->errors[] = $text;
	}

	public function getErrors()
	{
		return $this->errors;
	}


	private function validateСheckbox($val)
	{
		switch ($val) {
			case "Y":
				return "Y";
			case "N":
				return "N";
			default:
				$this->addError("Ошибка чекбокса");
		}
	}



	private function validateNumber($val)
	{
		if ($val > 0) {
			return $val;
		} {
			$this->addError("Значение меньше или равно 0");
		}
	}

	private function validateFilename($filename)
	{
		if (!empty($filename) && preg_match('/^[a-zA-Z0-9\.\-_]+$/', $filename)) {
			return $filename;
		} else {
			$this->addError("Имя файла содержит недопустимые символы");
		}
	}


	private function setActive()
	{
		$this->result['MAIN_SETINGS']['active'] = $this->validateСheckbox($this->fields['MAIN_SETINGS']['active']);
	}


	private function setAdressSitemap()
	{
		if (preg_match('/\.xml$/i', $this->validateFilename($this->fields['MAIN_SETINGS']['adressSitemap']))) {
			$this->result['MAIN_SETINGS']['adressSitemap'] = $this->validateFilename($this->fields['MAIN_SETINGS']['adressSitemap']);
		} else {
			$this->addError("Файл не XML");
		}
	}


	private function setNew_sitemap_name()
	{
		$this->result['MAIN_SETINGS']['new_sitemap_name'] = $this->validateFilename($this->fields['MAIN_SETINGS']['new_sitemap_name']);
	}


	private function setUrl_max_count()
	{
		$this->result['MAIN_SETINGS']['url_max_count'] = $this->validateNumber($this->fields['MAIN_SETINGS']['url_max_count']);
	}

	private function setImg_img_expansions()
	{
		$this->result['MAIN_SETINGS']['img_expansions'] = $this->fields['MAIN_SETINGS']['img_expansions'];
	}


	private function setImg_max_count()
	{
		$this->result['MAIN_SETINGS']['img_max_count'] = $this->validateNumber($this->fields['MAIN_SETINGS']['img_max_count']);
	}

	private function setImg_max_size()
	{
		$this->result['MAIN_SETINGS']['img_max_size'] = $this->validateNumber($this->fields['MAIN_SETINGS']['img_max_size']);
	}

	private function setHtml_block()
	{	
		$this->result['FILLES_SETINGS']['html_block'] = $this->fields['FILLES_SETINGS']['html_block'];
	}

	private function setDirs()
	{	
		$this->result['FILLES_SETINGS']['dirs'] = array_merge($this->dataVal['FILLES_SETINGS']['dirs'], $this->fields['FILLES_SETINGS']['dirs']);
	}


	private function setHide_no_active()
	{	
		$this->result['IBLOCK_SETINGS']['hide_no_active'] = $this->validateСheckbox($this->fields['IBLOCK_SETINGS']['hide_no_active']);
	}


	private function setIblocke()
	{	

		foreach ($this->fields['IBLOCK_SETINGS']['iblock'] as $key =>$val)
		{	


			$this->result['IBLOCK_SETINGS']['iblock'][$key]['sections'] =$val['sections'];
			$this->result['IBLOCK_SETINGS']['iblock'][$key]['elements'] =$val['elements'];
			

			if($val['elements'] == "Y")
			{	

				if (is_array($val['props'])){
					$this->result['IBLOCK_SETINGS']['iblock'][$key]['props'] = $val['props'];
				}
				else
				{
					$this->result['IBLOCK_SETINGS']['iblock'][$key]['props'] = $this->dataVal['IBLOCK_SETINGS']['iblock'][$key]['props'];
				}	
			}

			if($val['sections'] == "Y")
			{	

				if (is_array($val['sectProps'])){
					$this->result['IBLOCK_SETINGS']['iblock'][$key]['sectProps'] = $val['sectProps'];
				}
				else
				{
					$this->result['IBLOCK_SETINGS']['iblock'][$key]['sectProps'] = $this->dataVal['IBLOCK_SETINGS']['iblock'][$key]['sectProps'];
				}
			}
		}


		//$this->result['IBLOCK_SETINGS']['iblock'] = $this->fields['IBLOCK_SETINGS']['iblock'];
	}


	private function callMethod(string $method, array $args = [])
	{
		$reflectionMethod = new \ReflectionMethod(get_class($this), $method);
		$reflectionMethod->setAccessible(true);
		return $reflectionMethod->invokeArgs($this, $args);
	}


	public function save()
	{

		foreach ($this->reflectionClass->getMethods() as $method) {
			$name = $method->getName();
			if (preg_match('/^set/i', $name)) {
				$this->callMethod($name);
			}
		}

		if ($this->errors) {
			return false;
		} 

		$res = $this->datTable->Update(1, $this->result);
		if ($res->isSuccess()) {
			return true;
		}

		// если обновление прошло не успешно 
		if (!$res->isSuccess()) {
			global $APPLICATION;
			// если в процессе сохранения возникли ошибки - получаем текст ошибки
			if ($e = $APPLICATION->GetException())
				$this->addError($e);
			else {
				$mess = print_r($res->getErrorMessages(), true);
				$this->addError($mess);
			}
			return false;	
		}
	}
}

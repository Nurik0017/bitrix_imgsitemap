<?
namespace N7\Settings;
use N7\Data\DataTable;

class GetValues
{	
	private $data;

	public function __construct()
	{
		$this->data = DataTable::GetByID(1)->fetch();
	}


	public function active(){
		return  $this->data['MAIN_SETINGS']['active'];
	}

	public function adressSitemap(){
		return  $this->data['MAIN_SETINGS']['adressSitemap'];
	}

	public function new_sitemap_name(){
		return  $this->data['MAIN_SETINGS']['new_sitemap_name'];
	}

	public function img_expansions(){
		return  $this->data['MAIN_SETINGS']['img_expansions'];
	}

	public function get_img_expansions(){
		return explode(',',  preg_replace('/\s+/','',$this->data['MAIN_SETINGS']['img_expansions']));
	}

	public function url_max_count(){
		return  $this->data['MAIN_SETINGS']['url_max_count'];
	}

	public function img_max_size(){
		return  $this->data['MAIN_SETINGS']['img_max_size'];
	}

	public function img_max_count(){
		return  $this->data['MAIN_SETINGS']['img_max_count'];
	}

	public function html_block(){
		return  $this->data['FILLES_SETINGS']['html_block'];
	}

	public function dirs($path){
		return  $this->data['FILLES_SETINGS']['dirs'][$path];
	}


	public function hide_no_active(){
		return  $this->data['IBLOCK_SETINGS']['hide_no_active'];
	}

	public function iblockElemProps($id, $propId = false){
		if($propId)
		{
			return $this->data['IBLOCK_SETINGS']['iblock'][$id]['props'][$propId];
		}
		else {
			foreach ($this->data['IBLOCK_SETINGS']['iblock'][$id]['props'] as $id => $val){
				if ($val === "Y"){
					$res[] = $id;
				}	
			}
			return $res;
		}
		
	}


	public function iblockElem($id){
		return  $this->data['IBLOCK_SETINGS']['iblock'][$id]['elements'];
	}


	public function iblockSect($id){
		return  $this->data['IBLOCK_SETINGS']['iblock'][$id]['sections'];
	}

	public function iblockSectProps($id, $propId=false){

		if($propId)
		{
			return  $this->data['IBLOCK_SETINGS']['iblock'][$id]['sectProps'][$propId];
		}
		else {
			foreach ($this->data['IBLOCK_SETINGS']['iblock'][$id]['sectProps'] as $id => $val){
				if ($val === "Y"){
					$res[] = $id;
				}	
			}
			return $res;
		}	
	}

	public function getAll(){
		return $this->data;
	}

	/*
	public function getIbIds($type){

		foreach ($this->data['IBLOCK_SETINGS']['iblock'] as $id => $val){
			if ($val[$type] === "Y"){
				$res[] = $id;
			}	
		}
		return $res;
	}
	*/

	public function getIbIds(){

		foreach ($this->data['IBLOCK_SETINGS']['iblock'] as $id => $val){
			if ($val['sections'] === "Y" || $val['elements'] === "Y"){
				$res[] = $id;
			}	
		}
		return $res;
	}

}
?>
<?php
/**
* В версиях >1.0 нет обратной совместимости
* При создании объекта сласса, в него можно передать url для разбора. Если url не передан, то будет использоваться
* $this->url = "http://".$_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
*
* Метод add_query_var ().
* В этот метод можно передавать:
* ассоциативный массив, где ключи - это названия переменных, а значения - значения переменных,
* add_query_var ( array ("название переменной", "значение переменной" ...) )
* или
* два аргумента, первый - название переменной, а второй - её значение.
* add_query_var ( "название переменной", "значение переменной" )
*/
class url{

	public $url = false;								// string URL
	public $url_encoded = false;						// urlencode(string URL)
	
	public $host = false;								// string хост
	public $host_encoded = false;						// urlencode(string хост)
	
	public $path = false;								// string путь (без хоста)
	public $path_encoded = false;						// urlencode(string путь (без хоста))
	
	public $query = false;								// string GET-переменные (foo=1&foo2=2)
	public $query_encoded = false;						// urlencode( string GET-переменные (foo=1&foo2=2) )
	public $query_arr = array();						// ассоциативный массив c переменными, которые учавствуют в формировании URL
	
	public $user = false;								// string имя при GET авторизации  *(если URL передаётся в объект класса)
	public $pass = false;								// string пароль при GET авторизации  *(если URL передаётся в объект класса)
	public $anchor = false;								// string якорь *(если URL передаётся в объект класса)

	public function __construct($url = false) {
		if($url == false){
			$this->url = "http://".$_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			
		}
		else{
			$this->url = &$url;
		}
		
		
		$parse_url = parse_url($this->url);
		
		$this->host = !empty($parse_url["host"]) ? $parse_url["host"] : false;
		$this->host_encoded = !empty($parse_url["host"]) ? urlencode($parse_url["host"]) : false;
		$this->user = !empty($parse_url["user"]) ? $parse_url["user"] : false;
		$this->pass = !empty($parse_url["pass"]) ? $parse_url["pass"] : false;
		$this->path = !empty($parse_url["path"]) ? $parse_url["path"] : false;
		$this->path_encoded = !empty($parse_url["path"]) ? urlencode($parse_url["path"]) : false;
		$this->query = !empty($parse_url["query"]) ? $parse_url["query"] : false;
		$this->anchor = !empty($parse_url["fragment"]) ? $parse_url["fragment"] : false;
		
		$this->url_encoded = urlencode($this->url);

		if( !empty($this->query) ){
			$this->query_arr = $this->make_query_arr($this->query);
			$this->query_encoded = urlencode($this->query);
		}
	}
	
	
	public function __toString() {
        return $this->url;
    }
	
	
	public function make_query_arr($query){
		$query_dbl_arr = explode("&" , $query);
		foreach($query_dbl_arr as $k => $v){
			$query_dbl_tmp = explode("=" , $v);
			$query_arr[$query_dbl_tmp[0]] = $query_dbl_tmp[1];
		}
		return $query_arr;
	}


	public function make_query_str($query_arr, $separator = false){
		if($separator === false){
			return http_build_query($query_arr);
		}
		if($separator !== false){
			return http_build_query($query_arr, '', $separator);
		}
	}


	public function add_query_var (){
		$args = func_get_args();
		if (count($args) == 2){
			$this->query_arr[$args[0]] = $args[1];
		}
		if (count($args) == 1 && is_array($args[0])){
			$this->query_arr = array_merge ($this->query_arr, $args[0]);
		}
		$this->query = $this->make_query_str($this->query_arr);
		$this->query_encoded = urlencode($this->query);
		
		$this->url = $this->unparse_url($this->query);
		$this->url_encoded = urlencode($this->url);
	}
	
	
	public function del_query_var (){
		$args = func_get_args();
		if(count($args) == 1 && is_array($args[0])){
			foreach($args[0] as $k => $v){
				if( array_key_exists( $v, $this->query_arr) ) unset($this->query_arr[$v]);
			}
		}
		if(count($args) >= 0 && is_string($args[0])){
			foreach ($args as $k => $v){
				if(is_string($v)){
					if( array_key_exists( $v, $this->query_arr) ) unset($this->query_arr[$v]);
				}
			}
		}
		$this->query = $this->make_query_str($this->query_arr);
		$this->query_encoded = urlencode($this->query);
		
		$this->url = $this->unparse_url($this->query);
		$this->url_encoded = urlencode($this->url);
	}

	
	public function unparse_url(){
		$url = "http://";
		
		$url .= !empty($this->pass) ? $this->pass : '';
		if(!empty($this->pass) && !empty($this->user)){
			$url .= ":";
		}
		
		$url .= !empty($this->user) ? $this->user : '';
		
		if(!empty($this->pass) || !empty($this->user)){
			$url .= "@";
		}
		
		$url .= !empty($this->host) ? $this->host : '';
		$url .= !empty($this->path) ? $this->path : '';
		$url .= !empty($this->query) ? "?".$this->query : false;
		
		return $url;
	}
}
?>
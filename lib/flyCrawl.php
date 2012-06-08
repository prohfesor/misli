<?php 

class flyCrawler{
        

        var $user_agent = "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0)";
        var $referer    = "";
        var $contentType= "application/x-www-form-urlencoded"; 
        var $protocol   = "HTTP/1.1";
        var $port		= 80;
        var $timeout    = 10;
        var $socket_link= null;
        var $last_url   = "";
        var $last_err   = null;
        var $last_errno = null;
        var $last_response_headers = null;
        var $last_response = null;
        
        
        /**
         * Class constructor
         */
        function flyCrawler(){
        
        }
        
        
        function request($url, $data ='', $method =null){
                $method = (strtoupper($method)=="POST") ? "POST" : "GET";
                
                $this->last_url = $url;
                $url = ("http:" == substr($url,0,5)) ? $url : "http://$url"; 
                $url_host = @parse_url($url, PHP_URL_HOST);
				$url_port = @parse_url($url, PHP_URL_PORT);
				$url_port = (empty($url_port)) ? $this->port : $url_port;
				$url_host = (!empty($url_port)) ? $url_host.":".$url_port  :  $url_host;
                $url_path = @parse_url($url, PHP_URL_PATH);
                $url_query= @parse_url($url, PHP_URL_QUERY);
                $url_path = (empty($url_path)) ? '/' : $url_path;       
                $url_load = (!empty($url_query)) ? $url_path."?".$url_query  : "$url_path";
                
                $aHeaders       = array();
                $aHeaders[]     = "$method $url_load {$this->protocol}";
                $aHeaders[] = "Host: $url_host";
                $aHeaders[] = "Referer: {$this->referer}";
                $aHeaders[] = "User-Agent: {$this->user_agent}";
                $aHeaders[] = "Connection: Close";
                
                if($len = strlen($data)){
                        $aHeaders[] = "Content-Type: {$this->contentType}";
                        $aHeaders[] = "Content-Length: ".$len;
                }       
                
				//var_dump($aHeaders); die();
				//var_dump($data); die();
				
                $header = implode("\r\n", $aHeaders);
                $header .= "\r\n\r\n";
                
                $this->last_response_headers = '';
                $this->last_response = '';
                
                $this->socket_link = null;
                $this->socket_link = @fsockopen($url_host, $this->port, $this->last_errno, $this->last_err, $this->timeout);
                if(!$this->socket_link)
                        return false;
                fputs($this->socket_link, $header.$data."\r\n\r\n" ); 
                
                return true;
        }
        
        
        function load_response(){
                if(!$this->socket_link)
                        return false;
                $response = '';
                while($line = fgets($this->socket_link) )
                        $response .= $line;
                fclose($this->socket_link);     
			    $this->last_response_headers = substr($response, 0 , strpos($response, "\r\n\r\n") );
			    $this->last_response = substr($response, strpos($response, "\r\n\r\n")+4 );
			    return $this->last_response;
        }
        
        
        function request_get($url){
                $this->request($url, '', 'get');        
        }
        
        
        function request_post($url, $data =array()){
                foreach((array)$data as $k=>$v){
                	if(!is_array($v)){
                		$data[$k] = urlencode($k).'='.urlencode($v);
                	} else {
                		unset($data[$k]);
                		foreach($v as $key=>$val){
                			$data["{$k}[{$key}]"] = urlencode("{$k}[{$key}]").'='.urlencode($val);
                		}
                	}
                }
				$data = implode('&',$data);
                $this->request($url, $data, 'post');       
        }
        
        
        function get($url, $data =array()){
        		foreach((array)$data as $k=>$v){
                	if(!is_array($v)){
                		$data[$k] = urlencode($k).'='.urlencode($v);
                	} else {
                		unset($data[$k]);
                		foreach($v as $key=>$val){
                			$data["{$k}[{$key}]"] = urlencode("{$k}[{$key}]").'='.urlencode($val);
                		}
                	}
                }
				$data = implode('&',$data);
                $this->request_get($url."?".$data);
                return $this->load_response();
        }
        
        
        function post($url, $data){
                $this->request_post($url, $data);
                return $this->load_response();
        }

}

?>
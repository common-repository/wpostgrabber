<?php
include(dirname(__FILE__)."/component/simple_html_dom.php");
include(dirname(__FILE__)."/component/Readability.php");

function wpg_test_rss($url) {
	$xmldata = wpg_gethtml($url);
	if($xmldata=='') return 0;	
	
	if( (strpos($xmldata,"<channel>")===FALSE) ) {
		return 0;
	}else{
		return 1;
	}
}

function wpg_get($url)
{	

    $curl = curl_init();    
    $header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,";
    $header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
    $header[] = "Cache-Control: max-age=0";
    $header[] = "Connection: keep-alive";
    $header[] = "Keep-Alive: 300";
    $header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
    $header[] = "Accept-Language: en-us,en;q=0.5";
    $header[] = "Pragma: ";
    // browsers keep this blank.
    $referer = "http://www.google.com";
    
    $btext = rand(0,100000);
	$mozillav = rand(2,9);
	$wow = rand(20,80);
    $browser = "Mozilla/$mozillav.0 (Windows NT $mozillav.2; WOW$wow) AppleWebKit/$btext (KHTML, like Gecko) Chrome/$btext Safari/$btext";
	
	//Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/29.0.1547.22 Safari/537.36
 
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_USERAGENT, $browser);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    curl_setopt($curl, CURLOPT_REFERER, $referer);
    curl_setopt($curl, CURLOPT_AUTOREFERER, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 60);
    curl_setopt($curl, CURLOPT_MAXREDIRS, 14);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

    $data = curl_exec($curl);
 
    curl_close($curl);		
    return $data;
}



function wpg_get_rss($url,$offset,$limit) {		
	
	$xmldata = wpg_gethtml($url);
	if($xmldata=='') return;
	
	if( (strpos($xmldata,"<channel>")===FALSE) ) {
		return;
	}
	
	$xml = new SimpleXMLElement($xmldata);
	$data = array();
	
	foreach($xml->channel->item as $item) {					
		$data[] = array(
			"title"=>$item->title,
			"link"=>$item->link,
			"description"=>strip_tags($item->description)
		);		
	}
	$data = array_slice($data,$offset,$limit);
	if(count($data)==0) return;
	
	return $data;
}


function wpg_get_content($url) {

	$html = wpg_gethtml($url,'text/html');

	$readability = new Readability($html,$url);	
		
	$result = $readability->init();		
	$content = $readability->getContent()->innerHTML;	

	if(strpos(strtolower($content),'Sorry, We was unable')!==FALSE) {
		$htmldom = str_get_html($html);
		$meta_description = $htmldom->find("meta[name=description]",0)->content;
		if($meta_description) {
		$content = $meta_description;
		}
	}

	
	if (function_exists('tidy_parse_string')) {
		$tidy = tidy_parse_string($content, 
			array('indent'=>true, 'show-body-only'=>true), 
			'UTF8');
		$tidy->cleanRepair();
		$content = $tidy->value;
	}
	$title = $readability->getTitle()->textContent;
	
	$tag = strtolower(preg_replace("/[^A-Za-z ]/", "",$title));
	$tag = str_replace(" ",",",$tag);
	
	if(!$title) {
		$api_status = 0;
		$api_message = "Sorry, we can't grab this url, please try again or try another url !";
	}else{
		$api_status = 1;
		$api_message = "success";
	}
			
	return array(
		"api_status"=>$api_status,
		"api_message"=>$api_message,
		"title"=>$title,
		"content"=>$content,
		"html"=>$html,
		"tag"=>$tag
	);
}

function wpg_posthtml($array,$url) {
	 foreach($array as $a => $b) {
		$newarray[] = $a."=".$b;
	 }
	 $newarr = implode('&',$newarray);
	 $Curl_Session = curl_init($url);
	 
	 curl_setopt($Curl_Session, CURLOPT_POST, 1);
	 curl_setopt($Curl_Session, CURLOPT_POSTFIELDS, $newarr);
	 curl_setopt($Curl_Session, CURLOPT_RETURNTRANSFER, true);
	 $result = curl_exec ($Curl_Session);
	 curl_close ($Curl_Session);
	return $result;
} 

function wpg_check_mime($url) {
	$cookie_jar = './cookies.txt';
	$ch = curl_init();	
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch, CURLOPT_NOBODY, 1);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_jar);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_jar);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
	curl_setopt($ch, CURLOPT_MAXREDIRS, 14);
    curl_setopt($ch, CURLOPT_ENCODING,  '');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$content = curl_exec($ch);
	$contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
	curl_close($ch);
	return $contentType;
}
function wpg_gethtml($url,$mime="")
{	
	file_put_contents(WPG_ROOT."/cache/_log.txt","get $url\r\n",FILE_APPEND);

	if($mime) {
		file_put_contents(WPG_ROOT."/cache/_log.txt","check mime $mime $url\r\n",FILE_APPEND);
		
		$content_type = wpg_check_mime($url);
		if(strpos($content_type,$mime)===FALSE) {
			return;
		}
	}		
	$filename = md5($url);
	file_put_contents(WPG_ROOT."/cache/_log.txt","create filename $filename\r\n",FILE_APPEND);
	
	if(file_exists(WPG_ROOT."/cache/$filename.txt")) {
		file_put_contents(WPG_ROOT."/cache/_log.txt","file exists $filename\r\n",FILE_APPEND);
		
		$content = gzuncompress(file_get_contents(WPG_ROOT."/cache/$filename.txt"));
		$content = unserialize($content);
		if($content['url']==$url) {				
			file_put_contents(WPG_ROOT."/cache/_log.txt","url exist $filename\r\n",FILE_APPEND);
			
			if($content['created'] > strtotime('now')) {
				file_put_contents(WPG_ROOT."/cache/_log.txt","time accept $filename\r\n\r\n",FILE_APPEND);
				
				$today = date("Y-m-d H:i:s");
				$created = date("Y-m-d H:i:s",$content['created']);				
				return $content['data'];
			}else{
				file_put_contents(WPG_ROOT."/cache/_log.txt","time expired $filename\r\n",FILE_APPEND);
			}
		}else{
			file_put_contents(WPG_ROOT."/cache/_log.txt","url not found $filename\r\n",FILE_APPEND);
		}
	}else{
		file_put_contents(WPG_ROOT."/cache/_log.txt","file not found $filename\r\n",FILE_APPEND);
	}
	
	file_put_contents(WPG_ROOT."/cache/_log.txt","curl init $filename\r\n",FILE_APPEND);
    $curl = curl_init();    
    $header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,";
    $header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
    $header[] = "Cache-Control: max-age=0";
    $header[] = "Connection: keep-alive";
    $header[] = "Keep-Alive: 300";
    $header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
    $header[] = "Accept-Language: en-us,en;q=0.5";
    $header[] = "Pragma: ";
    // browsers keep this blank.
    $referer = "http://www.google.com";
    
    $btext = rand(0,100000);
	$mozillav = rand(2,9);
	$wow = rand(20,80);
    $browser = "Mozilla/$mozillav.0 (Windows NT $mozillav.2; WOW$wow) AppleWebKit/$btext (KHTML, like Gecko) Chrome/$btext Safari/$btext";
	
	//Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/29.0.1547.22 Safari/537.36
 
    $cookie_jar = './cookies.txt';
    curl_setopt($curl, CURLOPT_URL, $url); 
    curl_setopt($curl, CURLOPT_USERAGENT, $browser);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    curl_setopt($curl, CURLOPT_REFERER, $referer);
    curl_setopt($curl, CURLOPT_AUTOREFERER, true);
    curl_setopt($curl, CURLOPT_COOKIEJAR, $cookie_jar);
    curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie_jar);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);  
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 60);
    curl_setopt($curl, CURLOPT_MAXREDIRS, 14);
    curl_setopt($curl, CURLOPT_ENCODING,  '');
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

    $data = curl_exec($curl);
 
    if ($data === false) {
        $data = curl_error($curl);
		file_put_contents(WPG_ROOT."/cache/_log.txt","curl error $filename : $data\r\n",FILE_APPEND);
    }else{
		$cache = array();
		$cache['url'] = $url;
		$cache['data'] = $data;
		$cache['created'] = strtotime("+2 days");
		$cache = serialize($cache);
		file_put_contents(WPG_ROOT."/cache/$filename.txt",gzcompress($cache));
		$today = date("Y-m-d H:i:s",time());
		$created = date("Y-m-d H:i:s",$content['created']);
		file_put_contents(WPG_ROOT."/cache/_log.txt","new cache created $filename\r\n",FILE_APPEND);
	}
	
	file_put_contents(WPG_ROOT."/cache/_log.txt","curl close $filename\r\n\r\n",FILE_APPEND);
    curl_close($curl);		
    return $data;
}

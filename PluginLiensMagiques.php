<?php
/*
* Plugin Name: PluginLiensMagiques
* Plugin URI: http://www.26am.biz
* Description: Pluggin de generation de lien dans les messages
* Version: 0.5
* Author: Benoit Lamouche
* Author URI: http://www.26am.biz
*/	
function initLinksBL($content){
	$keywords = array();
	//liens internes
	$keywords = addKeywordsLinksBL($keywords,"keyword","","Title for link"); //links on keywords
	$keywords = addKeywordsLinksBL($keywords,"keyword","url","title"); //links on pages
	return addLinksBL($content,$keywords);
}

function addKeywordsLinksBL($keywords,$word,$url,$title = "",$target = ""){
	$line->word = $word;
	//pour wordpress
	$gsite = 'http://www.mywebsite.com/';
	$gtag = 'tag/';
	if($url=='') $url2 = $gsite.$gtag.$word.'/';
	else $url2 = $url;
	$line->url = $url2;
	$line->target = $target;
	$line->title = $title;
	$keywords[] = $line;
	return $keywords;
}

function addLinksBL($content,$keywords){
	$nstr='';
	$str = $content;
	$nb = strlen($str);
	$search = 1;
	$bstart=0;
	$blength=0;
	$used = "";
	for($i=0;$i<$nb;$i++){
		if($search){
			if($str[$i] == '<'){
				if($str[$i+1] == 'a')
					$search = 0;
			}
			elseif($search){
				foreach($keywords as $k){
					$j=0;
					$ressemble = 0;
					$identique = 1;
					if($k->word[$j]==strtolower($str[$i])){
						$ressemble = 1;
						$word = '';
						for($j=0;$j<strlen($k->word);$j++){
							$word .= $str[$i+$j];
							if($k->word[$j]!=strtolower($str[$i+$j])) $identique = 0;
						}
						//if($str[$i+strlen($k->word)]!=" ") $identique = 0;
						//if($str[$i-1]!=" ") $identique = 0;
						if(!preg_match("/[^0-9a-zA-Z]/",$str[$i+strlen($k->word)])) $identique = 0;
						if(!preg_match("/[^0-9a-zA-Z]/",$str[$i-1])) $identique = 0;
					}

					if($identique AND $ressemble AND (stripos($used,"|".$k->word."|")===false)){
						$nstr .= substr($str, $bstart, $blength).'<a href="'.htmlentities($k->url).'" target="'.$k->target.'" title="'.$k->title.'">'.$word.'</a>';
						$bstart = $i+strlen($k->word);
						$blength=0;
						$i = $i+$j;
						$used .= "|".$k->word."|";
					}
				}
			}
		}
		else{
			if($str[$i] == '/'){
				if(($str[$i+1] == 'a') AND ($str[$i+2] == '>'))
					$search = 1;
			}
		}
		if($i+1==$nb){
			$nstr .= substr($str, $bstart, $blength);
		}
		$blength++;
	}
	return $nstr;
}
add_filter('the_content', 'initLinksBL');
//add_action('wp_head', 'initLinksBL');
?>
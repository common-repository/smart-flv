<?php
/*
Plugin Name: Smart Flv
Version: 1.0
Author: SKeeper
Author URI: http://www.dirtydeeds.biz/
Plugin URI: http://www.dirtydeeds.biz/smart-flv-plugin/
Description: The Smart FLV plugin can easily insert FLV videos into WordPress blogs using the extended universal FLV tag style: <strong>[flv:url;image;width;height;link;player]</strong>. Where <strong>url</strong> - URL of the FLV video file you want to embed; <strong>image</strong> - URL of a preview image (shown in display and playlist); <strong>width</strong> - width of an FLV video (optional parameter, default: 400); <strong>height</strong> - height of an FLV video (optional parameter, default: 320); <strong>link</strong> - URL to an external page the display, controlbar and playlist can link to (optional parameter, default: #); <strong>player</strong> - URL to FLV player (optional parameter, default: http://yourblog.com/wp-content/SimpleFLV/flwplayer.swf). Allowed players are: jw3,jw5,flowplayerdark,flowplayer
*/

if (! function_exists ( "get_option" ) || ! function_exists ( "add_filter" )) {
	echo "Smart FLV v.1.0";
	die ();
}

define ( 'SMART_FLV_DEFAULT_WIDTH', '400' );
define ( 'SMART_FLV_DEFAULT_HEIGHT', '320' );
define ( 'SMART_FLV_DEFAULT_PLAYER', get_option ( 'siteurl' ) . "/wp-content/plugins/" . dirname ( plugin_basename ( __FILE__ ) ) . '/flvplayer.swf' );
define ( 'SMART_FLV_DEFAULT_AUTOSTART', 'false' );

function smartFlvInsert($st) {
	
	global $skvariablez;

	@list ( $url, $thumbnail, $width, $height, $link, $player ) = explode ( ";", $st );
	if (! isset ( $width ) || $width == "0") {
		$width = SMART_FLV_DEFAULT_WIDTH;
	}
	if (! isset ( $height ) || $height == "0") {
		$height = SMART_FLV_DEFAULT_HEIGHT;
	}
	if (! isset ( $link )) {
		$linkfromdisplay = 'false';
		$link = "#";
	} else {
		$linkfromdisplay = 'true';
		$link = urlencode ( html_entity_decode ( $link ) );
	}
	if (isset($player))
	{
		if ($player <> 'jw3' and $player <> 'flowplayerdark' and $player <> 'flowplayer' and $player <> 'jw5')
			unset($player);
	}
	$player_id = md5($url.time().rand(100000,999999));

	if (! isset ( $player ) or $player == 'jw3' or $player == 'jw5')
	{
		
		if ($player == 'jw5')
		{
			$player = get_option ( 'siteurl' ) . "/wp-content/plugins/" . dirname ( plugin_basename ( __FILE__ ) ) . '/jwplayer.swf' ;
		}
		else
		{
			$player = SMART_FLV_DEFAULT_PLAYER;
		}
		
		if (!isset($skvariablez[1]))
			$string = '<script type=\'text/javascript\' src=\''.get_option ( 'siteurl' ) . "/wp-content/plugins/" . dirname ( plugin_basename ( __FILE__ ) ) . '/swfobject.js\'></script>';
		
		$skvariablez[1] = 1;

		$string .= '
		
		<div id=\'player_'.$player_id.'\'>Please enable javascript</div>
		
		<script type=\'text/javascript\'>
		var s1 = new SWFObject(\'' . $player . '\',\'player\',\'' . $width . '\',\'' . $height . '\',\'9\'); 
		s1.addParam(\'allowfullscreen\',\'true\'); 
		s1.addParam(\'allowscriptaccess\',\'always\'); 
		s1.addParam(\'wmode\',\'opaque\'); 
		s1.addVariable(\'image\', \'' . $thumbnail . '\'); 
		s1.addVariable(\'file\', \'' . $url . '\'); 
		s1.addVariable(\'autostart\', \'' . SMART_FLV_DEFAULT_AUTOSTART . '\'); 
		s1.addVariable(\'linkfromdisplay\', \'' . $linkfromdisplay . '\'); 
		s1.addVariable(\'link\', \'' . $link . '\'); 
		s1.write(\'player_'.$player_id.'\'); 
		</script> ';

	}
	elseif ($player == 'flowplayerdark')
	{
		$player = get_option ( 'siteurl' ) . "/wp-content/plugins/" . dirname ( plugin_basename ( __FILE__ ) ) . '/FlowPlayerDark.swf' ;

		if (!isset($skvariablez[2]))
			$string = '<script type=\'text/javascript\' src=\''.get_option ( 'siteurl' ) . "/wp-content/plugins/" . dirname ( plugin_basename ( __FILE__ ) ) . '/flashembed.min.js\'></script>';
		
		$skvariablez[2] = 1;

		$string .= '<div id=\'player_'.$player_id.'\'>Please enable javascript</div>


<script type=\'text/javascript\'>
    flashembed(\'player_'.$player_id.'\',
      { src:\'' . $player . '\', wmode: \'transparent\', width: ' . $width . ',  height: ' . $height . ' },
      { config: {  autoPlay: ' . SMART_FLV_DEFAULT_AUTOSTART . ' ,loop: false, autoRewind: false, autoBuffering: true,
			splashImageFile: \'' . $thumbnail . '\', initialScale: \'scale\' 
      		,playList: [ { url: \'' . $url . '\', linkUrl: \'' . $link . '\', linkWindow: \'_blank\' } ]      	                
	    }}
    );
</script>

';

	}
	elseif ($player == 'flowplayer')
	{
		$player = get_option ( 'siteurl' ) . "/wp-content/plugins/" . dirname ( plugin_basename ( __FILE__ ) ) . '/flowplayer-3.2.7.swf' ;

		if (!isset($skvariablez[3]))
			$string = '<script type=\'text/javascript\' src=\''.get_option ( 'siteurl' ) . "/wp-content/plugins/" . dirname ( plugin_basename ( __FILE__ ) ) . '/flowplayer-3.2.6.min.js\'></script>';
		
		$skvariablez[3] = 1;

$string .= '<div id=\'player_'.$player_id.'\' style="height:' . $height . 'px;width:' . $width . 'px;"></div>  
<script language="JavaScript">

flowplayer("player_'.$player_id.'", "'.$player.'",  {
		
	// here is our playlist with two clips
	playlist: [
	
		// this first PNG clip works as a splash image
		{
			url: \''.$thumbnail.'\', 
			scaling: \'scale\'
		},
		
		// second clip is a video. when autoPlay is set to false the splash screen will be shown
		{
			url: \''.$url.'\', 
			autoPlay: false, 
			
			// video will be buffered when splash screen is visible
			autoBuffering: true 
		}
	]
});
</script>
';

	}
	

	return $string;
}

function smartFlvContent($content) {
	$content = preg_replace ( "'\[flv:(.*?)\]'ie", "stripslashes(smartFlvInsert('\\1'))", $content );
	return $content;
}


add_filter ( 'the_content', 'smartFlvContent' );
add_filter ( 'the_excerpt', 'smartFlvContent' );
?>
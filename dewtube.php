<?php
/*
Plugin Name: DewTube Player Video
Plugin URI: http://blog.lagon-bleu.org/wordpress/plugin-wp/
Description:  Insert Dewtube (free video player) in posts & comments.
Author: Jarod_
Version: 1.0.1
Comments: based on the dewtube plugin of Roya Khosravi (http://wordpress.org/extend/plugins/dewtube-flash-mp3-player/)
Updates:
- change the function to find the correct [dewtube] block

To-Doo: 
-none
*/

$dewtube_localversion="1.0";
$wp_dewp_plugin_url = trailingslashit( get_bloginfo('wpurl') ).PLUGINDIR.'/'. dirname( plugin_basename(__FILE__) );
 // Admin Panel   
function dewtube_add_pages()
{
	add_options_page('dewtube options', 'dewtube', 8, __FILE__, 'dewtube_options_page');            
}
// Options Page
function dewtube_options_page()
{ 
	global $dewtube_localversion;
	$status = dewtube_getinfo();	
	$theVersion = $status;
	
			if( (version_compare(strval($theVersion), strval($dewtube_localversion), '>') == 1) )
			{
				$msg = 'Latest version available '.' <strong>'.$theVersion.'</strong><br />';	
				  _e('<div id="message" class="updated fade"><p>' . $msg . '</p></div>');			
			
			}
			

			if (isset($_POST['submitted'])) 
	{	


			$disp_posts = !isset($_POST['disp_posts'])? 'off': 'on';
			update_option('dewtube_posts', $disp_posts);
			$disp_comments = !isset($_POST['disp_comments'])? 'off': 'on';
			update_option('dewtube_comments', $disp_comments);

			$dewwidth= ($_POST['dewwidth']=="")? '512': $_POST['dewwidth'];
			update_option('dewtube_dewwidth', $dewwidth);
			$dewheight= ($_POST['dewheight']=="")? '384': $_POST['dewheight'];
			update_option('dewtube_dewheight', $dewheight);
			$dewstart = !isset($_POST['dewstart'])? '0': '1';
			update_option('dewtube_dewstart', $dewstart);
			
			$msg_status = 'dewtube options saved.';
							
		   _e('<div id="message" class="updated fade"><p>' . $msg_status . '</p></div>');
		
	} 
		// vas me chercher le truc dans la base!
		$disp_link = (get_option('dewtube_link')=='on') ? 'checked':'';		
		$disp_posts = (get_option('dewtube_posts')=='on') ? 'checked' :'' ;
		$disp_comments = (get_option('dewtube_comments')=='on') ? 'checked':'';
		$dewwidth = get_option('dewtube_dewwidth');
		$dewheight = get_option('dewtube_dewheight');
		$dewstart = (get_option('dewtube_dewstart')=='1') ? 'checked':'';

		if ($dewwidth=="") $dewwidth="512";
		if ($dewheight=="") $dewheight="384";
	global $wp_version;	
	global $wp_dewp_plugin_url;
	$actionurl=$_SERVER['REQUEST_URI'];
    // Configuration Page
    echo <<<END
<div class="wrap" style="max-width:950px !important;">
	<h2>dewtube $dewtube_localversion</h2>
				
	<div id="poststuff" style="margin-top:10px;">
	
	<div id="sideblock" style="float:right;width:220px;margin-left:10px;"> 
		 <h3>Related</h3>

<div id="dbx-content" style="text-decoration:none;">
<ul>
<li><a style="text-decoration:none;" href="http://www.alsacreations.fr/dewtube">dewtube</a></li>
</ul><br />
</div>
 	</div>
	
	 <div id="mainblock" style="width:710px">
	 
		<div class="dbx-content">
		 	<form name="rkform" action="$action_url" method="post">
					<input type="hidden" name="submitted" value="1" /> 
						<h3>Usage</h3>                         
<p>dewtube Wordpress plugin allows you to insert dewtube (a free flash video Player, under Creative Commons licence) in posts & comments and lets you play a FLV video.
Just copy dewtube code and paste it into your post or comment.</p>
<p>Usage : <strong>[dewtube:</strong>Path to your video file on local<strong>]</strong></p>
<p><font color="#FF0000">Attention</font> - you can also use a remote video, however, so far, the DewTube could be unable to load the video with Internet Explorer</p>

<p>Examples: <br>
<strong>[dewtube:</strong>/myrep/myvideo.flv<strong>]</strong><br>
</p>

<h3>Options</h3>
<p><strong>dewtube settings</strong></p>

<div><input id="check3" type="checkbox" name="disp_posts" $disp_posts />
<label for="check3">Display dewtube in posts</label></div>

<div><input id="check4" type="checkbox" name="disp_comments" $disp_comments />
<label for="check4">Display dewtube in comments</label></div>

<br><br><strong>dewtube Appearence</strong><br><br>
<div><label for="dewwidth">Width  </label><input id="dewwidth"  name="dewwidth" value="$dewwidth" size="7"/>&nbsp;&nbsp;
<label for="dewheight">Height  </label><input id="dewheight"  name="dewheight" value="$dewheight" size="7"/>&nbsp;&nbsp;
</div>

<div><label for="dewstart">Auto start ? </label><input type="checkbox" id="dewstart" name="dewstart" $dewstart /></div>
<br>
<br>
<div class="submit"><input type="submit" name="Submit" value="Update options" /></div>
			</form>
		</div>
					
		<br/><br/><h3>&nbsp;</h3>	
	 </div>

	</div>
<h5>dewtube plugin by Jarod_ (inspired by the <a href="http://wordpress.org/extend/plugins/dewplayer-flash-mp3-player/">Roya Khosravi's plugin</a>)</h5>
</div>
END;
}
// Add Options Page
add_action('admin_menu', 'dewtube_add_pages');

function dewtube_tag($files) {

	$dewwidth = get_option('dewtube_dewwidth');
	$dewheight = get_option('dewtube_dewheight');
	$dewstart = get_option('dewtube_dewstart');
	$player = trailingslashit( get_bloginfo('wpurl') ).PLUGINDIR.'/'. dirname( plugin_basename(__FILE__) );
	$player .= '/flashplayer/';
	$player .= 'dewtube.swf';
	$width=$dewwidth;
	$height=$dewheight;
	
/*	$add_me_1='';
	$add_me_2='';
	$dewp_tag = '<!-- dewtube Begin-->';
	$dewp_tag .= '<object type="application/x-shockwave-flash" ';
	$dewp_tag .= 'data="'.$player.'?movie='.$files;
	if($dewstart==1) $dewp_tag .= '&amp;autostart='.$dewstart;
	$dewp_tag .= $add_me_1; 
	$dewp_tag .= '" width="'.$width.'" height="'.$height.'">';
	$dewp_tag .= $add_me_2;
	$dewp_tag .= '<param name="movie" value="'.$player.'?movie='.$files;
	if($dewstart==1) $dewp_tag .= '&amp;autostart='.$dewstart;
	$dewp_tag .= '" /></object>';
	$dewp_tag .= '<!-- dewtube End-->';	*/
  
	if($dewstart==1) $files .= '&amp;autostart='.$dewstart;
	$dewp_tag = '<!-- dewtube Begin-->';
	$dewp_tag  = '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" ';
	$dewp_tag .= 'width="'.$width.'" height="'.$height.'" id="dewtube" align="middle"><param name="allowScriptAccess" value="always" /><param name="allowFullScreen" value="true" />';
	$dewp_tag .= '<param name="movie" value="'.$player.'?movie='.$files.'" /><param name="quality" value="high" /><param name="bgcolor" value="#000000" /><embed src="'.$player.'?movie='.$files.'" allowFullScreen="true" quality="high" bgcolor="#000000" ';
	$dewp_tag .= 'width="'.$width.'" height="'.$height.'" name="dewtube" align="middle" allowScriptAccess="always" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer"></embed></object>';
	$dewp_tag .= '<!-- dewtube End-->';	
	return $dewp_tag;
}


function dewtube_check($the_content) {
	if(strpos($the_content, "[dewtube:")!==FALSE) {

		preg_match_all('/\[(?<name>\w+):([^\])]+)/', $the_content, $matches, PREG_SET_ORDER); 
		foreach($matches as $match) { 
		$the_content = preg_replace("/\[(?<name>\w+):([^\])]+)\]/", dewtube_tag($match[2]), $the_content,1);
		}
		
	}
    return $the_content;
}

function dewtube_install(){
  if(get_option('dewtube_posts' == '') || !get_option('dewtube_posts')){
    add_option('dewtube_posts', 'on');
  }
  if(get_option('dewtube_link' == '') || !get_option('dewtube_link')){
    add_option('dewtube_link', 'on');
  } 
  if(get_option('dewtube_dewwidth' == '') || !get_option('dewtube_dewwidth')){
    add_option('dewtube_dewwidth', '512');
  } 
  if(get_option('dewtube_dewheight' == '') || !get_option('dewtube_dewheight')){
    add_option('dewtube_dewheight', '384');
  } 
  if(get_option('dewtube_dewstart' == '') || !get_option('dewtube_dewstart')){
    add_option('dewtube_dewstart', '0');
  } 

//// on peut ajouter d'autres options par defaut
}

if (isset($_GET['activate']) && $_GET['activate'] == 'true') {
    dewtube_install();
}

if (get_option('dewtube_posts')=='on')  {
	add_filter('the_content', 'dewtube_check', 100);
	add_filter('the_excerpt','dewtube_check', 100);
	

}
if (get_option('dewtube_comments')=='on') {
	add_filter('comment_text','dewtube_check', 100);

}

add_action( 'plugins_loaded', 'dewtube_install' );

add_action( 'after_plugin_row', 'dewtube_check_plugin_version' );

function dewtube_getinfo()
{
		$checkfile = "http://blog.lagon-bleu.org/dewtube_plugin_version.txt";
		
		$status=array();
		return $status;
		$vcheck = wp_remote_fopen($checkfile);
				
		if($vcheck)
		{
			$version = $dewtube_localversion;
									
			return $vcheck;
		}					
}

function dewtube_check_plugin_version($plugin)
{
	global $plugindir,$dewtube_localversion;
	
 	if( strpos($plugin,'dewtube.php')!==false )
 	{
			

			$status=dewtube_getinfo();
			
			$theVersion = $status;
	
			if( (version_compare(strval($theVersion), strval($dewtube_localversion), '>') == 1) )
			{
				$msg = 'Latest version available '.' <strong>'.$theVersion.'</strong><br />';				
				echo '<td colspan="5" class="plugin-update" style="line-height:1.2em;">'.$msg.'</td>';
			} else {
				return;
			}
		
	}
}
?>

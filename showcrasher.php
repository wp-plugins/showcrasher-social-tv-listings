<?php

/*
Plugin Name: Showcrasher
Plugin URI: http://www.showcrasher.com/plugins/wordpress
Description: Plugin to embed Showcrasher's Social TV Listings
Date: 03/30/2011
Author: Showcrasher
Author URI: http://showcrasher.com
Version: 1.0

Copyright 2011 Showcrasher
This software is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.

*/

add_filter('the_content','showcrasher_content');

function showcrasher_content($content) {
    $regex = '/\[sc:(.*?)]/i';
	preg_match_all( $regex, $content, $matches );
	for($i=0;$i<count($matches[0]);$i++) {
		$elems = explode("~", $matches[1][$i]);
		$size = "small-vertical" ;
		$show = $elems[0] ;
		if( count($elems) > 1 ) $size = $elems[1] ;
		
		$username = showcrasher_get_username();
		
		$replace = "<script src='http://img.showcrasher.com/d.js' type='text/javascript'></script> <script type='text/javascript'>var showcrasherParams = new showcrasherEmbed({'action' : 'watchlistButton', 'series_name'	: '$show', 'embed_size':'$size', 'sharer_username' : '$username', 'referrer_url' : location.href});</script>";

		$content = str_replace($matches[0][$i], $replace, $content);
	}
	return $content;
}

add_action('admin_menu', 'showcrasher_menu');
add_action( 'admin_notices', 'showcrasher_admin_notices' );

function showcrasher_menu() {
	add_options_page('Showcrasher Embed', 'Showcrasher Embed', 'manage_options', 'showcrasher_settings', 'showcrasher_options');
}

// displays prompt to login on the admin pages if user has not logged into IntenseDebate
function showcrasher_admin_notices() {
	// global administrative settings prompt
	if ( !showcrasher_get_username() && $_GET['page'] != 'showcrasher_settings' ) {
		$settingsurl = get_bloginfo( 'wpurl' ) . '/wp-admin/options-general.php?page=showcrasher_settings';
		?>
		<div class="updated fade-ff0000">
			<p><strong><?php printf( __( 'The Showcrasher plugin is enabled but you need to adjust <a href="%s">your settings</a>.', 'intensedebate' ), $settingsurl ); ?></strong></p>
		</div>
		<?php
		return;
	}
}

function showcrasher_options() {
	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}

	if(isset($_POST['showcrasher_submit']))
	{
		$username = $_POST['showcrasher_username'];
		update_option('showcrasher_username', $username);
		echo '<div class="updated"><p><strong>Showcrasher Settings Updated</strong></p></div>';	 
	}
	
	$current_url = showcrasher_get_full_url();
?>
	<div class="wrap">
		<div style="background:#fff; font-size:11px; padding:15px; margin-top:20px; width:600px; -moz-border-radius: 7px; -webkit-border-radius: 7px;">
			<img src="http://img.showcrasher.com/home_logo_museo.png" height="40">
			<br><br>			
			<strong style="font-size:16px">Enter your Showcrasher username to enable tracking.</strong><br>Each time your readers interact with the tv listings you get instant cred and move up the 'top blog' list for each show you write about. This is optional.<br>
			<form style="background:#eee; padding:10px; width:300px;" action="<? echo $current_url ?>" method="post">
				<p><input type="text" id="username" name="showcrasher_username" value="<? echo showcrasher_get_username(); ?>"/></p>
				<p><input type="submit" name="showcrasher_submit" style="background:#881157; color:#fff" value="Save Username"/></p>
			</form>
			<br><br>
<strong style="font-size:16px">How to use this plugin</strong><BR>
Just write [showcrasher:the show's name] within your blog posts to insert TV Listing information about the next episode of that show. We'll pull in the next NEW episode if there are any & display the faces of friends who will be watching it.<br><BR><i>For example...</i><br>
[showcrasher:American Idol]<br>
[showcrasher:Saturday Night Live]<br><BR>
<strong style="font-size:16px">Sizes</strong><br>
You can use different sizes of the embed units.  If you do not specify a size, it will default to small-vertical.  <i>Sizes:</i> medium, medium-vertical, small-vertical, mini<br><BR>
To specify a size just use a tilde ~ after the show's name and include the size.<br>
<i>For example...</i><br>
[showcrasher:American Idol~mini]<br>
[showcrasher:Saturday Night Live~small-vertical]
<br><BR><BR>
			&raquo;&nbsp;<a href="http://www.showcrasher.com/tools" target="_blank">More embeds, plugins and tools at Showcrasher.com/tools</a>
		</div<		
	</div>
<?

}

function showcrasher_get_username()
{
	return get_option('showcrasher_username', null);
}

function showcrasher_get_full_url()
{
   /*** check for https ***/
   $protocol = $_SERVER['HTTPS'] == 'on' ? 'https' : 'http';
   /*** return the full address ***/
   return $protocol.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
}

?>
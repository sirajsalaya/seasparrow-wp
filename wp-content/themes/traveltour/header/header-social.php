<?php
	/* a template for displaying the header social network */

	$icon_type = traveltour_get_option('general', 'top-bar-social-icon-type', 'font-awesome');
	if( $icon_type == 'font-awesome' ){
		$social_list = array(
			'delicious' => array('title'=> 'Delicious', 'icon'=>'fa fa-delicious'), 
			'email' => array('title'=> 'Email', 'icon'=>'fa fa-envelope'),
			'deviantart' => array('title'=> 'Deviantart', 'icon'=>'fa fa-deviantart'),
			'digg' => array('title'=> 'Digg', 'icon'=>'fa fa-digg'),
			'facebook' => array('title'=> 'Facebook', 'icon'=>'fa fa-facebook'),
			'flickr' => array('title'=> 'Flickr', 'icon'=>'fa fa-flickr'),
			'lastfm' => array('title'=> 'Lastfm', 'icon'=>'fa fa-lastfm'),
			'linkedin' => array('title'=> 'Linkedin', 'icon'=>'fa fa-linkedin'),
			'pinterest' => array('title'=> 'Pinterest', 'icon'=>'fa fa-pinterest-p'),
			'rss' => array('title'=> 'Rss', 'icon'=>'fa fa-rss'), 
			'skype' => array('title'=> 'Skype', 'icon'=>'fa fa-skype'),
			'stumbleupon' => array('title'=> 'Stumbleupon', 'icon'=>'fa fa-stumbleupon'),
			'tumblr' => array('title'=> 'Tumblr', 'icon'=>'fa fa-tumblr'),
			'twitter' => array('title'=> 'Twitter', 'icon'=>'fa fa-twitter'),
			'vimeo' => array('title'=> 'Vimeo', 'icon'=>'fa fa-vimeo'),
			'youtube' => array('title'=> 'Youtube', 'icon'=>'fa fa-youtube'),
			'dribbble' => array('title'=> 'Dribbble', 'icon'=>'fa fa-dribbble'),
			'behance' => array('title'=> 'Behance', 'icon'=>'fa fa-behance'),
			'instagram' => array('title'=> 'Instagram', 'icon'=>'fa fa-instagram'),
			'snapchat' => array('title'=> 'Snapchat', 'icon'=>'fa fa-snapchat-ghost'),
			'twitch' => array('title'=> 'Snapchat', 'icon'=>'fa fa-twitch'),
		);
	}else if( $icon_type == 'font-awesome5' ){
		$social_list = array(
			'tiktok' => array('title'=> 'Tiktok', 'icon'=>'fa5b fa5-tiktok'), 
			'delicious' => array('title'=> 'Delicious', 'icon'=>'fa5b fa5-delicious'), 
			'email' => array('title'=> 'Email', 'icon'=>'fa5s fa5-envelope'),
			'deviantart' => array('title'=> 'Deviantart', 'icon'=>'fa5b fa5-deviantart'),
			'digg' => array('title'=> 'Digg', 'icon'=>'fa5b fa5-digg'),
			'facebook' => array('title'=> 'Facebook', 'icon'=>'fa5b fa5-facebook'),
			'flickr' => array('title'=> 'Flickr', 'icon'=>'fa5b fa5-flickr'),
			'lastfm' => array('title'=> 'Lastfm', 'icon'=>'fa5b fa5-lastfm'),
			'linkedin' => array('title'=> 'Linkedin', 'icon'=>'fa5b fa5-linkedin'),
			'pinterest' => array('title'=> 'Pinterest', 'icon'=>'fa5b fa5-pinterest-p'),
			'rss' => array('title'=> 'Rss', 'icon'=>'fa5s fa5-rss'), 
			'skype' => array('title'=> 'Skype', 'icon'=>'fa5b fa5-skype'),
			'stumbleupon' => array('title'=> 'Stumbleupon', 'icon'=>'fa5b fa5-stumbleupon'),
			'tumblr' => array('title'=> 'Tumblr', 'icon'=>'fa5b fa5-tumblr'),
			'twitter' => array('title'=> 'Twitter', 'icon'=>'fa5b fa5-twitter'),
			'vimeo' => array('title'=> 'Vimeo', 'icon'=>'fa5b fa5-vimeo'),
			'youtube' => array('title'=> 'Youtube', 'icon'=>'fa5b fa5-youtube'),
			'dribbble' => array('title'=> 'Dribbble', 'icon'=>'fa5b fa5-dribbble'),
			'behance' => array('title'=> 'Behance', 'icon'=>'fa5b fa5-behance'),
			'instagram' => array('title'=> 'Instagram', 'icon'=>'fa5b fa5-instagram'),
			'snapchat' => array('title'=> 'Snapchat', 'icon'=>'fa5b fa5-snapchat-ghost'),
			'discord' => array('title'=> 'Discord', 'icon'=>'fa5b fa5-discord'),
			'twitch' => array('title'=> 'Twitch', 'icon'=>'fa5b fa5-twitch'),
		);
	}

	foreach( $social_list as $social_key => $social_icon ){
		$social_link = traveltour_get_option('general', 'top-bar-social-' . $social_key);

		if( $social_key == 'email' && !empty($social_link) ){
			$social_link = 'mailto:' . $social_link;
		}

		if( !empty($social_link) ){
			echo '<a href="' . esc_attr($social_link) . '" target="_blank" class="infinite-top-bar-social-icon" title="' . esc_attr($social_key) . '" >';
			echo '<i class="' . esc_attr($social_icon['icon']) . '" ></i>';
			echo '</a>';
		}
	}
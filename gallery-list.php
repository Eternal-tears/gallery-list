<?php
/*
 Plugin Name: gallery-list
 Plugin URI: http://lovelog.eternal-tears.com/
 Description: ギャラリーサイトを作成する時に便利です。
 Version: 1.0
 Author: Eternal-tears
 Author URI: http://lovelog.eternal-tears.com/
 */

class GalleryListTextDomain {
	
	var $domain = 'gallerylist';
	var $loaded = false;

	function load() {
		if ($this->loaded) {
			return;
		}
		$locale  = get_locale();
		$mofile  = dirname(__FILE__);
		$mofile .= "/{$this->domain}-{$locale}.mo";
		load_textdomain($this->domain, $mofile);
		$this->loaded = true;
	}
}
$gallerylisttextdomain = new GalleryListTextDomain;

/* ==================================================
■サムネイル画像
 ================================================== */
add_image_size( 'single-post-thumbnail', 348, 348); // 個別投稿、個別の固定ページでのサムネイル
add_image_size( 'top-post-thumbnail', 148, 148, true); // 個別投稿、個別の固定ページでのサムネイル
add_image_size( 'thumbnail', 58, 58, true);

/* ==================================================
■サムネイル画像とメイン画像の表示ソース
 ================================================== */
function thumnail_images() {
	global $post;

	$thumnailoutput = '';

	$images = get_posts(array(
		'post_parent' => $post->ID,
						   'post_type' => 'attachment',
						   'post_mime_type' => 'image',
						   'orderby' => 'menu_order',
						   'order' => 'ASC',
						   'numberposts' => 999
					   ));

	foreach ($images as $image) {
		$thumnailimg = wp_get_attachment_metadata($image->ID);
		$mainimg = wp_get_attachment_url($image->ID);
		$imgmimetype1 = str_replace ("image/","",$image->post_mime_type);
		$imgmimetype2 = str_replace ("jpeg","jpg",$imgmimetype1);
		$imgmimetype3 = str_replace ("jpg",".jpg",$imgmimetype2);

		$upload_dir = wp_upload_dir();//http://shop.eternal-tears.jp/wp-content/uploads

		$mainsizew = $thumnailimg["width"];
		$mainsizeh = $thumnailimg["height"];
		$thumnailw = $thumnailimg["sizes"]["thumbnail"]["width"];
		$thumnailh = $thumnailimg["sizes"]["thumbnail"]["height"];

		$thumbnailimgsize = "-" . $thumnailw . "x" . $thumnailh .  "." . $imgmimetype2;
		$mainimgmedium2 = str_replace ($thumbnailimgsize, $imgmimetype3, $thumnailimg["sizes"]["thumbnail"]["file"]);

		$upfiledir = str_replace ($mainimgmedium2, "", $thumnailimg["file"]);

		$mainimgmedium = $upload_dir['baseurl']."/". $upfiledir .$thumnailimg["sizes"]["single-post-thumbnail"]["file"];
		$thumnail_url = $upload_dir['baseurl']."/". $upfiledir .$thumnailimg["sizes"]["thumbnail"]["file"];

//echo '<pre>';
//var_dump($thumbnailimgsize);
//echo '</pre>';

		$thumnailoutput .= '  <li><a';
		$thumnailoutput .= ' href="' . esc_attr($mainimgmedium) . '">';
		$thumnailoutput .= '<img';
		$thumnailoutput .= ' src="' . esc_attr($thumnail_url) . '"';
		$thumnailoutput .= ' width="' . esc_attr($thumnailw) . '"';
		$thumnailoutput .= ' height="' . esc_attr($thumnailh) . '"';
		$thumnailoutput .= ' /></a></li>' . "\n";

	}

	if (!empty($thumnailoutput)) {
		$thumnailoutput = '<ul id="stylelist" class="clfx">' . "\n"
			  . $thumnailoutput
			  . '</ul>' . "\n";
	}
		echo $thumnailoutput;
}




/* ==================================================
■表示したい部分に入れるソース
 ================================================== */

function add_gallery(){
	global $post;
	echo '<div class="itemimg">' . "\n";
	thumnail_images($post->ID);
	echo  '<p class="mainstyle">' . "\n";
		if ( has_post_thumbnail()) {
			$medium_image_url = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'single-post-thumbnail');
			echo get_the_post_thumbnail($post->ID, 'single-post-thumbnail', array('id' => 'target'));
		}
	echo  '</p>' . "\n";
	echo  '</div>' . "\n";

}

/* ==================================================
■ヘッダーにソース
 ================================================== */
function add_gallery_list_js() {
echo '<script type="text/javascript" src="' . plugins_url( "js/gallery-list-head.js" , __FILE__ ) . '"></script>' . "\n";
echo '<link rel="stylesheet" href="' . plugins_url( "css/gallery-list-head.css" , __FILE__ ) . '" type="text/css" media="screen" />' . "\n";
}
add_action('wp_head','add_gallery_list_js');

?>
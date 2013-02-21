<?php
/*
 Plugin Name: gallery-list
 Plugin URI: http://lovelog.eternal-tears.com/
 Description: ギャラリーサイトを作成する時に便利です。
 Version: 1.0
 Author: Eternal-tears
 Author URI: http://lovelog.eternal-tears.com/
 */

load_plugin_textdomain( 'gallerylist', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

/* ==================================================
■サムネイル画像
 ================================================== */
add_image_size( 'gallerylist-large',get_option('gallerylist_largew'), get_option('gallerylist_largeh')); // 個別投稿、個別の固定ページでのサムネイル
add_image_size( 'gallerylist-mediun', get_option('gallerylist_mediumw'), get_option('gallerylist_mediumh'), true); // 個別投稿、個別の固定ページでのサムネイル
add_image_size( 'gallerylist-thumbnail', get_option('gallerylist_miniw'), get_option('gallerylist_minih'), true);

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
		'posts_per_page' => -1
					   ));

	foreach ($images as $image) {
		$thumb_attributess = wp_get_attachment_image_src($image->ID,'gallerylist-thumbnail');
		$thumb_attributesm = wp_get_attachment_image_src($image->ID,'gallerylist-large');
//echo '<pre>';
//var_dump(get_option('gallerylist_miniw'));
//echo '</pre>';

		$thumnailoutput .= '  <li><a';
		$thumnailoutput .= ' href="' . esc_attr($thumb_attributesm[0]) . '">';
		$thumnailoutput .= '<img';
		$thumnailoutput .= ' src="' . esc_attr($thumb_attributess[0]) . '"';
		$thumnailoutput .= ' width="' . esc_attr($thumb_attributess[1]) . '"';
		$thumnailoutput .= ' height="' . esc_attr($thumb_attributess[2]) . '"';
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
			$medium_image_url = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'gallerylist-large');
			echo get_the_post_thumbnail($post->ID, 'gallerylist-large', array('id' => 'target'));
		}
	echo  '</p>' . "\n";
	echo  '</div>' . "\n";

}

/* ==================================================
■ヘッダーにソース
 ================================================== */
function add_gallerylist_js() {
	wp_enqueue_script('jquery');
	wp_enqueue_script('gallery-list-head',plugins_url('js/gallery-list-head.js', __FILE__),array('jquery'),false,false);
}
function add_gallerylist_css(){
	wp_register_style( 'gallery-list-head', plugins_url('css/gallery-list-head.css', __FILE__),'screen' );
	wp_enqueue_style('gallery-list-head');
}
add_action('wp_enqueue_scripts','add_gallerylist_js');
add_action('wp_enqueue_scripts','add_gallerylist_css');

// 「プラグイン」メニューのサブメニュー
function gallerylist_add_admin_menu() {
	add_submenu_page('options-general.php', 'Gallery listの設定', 'Gallery list', manage_options, __FILE__, 'gallerylist_admin_page');
}
add_action('admin_menu', 'gallerylist_add_admin_menu');

function gallerylist_admin_page() {

	$gallerylist_largew = get_option('gallerylist_largew',348);
	$gallerylist_largeh = get_option('gallerylist_largeh',348);
	$gallerylist_mediumw = get_option('gallerylist_mediumw',148);
	$gallerylist_mediumh = get_option('gallerylist_mediumh',148);
	$gallerylist_miniw = get_option('gallerylist_miniw',58);
	$gallerylist_minih = get_option('gallerylist_minih',58);

	// 「変更を保存」ボタンがクリックされたときは、設定を保存する
	if ($_POST['posted'] == 'Y') {

		$gallerylist_largew = stripslashes($_POST['gallerylist_largew']);
		$gallerylist_largeh = stripslashes($_POST['gallerylist_largeh']);
		$gallerylist_mediumw = stripslashes($_POST['gallerylist_mediumw']);
		$gallerylist_mediumh = stripslashes($_POST['gallerylist_mediumh']);
		$gallerylist_miniw = stripslashes($_POST['gallerylist_miniw']);
		$gallerylist_minih = stripslashes($_POST['gallerylist_minih']);

		update_option('gallerylist_largew', $gallerylist_largew);
		update_option('gallerylist_largeh', $gallerylist_largeh);
		update_option('gallerylist_mediumw', $gallerylist_mediumw);
		update_option('gallerylist_mediumh', $gallerylist_mediumh);
		update_option('gallerylist_miniw', $gallerylist_miniw);
		update_option('gallerylist_minih', $gallerylist_minih);
	}
?>

<?php if($_POST['posted'] == 'Y') : ?>
	<div class="updated"><p><strong><?php echo __( '設定を保存しました', 'gallerylist' ); ?></strong></p></div>
<?php endif; ?>
<div class="wrap">
	<h2><?php echo __( 'flexslidergalleryの設定', 'gallerylist' ); ?></h2>
	<form method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
		<input type="hidden" name="posted" value="Y">
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php echo __( 'ギャラリーのメイン画像のサイズ', 'gallerylist' ); ?></th>
				<td>
					<label for="gallerylist_largew">幅</label>
					<input name="gallerylist_largew" type="number" id="gallerylist_largew" value="<?php echo esc_attr($gallerylist_largew); ?>" class="small-text" />
					<label for="gallerylist_largeh">高さ</label>
					<input name="gallerylist_largeh" type="number" id="gallerylist_largeh" value="<?php echo esc_attr($gallerylist_largeh); ?>" class="small-text" /><br />
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php echo __( 'ギャラリーのミディアム画像のサイズ', 'gallerylist' ); ?></th>
				<td>
					<label for="gallerylist_mediumw">幅</label>
					<input name="gallerylist_mediumw" type="number" id="gallerylist_mediumw" value="<?php echo esc_attr($gallerylist_mediumw); ?>" class="small-text" />
					<label for="gallerylist_mediumh">高さ</label>
					<input name="gallerylist_mediumh" type="number" id="gallerylist_mediumh" value="<?php echo esc_attr($gallerylist_mediumh); ?>" class="small-text" /><br />
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php echo __( 'ギャラリーのサムネイル画像のサイズ', 'gallerylist' ); ?></th>
				<td>
					<label for="gallerylist_miniw">幅</label>
					<input name="gallerylist_miniw" type="number" id="gallerylist_miniw" value="<?php echo esc_attr($gallerylist_miniw); ?>" class="small-text" />
					<label for="gallerylist_minih">高さ</label>
					<input name="gallerylist_minih" type="number" id="gallerylist_minih" value="<?php echo esc_attr($gallerylist_minih); ?>" class="small-text" /><br />
				</td>
			</tr>
		</table>

		<p class="submit">
			<input type="submit" name="Submit" class="button-primary" value="<?php echo __( '変更を保存', 'gallerylist' ); ?>" />
		</p>
	</form>
	<h2><?php echo __( 'プラグインの使い方', 'gallerylist' ); ?></h2>
	<p><?php echo __( 'テーマ内の表示したい箇所に下記のソースを表示してください。', 'gallerylist' ); ?><br>
	<?php echo __( '&lt;?php add_gallery(); ?&gt;', 'gallerylist' ); ?>
	</p>

</div>
<?php
}
?>

<?php
/**
 * @package WPostGrabber
 * @version 2.0.4
 */
/*
Plugin Name: WPostGrabber
Plugin URI: http://websprogramming.com/
Description: WPostGrabber, this plugin will help you to get your content on the website that you want instantly. 
Author: Ferry Ariawan
Version: 2.0.4
Author URI: http://websprogramming.com/
*/

define('WPG_ROOT', dirname(__FILE__));
include(dirname(__FILE__)."/wpg_function.php");
include(dirname(__FILE__)."/wpg_settings.php");


register_activation_hook(__FILE__, 'wpg_SetDefaults');
register_uninstall_hook(__FILE__, 'wpg_delete_plugin');
add_action( 'admin_init', 'wpg_init' );
add_action( 'admin_menu', 'wpg_add_page' );

add_action( 'admin_menu', 'wpg_create_form' );
add_action( 'save_post', 'wpg_filter' );
add_filter('intermediate_image_sizes_advanced', 'add_image_insert_override' );

function add_image_insert_override($sizes){
    unset( $sizes['thumbnail']);
    unset( $sizes['medium']);
    unset( $sizes['large']);
    return $sizes;
}


function wpg_new_attachment($att_id){    
    $p = get_post($att_id);
    update_post_meta($p->post_parent,'_thumbnail_id',$att_id);
}

function wpg_filter($post_id) {		
	global $post; 
	global $wpdb;
	$set = get_option("wpg_options");
	
	if (empty( $post )) $post = get_post($post_id);
			
	if ( get_post_status ( $post_id ) != 'publish' ) {
		return;
	}
		
	$current_server = $_SERVER['SERVER_NAME'];
	$content = $post->post_content;	
	$title = $post->post_title;
	
	//file_put_contents(WPG_ROOT."/before.txt",$content);
		
	$domimage = new DOMDocument();	
	@$domimage->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));	
	$last_image = "";
	foreach ($domimage->getElementsByTagName("img") as $element) {		
		$src = $element->getAttribute("src");
		$host = parse_url($src,PHP_URL_HOST);
		if($host!=$current_server) {
			$new_src = media_sideload_image($src,$post_id,$title,'src');			
			$element->setAttribute("src",$new_src);			
		}
		$last_image = $element->getAttribute("src");
	}
	$content = $domimage->saveHTML();
	
	//Update post_content
	if($last_image) {
		$where = array( 'ID' => $post_id );
		$wpdb->update( $wpdb->posts, array( 'post_content' => $content ), $where );	
	}
	//file_put_contents(WPG_ROOT."/after.txt",$content);	
	
	//create thumbnail if dont have
	if($last_image && has_post_thumbnail($post_id)===FALSE) {
		add_action('add_attachment','wpg_new_attachment');
		media_sideload_image($last_image, $post_id, $title);
		remove_action('add_action','wpg_new_attachment');
	}	
	
	//Send Statistic
	$permalink = get_permalink($post_id);
	
}

function wpg_catch_first_image($content) {
	$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $content, $matches, PREG_SET_ORDER);
	$first_img = $matches[0][1];	
	return $first_img;
}
function wpg_create_form() {	
	add_meta_box( 'wpg-url-source', 'WPostGrabber', 'wpg_url_form', 'post', 'normal', 'high' );
}

add_action( 'admin_enqueue_scripts', 'wpg_enqueue' );
function wpg_enqueue($hook) {
	wp_enqueue_style('wpg_style', plugins_url('wpg_style.css?_='.time(), __FILE__));	     
	wp_enqueue_script( 'ajax-script', plugins_url( '/component/wpg_script.js?_='.time(), __FILE__ ), array('jquery') );	
	wp_localize_script( 'ajax-script', 'wpg_object',
	array( 
	'ajax_url' => admin_url( 'admin-ajax.php' )			
	) );
}

add_action( 'wp_ajax_wpg_grab_content', 'wpg_grab_content' );
function wpg_grab_content() {		
	$post = $_POST;
	if($post['url'] && $post['api_key']) {
		$data = wpg_get_content($post['url']);
		header("Content-type: application/json");
		echo json_encode($data);		
	}
	wp_die();
}

add_action( 'wp_ajax_wpg_ajax_test_rss', 'wpg_ajax_test_rss' );
function wpg_ajax_test_rss() {
	$post = $_POST;
	if($post['url']) {
		$data = wpg_test_rss($post['url']);
		if($data) {
			echo "WoW so beautiful, I can do that ! :)";
		}else{
			echo "Oowwhh mann, unfortunately this time i can't do  that :(";
		}
	}
	wp_die();
}

add_action( 'wp_ajax_wpg_load_rss', 'wpg_load_rss' );
function wpg_load_rss() {
	$set = get_option("wpg_options");
	$post = $_POST;
	if($post['url']) {
		$limit = ($post['limit'])?$post['limit']:5;
		$offset = ($post['offset'])?$post['offset']:0;
		$data = wpg_get_rss($post['url'],$offset,$limit);	
		?>
		<table class="wpg_table" width="100%" cellpadding="5">
			<tr class='wpg_table_header'>
				<th width="5%" align="center"><a href="javascript:;" class="button button-primary" onclick="wpg_get_rss('<?php echo $post['url']?>','<?php echo $post['id']?>','0','<?=$limit?>')">Reload</a></th>
				<th><strong>Rss From : <?php echo $post['url']?></strong></th>
				<th width="5%" align="center"><a href="javascript:;" class="button button-primary" onclick="wpg_get_rss('<?php echo $post['url']?>','<?php echo $post['id']?>','<?=$offset+$limit?>','<?=$limit?>')">Next&raquo;</a></th>
			</tr>
			<?php 
			$no=$offset;
			if($data) {
			foreach($data as $r) {
			$no++;
			?>		
			<tr>
				<td colspan="2"><?php echo $no;?>. <a href="javascript:;" onClick="<?php if($set['api_key']!=''):?>wpg_trigger_grab('<?php echo $r['link']?>')<?php endif;?>"><?php echo $r['title']?></a></td>
				<td align="center"><a  class='button button-primary' href='javascript:;' <?php echo ($set['api_key']=='')?"disabled":""?> onClick="<?php if($set['api_key']!=''):?>wpg_trigger_grab('<?php echo $r['link']?>')<?php endif;?>">Grab</a></td>				
			</tr>
			<?php
			}
			}
			if(count($data)==0) {
				echo "<tr><td colspan='3' align='center'>Sorry there is no more data found, please reload...</td></tr>";
			}
			?>
		</table>
		<?php
	}
	wp_die();
}


function wpg_delete_plugin() {
	delete_option('wpg_options');
}

function wpg_init(){		
	register_setting( 'wpg_options', 'wpg_options','wpg_validate' );
}

function wpg_url_form( $object, $box ) { 	
	$set = get_option("wpg_options");
?>
	<div style="padding:10px;background:#8ccbff;box-shadow:4px 4px 0px #48a1ea">
	<table cellspacing='0'>
		<tr>
			<td><input type='text' style='width:100%' id='url_source' /></td>
			<td width='5%'><button class='button button-primary' style='cursor:pointer' id='wpg_grab_button' onClick='wpg_js_grab()' type='button'>Grab Now</button></td>
		</tr>
	</table>
	</div>
	<p class='howto'>Example URL Usually : http://www.example.com/article-name/ , please enter only url with content not url homepage or url rss</p>

	<?php if($set['url_feed_1'] || $set['url_feed_2'] || $set['url_feed_3']):?>
	<p>
		<label>Your RSS Subscribe</label><br/>
		<div id='wpg_rss_1' class='wpg_rss' data-url="<?php echo $set['url_feed_1']?>"></div>
		<div id='wpg_rss_2' class='wpg_rss' data-url="<?php echo $set['url_feed_2']?>"></div>
		<div id='wpg_rss_3' class='wpg_rss' data-url="<?php echo $set['url_feed_3']?>"></div>
	</p>
	<?php endif;?>	

	<p style='font-size:10px;text-align:right'>
		<a href='<?php echo admin_url("options-general.php?page=wpg_settings.php")?>'>Setting</a> | <a href='http://websprogramming.com' title='Hi Thanks For Using WPostGrabber'>Homepage</a>
	</p>
<?php }


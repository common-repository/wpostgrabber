<?php 
#include(dirname(__FILE__)."/wpg_function.php");

function wpg_add_page() {
	add_options_page('WPostGrabber Settings', 'WPostGrabber', 'manage_options', 'wpg_settings.php', 'wpg_settings');
}

function wpg_SetDefaults() {
	$tmp = get_option('wpg_options');
	if($tmp === FALSE) {		
		$arr = array(	"data_disallow_title" => "fuck, sex, porn",
						"data_disallow_content" => "fuck, sex, porn",
						"url_feed_1" => "",
						"url_feed_2" => "",
						"url_feed_3" => ""
		);
		update_option('wpg_options', $arr);
	}
}

function wpg_settings() {	
?>
	<style>
		#wpg_settings {
			border:1px dotted #999999;
			box-shadow:1px 1px 10px #999999;
			padding:20px;
		}
		#wpg_title {
			font-weight:bold;
			border-bottom:3px double #cccccc;
		}
	</style>
	<div class='wrap' id='wpg_settings'>
		<div id='wpg_title' align="center"><h2>WPostGrabber Settings</h2></div>						
		<form method="post" action="options.php">
		<?php 
			settings_fields('wpg_options'); 			
			$set = get_option('wpg_options'); 
		?>				
		
		<table class="form-table">
		
			<tr valign="top">
			<th scope="row">Disallow Word(s) in Title</th>
			<td>
				<textarea rows='4' name='wpg_options[data_disallow_title]' style='width:90%'><?php echo $set['data_disallow_title']?></textarea>			
			</td>
			</tr>
			
			<tr valign="top">
			<th scope="row">Disallow Word(s) in Content</th>
			<td>
				<textarea rows='4' name='wpg_options[data_disallow_content]' style='width:90%'><?php echo $set['data_disallow_content']?></textarea>			
			</td>
			</tr>
			
			<tr><td colspan='2'>
				<div class='wpg_alert'><strong>Note : </strong> Below are setting the rss / feed url address, will serve as the latest news viewer, not taking the news automatically.</div>
			</td></tr>
			
			<tr valign="top">
			<th scope="row">URL Feed 1</th>
			<td>
				<input type='text' id='wpg_url_feed_1' name='wpg_options[url_feed_1]' size='90' placeholder='http://...' value='<?php echo $set['url_feed_1']?>'/>			
				<input type='button' title='Click me to test wheter support or not' onclick='wpg_test_url(this,"wpg_url_feed_1")' class='button button-primary' value='Test'/>
			</td>
			</tr>
			
			<tr valign="top">
			<th scope="row">URL Feed 2</th>
			<td>
				<input type='text' id='wpg_url_feed_2' name='wpg_options[url_feed_2]' size='90' placeholder='http://...' value='<?php echo $set['url_feed_2']?>'/>			
				<input type='button' title='Click me to test wheter support or not' onclick='wpg_test_url(this,"wpg_url_feed_2")' class='button button-primary' value='Test'/>
			</td>
			</tr>
			
			<tr valign="top">
			<th scope="row">URL Feed 3</th>
			<td>
				<input type='text' id='wpg_url_feed_3' name='wpg_options[url_feed_3]' size='90' placeholder='http://...' value='<?php echo $set['url_feed_3']?>'/>			
				<input type='button' title='Click me to test wheter support or not' onclick='wpg_test_url(this,"wpg_url_feed_3")' class='button button-primary' value='Test'/>
			</td>
			</tr>
			
			<tr valign="top">
			<th scope="row">URL Feed 4</th>
			<td>
				<input type='text' id='wpg_url_feed_4' name='wpg_options[url_feed_4]' size='90' placeholder='http://...' value='<?php echo $set['url_feed_4']?>'/>			
				<input type='button' title='Click me to test wheter support or not' onclick='wpg_test_url(this,"wpg_url_feed_4")' class='button button-primary' value='Test'/>
			</td>
			</tr>
			
			<tr valign="top">
			<th scope="row">URL Feed 5</th>
			<td>
				<input type='text' id='wpg_url_feed_3' name='wpg_options[url_feed_5]' size='90' placeholder='http://...' value='<?php echo $set['url_feed_5']?>'/>			
				<input type='button' title='Click me to test wheter support or not' onclick='wpg_test_url(this,"wpg_url_feed_5")' class='button button-primary' value='Test'/>
			</td>
			</tr>
		</table>
		<p>
			<div class='wpg_alert'>Before you save the settings, if you fill rss url please test one by one. To check wheter support or not</div>
		</p>

		<p align='center'>	
			<a class='button' href='<?php echo admin_url("post-new.php")?>'>Lets Create New Post !</a>
			<input type="submit" class="button button-primary" value="<?php _e('Save Changes') ?>" />			
		</p>
		</form>
		
		
		<br/><br/>
		<div align='center' style='font-size:16px;padding:20px;border:1px solid #f2e05c;background:#FFFCE5;box-shadow:4px 4px 0px #d8c531'>
			If you like this Plugin, please LIKE and SHARE <br/>
			<a target='_blank' href='https://www.facebook.com/wpostgrabber'>https://www.facebook.com/wpostgrabber</a><br/><br/>
			-- AND DONT FORGET -- 
			<br/>
			<div align='center'>
			<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
			<input type="hidden" name="cmd" value="_s-xclick">
			<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
			">
			<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
			<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
			</form>
			Thank You ^_^
			</div>			
		</div>
		
		<br/><br/>
		<p>Visit : <a target='_blank' href='http://websprogramming.com'>Our Homepage</a></p>
		
	</div>
<?php 
}

function wpg_validate($input) {
		
		$input['data_disallow_title'] =  wp_filter_nohtml_kses($input['data_disallow_title']);
		$input['data_disallow_content'] =  wp_filter_nohtml_kses($input['data_disallow_content']);
		$input['url_feed_1'] =  wp_filter_nohtml_kses($input['url_feed_1']);
		$input['url_feed_2'] =  wp_filter_nohtml_kses($input['url_feed_2']);
		$input['url_feed_3'] =  wp_filter_nohtml_kses($input['url_feed_3']);
		$input['url_feed_4'] =  wp_filter_nohtml_kses($input['url_feed_4']);
		$input['url_feed_5'] =  wp_filter_nohtml_kses($input['url_feed_5']);			
		
		return $input;
	}
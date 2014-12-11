<?php

// only instantiate the class if it doesn't already exist
if ( !class_exists('ns_sidebar') ) {

	class ns_sidebar {
		
		public static function widget( $widgetname, $args=array() ){
			if( method_exists('ns_sidebar',$widgetname) ){
				?>
				<div class="ns-side-widget ns-<?php echo $widgetname; ?>-widget">
					<div class="ns-side-widget-content">
						<?php call_user_func_array( array('ns_sidebar',$widgetname), $args ); ?>
					</div>
				</div>
				<?php
			}
		}
		
		static function rate( $text='Has this plugin helped you out? Give back with a 5-star rating!', $wp_plugin_slug ){
			?>
			<?php if($text) echo "<p>$text</p>"; ?>
			<p><a href="http://wordpress.org/support/view/plugin-reviews/<?php echo $wp_plugin_slug; ?>?rate=5#postform" target="_blank" class="button">Rate it 5 Stars</a></p>
			<?php
		}
		
		static function links( $plugin_utm_source ){
			?>
			<a href="http://neversettle.it/home/?utm_campaign=in+plugin+referral&utm_source=<?php echo $plugin_utm_source; ?>&utm_medium=plugin&utm_content=social+button+to+ns" target="_blank"><img src="<?php echo plugins_url('ns-visit.png',__FILE__); ?>" alt="Visit NS" /></a>
			<a href="http://facebook.com/neversettle.it" target="_blank"><img src="<?php echo plugins_url('ns-like.png',__FILE__); ?>" alt="Like NS" /></a>
			<a href="https://twitter.com/neversettleit" target="_blank"><img src="<?php echo plugins_url('ns-follow.png',__FILE__); ?>" alt="Follow NS" /></a>
			<?php
		}
		
		static function share( $plugin_url, $plugin_desc, $text='Don\'t be shy, share the love!' ){
			?>
			<?php if($text) echo "<p>$text</p>"; ?>
			<p>
				<a class="facebook" href="http://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($plugin_url); ?>&amp;t=<?php echo urlencode($plugin_desc); ?>" target="_blank">
					<img src="<?php echo plugins_url('share-facebook.png',__FILE__); ?>" alt="Share on Facebook" />
				</a>
				<a class="twitter" href="http://twitter.com/share?url=<?php echo urlencode($plugin_url); ?>&amp;text=<?php echo urlencode($plugin_desc); ?>&amp;via=neversettle" target="_blank">
					<img src="<?php echo plugins_url('share-twitter.png',__FILE__); ?>" alt="Share on Twitter" />
				</a>
				<a class="google" href="http://plus.google.com/share?url=<?php echo urlencode($plugin_url); ?>" target="_blank">
					<img src="<?php echo plugins_url('share-googleplus.png',__FILE__); ?>" alt="Share on Google+" />
				</a>
			</p>
			<?php
		}
		
		static function subscribe( $mc_u='a979a91d50433ca0485c903ee', $mc_id='15ee335def', $text='Get New Plugins, Updates, and Dev Tips!' ){
			?>
			<?php if($text) echo "<p>$text</p>"; ?>
			<!-- Begin MailChimp Signup Form -->
			<!--<link href="//cdn-images.mailchimp.com/embedcode/classic-081711.css" rel="stylesheet" type="text/css"></style>-->
			<div id="mc_embed_signup">
			<form action="//NeverSettle.us8.list-manage.com/subscribe/post?u=<?php echo $mc_u; ?>&amp;id=<?php echo $mc_id; ?>" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
			<div class="mc-field-group">
			<label for="mce-EMAIL">Email Address </label>
			<input type="email" value="" name="EMAIL" class="required email" id="mce-EMAIL" placeholder="Email Address" style="padding:6px 12px;width:100%;">
			<input type="checkbox" id="group_1" name="group[8737][1]" value="1" checked="checked" style="display: none;">
			</div>
			<div id="mce-responses" class="clear">
			<div class="response" id="mce-error-response" style="display:none"></div>
			<div class="response" id="mce-success-response" style="display:none"></div>
			</div>    <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
			   <div style="position: absolute; left: -5000px;"><input type="text" name="b_<?php echo $mc_u; ?>_<?php echo $mc_id; ?>" tabindex="-1" value=""></div>
			   <div class="clear"><input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="button-secondary" style="margin-top:6px;width:100%;"></div>
			</form>
			</div>
			<script type='text/javascript' src='//s3.amazonaws.com/downloads.mailchimp.com/js/mc-validate.js'></script>
			<script type='text/javascript'>
			(function($) {
			window.fnames = new Array(); window.ftypes = new Array();fnames[0]='EMAIL';ftypes[0]='email';fnames[1]='FNAME';ftypes[1]='text';fnames[2]='LNAME';ftypes[2]='text';fnames[7]='MMERGE7';ftypes[7]='text';fnames[10]='MMERGE10';ftypes[10]='number';fnames[11]='MMERGE11';ftypes[11]='number';fnames[12]='MMERGE12';ftypes[12]='text';fnames[13]='MMERGE13';ftypes[13]='text';fnames[14]='MMERGE14';ftypes[14]='text';fnames[15]='MMERGE15';ftypes[15]='text';fnames[16]='MMERGE16';ftypes[16]='text';fnames[17]='MMERGE17';ftypes[17]='text';fnames[18]='MMERGE18';ftypes[18]='text';
			}(jQuery));
			var $mcj = jQuery.noConflict(true);
			</script>
			<!--End mc_embed_signup-->
			<?php
		}
	
		static function support( $text='Need any help with this plugin, or have ideas to make it better? We\'d love to hear from you.' ){
			?>
			<?php if($text) echo "<p>$text</p>"; ?>
			<p><a href="http://support.neversettle.it" class="button" target="_blank" style="width:100%;text-align:center;"><?php _e( 'Support & Feature Requests', 'ns-cloner' ); ?></a></p>
			<?php
		}
	
		static function featured(){
			$feed = @fetch_feed( 'http://neversettle.it/plugin-widget-status/featured/feed/' );
			if( !is_array($feed->get_items()) || sizeof($feed->get_items())<1 ) return;
			$items = $feed->get_items();
			$featured = array_shift($items);
			define('NS_SIDEBAR_FEATURED_LINK', $featured->get_link());
			$thumbnail_el = $featured->get_item_tags('http://neversettle.it/','thumbnail');
			?>
			<a href="<?php echo $featured->get_link(); ?>" target="_blank">
				<img style="max-width:100%" src="<?php echo $thumbnail_el[0]['data']; ?>" />
			</a>
			<?php
		}
		
		static function random( $exclude_links=array() ){
			if( defined('NS_SIDEBAR_FEATURED_LINK') ){
				$exclude_links[] = NS_SIDEBAR_FEATURED_LINK;
			}
			$feed = @fetch_feed( 'http://neversettle.it/feed/?post_type=product' );
			if( !is_array($feed->get_items()) || sizeof($feed->get_items())<1 ) return;
			$items = $feed->get_items();
			$other_items = array_filter( $items, create_function('$i','return false===strpos("'.join(' ',$exclude_links).'",$i->get_link());') );
			$random = $other_items[ array_rand($other_items) ];
			$thumbnail_el = $random->get_item_tags('http://neversettle.it/','thumbnail');
			?>
			<a href="<?php echo $random->get_link(); ?>" target="_blank">
				<img style="max-width:100%" src="<?php echo $thumbnail_el[0]['data']; ?>" />
			</a>
			<?php
		}

		static function donate($text='Help us provide support and updates') {
			?>
			<div style="text-align: center;">
				<?php if($text) echo "<p>$text</p>"; ?>
				<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
					<input type="hidden" name="cmd" value="_s-xclick">
					<input type="hidden" name="hosted_button_id" value="YCMV435HB28JG">
					<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
					<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
				</form>
			</div>
			<?php
		}
	}
}
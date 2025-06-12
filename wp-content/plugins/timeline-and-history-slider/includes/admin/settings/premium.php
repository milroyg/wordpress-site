<?php
/**
 * Plugin Premium Offer Page
 *
 * @package Timeline and History Slider
 * @since 1.0.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="wrap">
	
	<style>
		/* Table CSS */
		table, th, td {border: 1px solid #d1d1d1;}
		table.wpos-plugin-list{width:100%; text-align: left; border-spacing: 0; border-collapse: collapse; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box; margin-bottom: 50px;}
		.wpos-plugin-list th {width: 16%; background: #2271b1; color: #fff; }
		.wpos-plugin-list td {vertical-align: top;}
		.wpos-plugin-type { text-align: left; color: #fff; font-weight: 700; padding: 0 10px; margin: 15px 0; }
		.wpos-slider-list { font-size: 14px; font-weight: 500; padding: 0 10px 0 25px; }
		.wpos-slider-list li {text-align: left; font-size: 13px; list-style: disc;}

		.wpostahs-sf-blue{color:#6c63ff; font-weight:bold;}
		.wpostahs-sf-btn{display: inline-block; font-size: 18px; padding: 10px 25px; border-radius: 100px;  background-color: #46b450; border-color: #46b450; color: #fff !important; font-weight: 600; text-decoration: none;}
		.wpostahs-sf-btn-orange{ background-color: #FF1000; border-color: #FF1000 ;}
		.wpostahs-sf-btn:hover,
		.wpostahs-sf-btn:focus{background-color: #3fa548; border-color: #3fa548;}
		.wpostahs-sf-btn-orange:hover,
		.wpostahs-sf-btn-orange:focus {background-color: #D01003 ; border-color: #D01003 ;}

		.wpostahs-favourite-section{text-align: center; margin-bottom:30px}
		.wpostahs-favourite-heading{margin:0; margin-bottom:10px; font-size:24px; font-weight:bold;}
		.wpostahs-favourite-sub-heading{font-size: 28px !important; font-weight: 700 !important; letter-spacing: -1px !important; text-align: center; padding:0 !important; margin-bottom: 5px !important;}
		.wpostahs-favourite-section span{font-size: 16px;color: #000;display: inline-block;width: 100%;}
		.wpostahs-favourite-section span i{color: #50c621; font-weight: 600; vertical-align: middle;}
		.wpostahs-favourite-section span img{display: inline-block; vertical-align: middle; max-width: 100%; height: auto;}

		/* welcome-screen-css start -M */
		.wpostahs-sf-btn{display: inline-block; font-size: 18px; padding: 10px 25px; border-radius: 100px;  background-color: #ff5d52; border-color: #ff5d52; color: #fff !important; font-weight: 600; text-decoration: none;}
		.wpostahs-sf-btn:hover,
		.wpostahs-sf-btn:focus{background-color: #ff5d52; border-color: #ff5d52;}
		.wpostahs-inner-Bonus-class{background: #46b450;
		  border-radius: 20px;
		  font-weight: 700;
		  padding: 5px 10px;
		  color: #fff;
		    line-height: 1;
		  font-size: 12px;}

		.wpostahs-black-friday-feature{padding: 30px 40px;
		  background: #fafafa;
		  border-radius: 20px 20px 0 0;
		  gap: 60px;
		  align-items: center;
		  flex-direction: row;
		  display: flex;}
		.wpostahs-black-friday-feature .wpostahs-inner-deal-class{flex-direction: column;
		  gap: 15px;
		  display: flex;
		  align-items: flex-start;}
		.wpostahs-black-friday-feature ul li{text-align: left;}
		.wpostahs-black-friday-feature .wpostahs-inner-list-class {
		  display: grid;
		  grid-template-columns: repeat(4,1fr);
		  gap: 10px;
		}
		.wpostahs-black-friday-feature .wpostahs-list-img-class {
		  min-height: 95px;
		  display: flex;
		  align-items: center;
		  background: #fff;
		  border-radius: 20px;
		  flex-direction: column;
		  gap: 10px;
		  justify-content: center;
		  padding: 10px;color: #000;
		  font-size: 12px;
		}
		.wpostahs-black-friday-banner-wrp .wpostahs-list-img-class img {
		  width: 100%;
		  flex: 0 0 40px;
		  font-size: 20px;
		  height: 40px;
		  width: 40px;
		  box-shadow: inset 0px 0px 15px 2px #c4f2ac;
		  border-radius: 14px;
		  display: flex;
		  justify-content: center;
		  align-items: center;
		  padding: 10px;
		}

		.wpostahs-main-feature-item{background: #fafafa;
		  padding: 20px 15px 40px;
		  border-radius: 0 0 20px 20px;margin-bottom: 40px;}
		.wpostahs-inner-feature-item{display: flex;
		  gap: 30px;
		  padding: 0 15px;}
		.wpostahs-list-feature-item {
		  border: 1px solid #ddd;
		  padding: 10px 15px;
		  border-radius: 8px;text-align: left;
		}
		.wpostahs-list-feature-item img {
		  width: 36px !important;
		  padding: 5px;
		  border: 1px solid #ccc;
		  border-radius: 50%;margin-bottom: 5px;
		}
		.wpostahs-list-feature-item h5{margin: 0;
		  font-weight: bold;font-size: 16px;
		  text-decoration: underline;
		  text-underline-position: under;
		  color: #000;}
		.wpostahs-list-feature-item p {
		  color: #505050;
		  font-size: 12px;
		  margin-bottom: 0;
		}

		/* welcome-screen-css end -M */

	</style>

	<!-- <div class="wpostahs-black-friday-banner-wrp">
		<a href="<?php // echo esc_url( WPOSTAHS_PLUGIN_LINK_UPGRADE ); ?>" target="_blank"><img style="width: 100%;" src="<?php // echo esc_url( WPOSTAHS_URL ); ?>assets/images/black-friday-banner.png" alt="black-friday-banner" /></a>
	</div> -->

	<div class="wpostahs-black-friday-banner-wrp" style="background:#e1ecc8;padding: 20px 20px 40px; border-radius:5px; text-align:center;margin-bottom: 40px;">
		<h2 style="font-size:30px; margin-bottom:10px;"><span style="color:#0055fb;">Timeline and History slider</span> is included in <span style="color:#0055fb;">Essential Plugin Bundle</span> </h2> 
		<h4 style="font-size: 18px;margin-top: 0px;color: #ff5d52;margin-bottom: 24px;">Now get Designs, Optimization, Security, Backup, Migration Solutions @ one stop. </h4>

		<div class="wpostahs-black-friday-feature">

			<div class="wpostahs-inner-deal-class" style="width:40%;">
				<div class="wpostahs-inner-Bonus-class">Bonus</div>
				<div class="wpostahs-image-logo" style="font-weight: bold;font-size: 26px;color: #222;"><img style="width: 34px; height:34px;vertical-align: middle;margin-right: 5px;" class="wpostahs-img-logo" src="<?php echo esc_url( WPOSTAHS_URL ); ?>assets/images/essential-logo-small.png" alt="essential-logo" /><span class="wpostahs-esstial-name" style="color:#0055fb;">Essential </span>Plugin</div>
				<div class="wpostahs-sub-heading" style="font-size: 16px;text-align: left;font-weight: bold;color: #222;margin-bottom: 10px;">Includes All premium plugins at no extra cost.</div>
				<a class="wpostahs-sf-btn" href="<?php echo esc_url( WPOSTAHS_PLUGIN_LINK_UPGRADE ); ?>" target="_blank">Grab The Deal</a>
			</div>

			<div class="wpostahs-main-list-class" style="width:60%;">
				<div class="wpostahs-inner-list-class">
					<div class="wpostahs-list-img-class"><img src="<?php echo esc_url( WPOSTAHS_URL ); ?>assets/images/logo-image/img-slider.png" alt="essential-logo" /> Image Slider</li></div>

					<div class="wpostahs-list-img-class"><img src="<?php echo esc_url( WPOSTAHS_URL ); ?>assets/images/logo-image/advertising.png" alt="essential-logo" /> Publication</li></div>

					<div class="wpostahs-list-img-class"><img src="<?php echo esc_url( WPOSTAHS_URL ); ?>assets/images/logo-image/marketing.png" alt="essential-logo" /> Marketing</li></div>

					<div class="wpostahs-list-img-class"><img src="<?php echo esc_url( WPOSTAHS_URL ); ?>assets/images/logo-image/photo-album.png" alt="essential-logo" /> Photo album</li></div>

					<div class="wpostahs-list-img-class"><img src="<?php echo esc_url( WPOSTAHS_URL ); ?>assets/images/logo-image/showcase.png" alt="essential-logo" /> Showcase</li></div>

					<div class="wpostahs-list-img-class"><img src="<?php echo esc_url( WPOSTAHS_URL ); ?>assets/images/logo-image/shopping-bag.png" alt="essential-logo" /> WooCommerce</li></div>

					<div class="wpostahs-list-img-class"><img src="<?php echo esc_url( WPOSTAHS_URL ); ?>assets/images/logo-image/performance.png" alt="essential-logo" /> Performance</li></div>

					<div class="wpostahs-list-img-class"><img src="<?php echo esc_url( WPOSTAHS_URL ); ?>assets/images/logo-image/security.png" alt="essential-logo" /> Security</li></div>

					<div class="wpostahs-list-img-class"><img src="<?php echo esc_url( WPOSTAHS_URL ); ?>assets/images/logo-image/forms.png" alt="essential-logo" /> Pro Forms</li></div>

					<div class="wpostahs-list-img-class"><img src="<?php echo esc_url( WPOSTAHS_URL ); ?>assets/images/logo-image/seo.png" alt="essential-logo" /> SEO</li></div>

					<div class="wpostahs-list-img-class"><img src="<?php echo esc_url( WPOSTAHS_URL ); ?>assets/images/logo-image/backup.png" alt="essential-logo" /> Backups</li></div>

					<div class="wpostahs-list-img-class"><img src="<?php echo esc_url( WPOSTAHS_URL ); ?>assets/images/logo-image/White-labeling.png" alt="essential-logo" /> Migration</li></div>
				</div>
			</div>
		</div>
		<div class="wpostahs-main-feature-item">
			<div class="wpostahs-inner-feature-item">
				<div class="wpostahs-list-feature-item">
					<img src="<?php echo esc_url( WPOSTAHS_URL ); ?>assets/images/logo-image/layers.png" alt="layer" />
					<h5>Site management</h5>
					<p>Manage, update, secure & optimize unlimited sites.</p>
				</div>
				<div class="wpostahs-list-feature-item">
					<img src="<?php echo esc_url( WPOSTAHS_URL ); ?>assets/images/logo-image/risk.png" alt="backup" />
					<h5>Backup storage</h5>
					<p>Secure sites with auto backups and easy restore.</p>
				</div>
				<div class="wpostahs-list-feature-item">
					<img src="<?php echo esc_url( WPOSTAHS_URL ); ?>assets/images/logo-image/support.png" alt="support" />
					<h5>Support</h5>
					<p>Get answers on everything WordPress at anytime.</p>
				</div>
			</div>
		</div>
		<a class="wpostahs-sf-btn" href="<?php echo esc_url( WPOSTAHS_PLUGIN_LINK_UPGRADE ); ?>" target="_blank">Grab The Deal</a>
	</div>

	<h2 style="font-size: 24px; text-align: center; margin-bottom:25px;"><span class="wpostahs-sf-blue">Timeline and History slider </span>Including in <span class="wpostahs-sf-blue">Essential Plugin Bundle</span></h2>

	<div id="poststuff">
		<div id="post-body" class="metabox-holder">
			<div id="post-body-content">
					<table class="wpos-plugin-list">
						<thead>
							<tr>
								<th><h3 class="wpos-plugin-type">Image Slider</h3></th>
								<th><h3 class="wpos-plugin-type">Marketing</h3></th>
								<th><h3 class="wpos-plugin-type">Photo Album</h3></th>
								<th><h3 class="wpos-plugin-type">Publication</h3></th>
								<th><h3 class="wpos-plugin-type">Showcase</h3></th>
								<th><h3 class="wpos-plugin-type">WooCommerce</h3></th>
							</tr>
							<tr>
								<td>
									<ul class="wpos-slider-list">
										<li>Accordion and Accordion Slider</li>
										<li>WP Slick Slider and Image Carousel</li>
										<li>WP Responsive Recent Post Slider/Carousel</li>
										<li>WP Logo Showcase Responsive Slider and Carousel</li>
										<li>WP Featured Content and Slider</li>
										<li>Trending/Popular Post Slider and Widget</li>
										<li><span style="color:#2271b1; font-weight: bold;">Timeline and History slider</span></li>
										<li>Meta slider and carousel with lightbox</li>
										<li>Post Category Image With Grid and Slider</li>
									</ul>
								</td>
								<td>
									<ul class="wpos-slider-list">
										<li>Popup Anything - A Marketing Popup and Lead Generation Conversions</li>
										<li>Countdown Timer Ultimate</li>
									</ul>
								</td>
								<td>
									<ul class="wpos-slider-list">
										<li>Album and Image Gallery plus Lightbox</li>
										<li>Portfolio and Projects</li>
										<li>Video gallery and Player</li>
									</ul>
								</td>
								<td>
									<ul class="wpos-slider-list">
										<li>WP Responsive Recent Post Slider/Carousel</li>
										<li>WP News and Scrolling Widgets</li>
										<li>WP Blog and Widget</li>
										<li>Blog Designer - Post and Widget</li>
										<li>Trending/Popular Post Slider and Widget</li>
										<li>WP Featured Content and Slider</li>
										<li><span style="color:#2271b1; font-weight: bold;">Timeline and History slider</span></li>
										<li>Testimonial Grid and Testimonial Slider plus Carousel with Rotator Widget</li>
										<li>Post Ticker Ultimate</li>
										<li>Post grid and filter ultimate</li>
									</ul>
								</td>
								<td>
									<ul class="wpos-slider-list">
										<li>Testimonial Grid and Testimonial Slider plus Carousel with Rotator Widget</li>
										<li>Team Slider and Team Grid Showcase plus Team Carousel</li>
										<li>Hero Banner Ultimate</li>
										<li>WP Logo Showcase Responsive Slider and Carousel</li>
									</ul>
								</td>
								<td>
									<ul class="wpos-slider-list">
										<li>Product Slider and Carousel with Category for WooCommerce</li>
										<li>Product Categories Designs for WooCommerce</li>
										<li>Popup Anything - A Marketing Popup and Lead Generation Conversions</li>
										<li>Countdown Timer Ultimate</li>
									</ul>
								</td>
							</tr>
						</thead>
					</table>

					<div class="wpostahs-favourite-section">
						<h3 class="wpostahs-sf-blue  wpostahs-favourite-heading">Use Essential Plugin Bundle</h3>
						<h1 class="wpostahs-favourite-sub-heading">With Your Favourite Page Builders</h1>
						<span><i class="dashicons dashicons-yes"></i> = <img src="<?php echo esc_url( WPOSTAHS_URL ); ?>assets/images/essential-logo-small.png" width="15" height="15" /> Essential Plugin Bundle contain many more layouts and designs</span>
					</div>

					<div style="text-align: center;">
						<img style="width: 100%; margin-bottom:30px;" src="<?php echo esc_url( WPOSTAHS_URL ); ?>assets/images/image-upgrade.png" alt="image-upgrade" title="image-upgrade" />
						<div style="font-size: 14px; margin-bottom:10px;"><span class="wpostahs-sf-blue">Timeline Slider </span>Including in <span class="wpostahs-sf-blue">Essential Plugin Bundle</span></div>
						<a href="<?php echo esc_url(WPOSTAHS_PLUGIN_LINK_UPGRADE); ?>" target="_blank" class="wpostahs-sf-btn wpostahs-sf-btn-orange"><span class="dashicons dashicons-cart"></span> Grab The Deal</a>
					</div>
			</div>
		</div>
	</div>
</div>
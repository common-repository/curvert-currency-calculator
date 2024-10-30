<?php
/*
Plugin Name: Curvert - Currency calculator
Description: Umrechner für Welt- und Kryptowährungen
Version: 1.0
Author: BigClick GmbH & Co.KG
Author URI: https://curvert.com
*/


class CurvertConverter extends WP_Widget {
 
	function __construct(){
		parent::__construct(
			// Base ID of your widget
			'curvert_widget',

			// Widget name will appear in UI
			__('Curvert - Currency Converter', 'curvert_widget_lng'),

			// Description
			array(
				'description' => __('Currency calculator', 'curvert_widget_lng'),
			)
		);
	}

	// FrontEnd
	public function widget( $args, $instance ) {

		wp_enqueue_style( "curvert_style", plugins_url( 'css/frontend.css', __FILE__) );

		$_js_file_path = plugins_url( 'js/curvert-frontend-script.js', __FILE__);
		$my_js_ver  = date('ymd-Gis', filemtime($_js_file_path));
		wp_enqueue_script('curvert_script', $_js_file_path, [], $my_js_ver);

		// Settings for FrontEnd
		$title = apply_filters('widget_title', $instance['title']);
		$bpadding = (int)$instance['bpadding'];
		$bordercolor = $instance['bordercolor'];
		$borderstyle = $instance['borderstyle'];
		$borderthicknes = (int)$instance['borderthicknes'];
		$bgcolor = $instance['bgcolor'];
		$textcolor = $instance['textcolor'];

		require_once dirname(__FILE__).'/inc/currencies.php';

		echo $args['before_widget'];

		?>
			<?php if(!empty($title)){ echo $args['before_title']; ?><a href="https://curvert.com"><?php echo $title ?></a><?php echo $args['after_title']; } ?>
			<div class="curvert-converter-wrapper">
				<div class="input-wrapper">
					<input type="number" id="curvert-amount" placeholder="<?php echo __('Insert Amount', 'curvert_widget_lng') ?>" value="1">
				</div>
				<div class="input-wrapper">
					<select id="curvert-from-currency">
						<?php
							foreach($_available_currencies as $_crc){
								echo '<option data-type="'.$_crc[2].'" value="'.$_crc[0].'"'.($_crc[0] == 'BTC' ? 'selected' : '').'>'.$_crc[1].' ('.$_crc[0].')</option>';
							}
						?>
					</select>
				</div>
				<div class="text-center">
					<span id="curvert-swap-currencies">
						<svg fill="#000000" height="28" viewBox="0 0 28 28" width="28" xmlns="http://www.w3.org/2000/svg">
							<path d="M16 17.01V10h-2v7.01h-3L15 21l4-3.99h-3zM9 3L5 6.99h3V14h2V6.99h3L9 3z"/>
							<path d="M0 0h28v28H0z" fill="none"/>
						</svg>
					</span>
				</div>
				<div class="input-wrapper">
					<select id="curvert-to-currency">
						<?php
							foreach($_available_currencies as $_crc){
								echo '<option data-type="'.$_crc[2].'" value="'.$_crc[0].'"'.($_crc[0] == 'EUR' ? 'selected' : '').'>'.$_crc[1].' ('.$_crc[0].')</option>';
							}
						?>
					</select>
				</div>
				<div id="curvert-result-single">
					&nbsp;
				</div>
				<div id="curvert-result">
					&nbsp;
				</div>
				<?php if(empty($title)){ ?>
					<p style="text-align:right"><a href="https://www.curvert.com"><small><?php echo __('Currency calculator', 'curvert_widget_lng') ?></small></a></p>
				<?php } ?>
			</div>

			<style type="text/css">
				.curvert-converter-wrapper {
					<?php if($bpadding > 0){ ?>padding: <?php echo $bpadding ?>px;<?php } ?>
					background-color: <?php echo $bgcolor ?>;
					<?php if($borderthicknes > 0){ ?>
						border: <?php echo $borderthicknes ?>px <?php echo $borderstyle ?> <?php echo $bordercolor ?>;
					<?php } ?>
					color: <?php echo $textcolor ?>
				}

			</style>
		<?php
		echo $args['after_widget'];
	}


	// BackEnd
	public function form($instance){

		wp_enqueue_style( "curvert_style", plugins_url( 'css/admin.css', __FILE__) );
		$_js_file_path = plugins_url( 'js/curvert-admin-script.js', __FILE__);
		$my_js_ver  = date('ymd-Gis', filemtime($_js_file_path));
		wp_enqueue_script('curvert_script', $_js_file_path, [], $my_js_ver);

		// Standardwerte setzen
		$defaults = array(
			'title' => __('Curvert - Currency Converter', 'curvert_widget_lng'),
			'bpadding' => __('10', 'curvert_widget_lng'),
			'bordercolor' => __('#cccccc', 'curvert_widget_lng'),
			'borderstyle' => __('solid', 'curvert_widget_lng'),
			'borderthicknes' => __('1', 'curvert_widget_lng'),
			'bgcolor' => __('#f9f9f9', 'curvert_widget_lng'),
			'textcolor' => __('#333333', 'curvert_widget_lng'),
		);

		$instance = wp_parse_args((array)$instance, $defaults);

		?>
			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>">
					<?php _e( 'Widget-Titel:' ); ?>
				</label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>">
			</p>

			<div style="margin: 1em 0">
				<div class="input-group-mini">
					<span>Randfarbe</span>
					<div class="color-picker-wrapper" style="background-color:<?php echo esc_attr( $instance['bordercolor'] ); ?>">
						<input name="<?php echo $this->get_field_name( 'bordercolor' ); ?>" type="color" value="<?php echo esc_attr( $instance['bordercolor'] ); ?>">
					</div>
				</div>
			</div>

			<p>
				<label for="curvert_bpadding">Innerer Abstand: <span class="bpadding_preview"><?php echo esc_attr( $instance['bpadding'] ); ?></span>px</label>
				<input class="curvert-range-slider" id="curvert_bpadding" name="<?php echo $this->get_field_name('bpadding'); ?>" step="1" type="range" min="0" max="30" value="<?php echo esc_attr( $instance['bpadding'] ); ?>">
			</p>

			<p>
				<?php $borderstyle = $instance['borderstyle']; ?>
				<label for="<?php echo $this->get_field_id('borderstyle'); ?>">Rand Typ:
					<select class='widefat' id="<?php echo $this->get_field_id('borderstyle'); ?>" name="<?php echo $this->get_field_name('borderstyle'); ?>" type="text">
						<option value="dashed"<?php echo ($borderstyle=='dashed')?'selected':''; ?>>
							gestrichelt
						</option>
						<option value="dotted"<?php echo ($borderstyle=='dotted')?'selected':''; ?>>
							gepunktet
						</option>
						<option value="solid"<?php echo ($borderstyle=='solid')?'selected':''; ?>>
							durchgezogen
						</option>
					</select>
				</label>
			</p>

			<p>
				<label for="curvert_borderthicknes">Rand Stärke: <span class="border-thickness-preview"><?php echo esc_attr( $instance['borderthicknes'] ); ?></span>px</label>
				<input class="curvert-range-slider" id="curvert_borderthicknes" name="<?php echo $this->get_field_name('borderthicknes'); ?>" type="range" min="0" max="8" step="1" value="<?php echo esc_attr( $instance['borderthicknes'] ); ?>">
			</p>

			<hr />
			<div style="margin: 1em 0">
				<div><?php _e( 'Farben:' ); ?></div>
				<div class="input-group-mini">
					<span>Hintergrundfarbe</span>
					<div class="color-picker-wrapper" style="background-color:<?php echo esc_attr( $instance['bgcolor'] ); ?>">
						<input name="<?php echo $this->get_field_name( 'bgcolor' ); ?>" type="color" value="<?php echo esc_attr( $instance['bgcolor'] ); ?>">
					</div>
				</div>
				<div class="input-group-mini">
					<span>Textfarbe</span>
					<div class="color-picker-wrapper" style="background-color:<?php echo esc_attr( $instance['textcolor'] ); ?>">
						<input name="<?php echo $this->get_field_name( 'textcolor' ); ?>" type="color" value="<?php echo esc_attr( $instance['textcolor'] ); ?>">
					</div>
				</div>
			</div>

		<?php
	}


	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['title'] = strip_tags($new_instance['title']);
		$instance['bpadding'] = strip_tags($new_instance['bpadding']);
		$instance['bordercolor'] = strip_tags($new_instance['bordercolor']);
		$instance['borderstyle'] = strip_tags($new_instance['borderstyle']);
		$instance['borderthicknes'] = strip_tags($new_instance['borderthicknes']);
		$instance['bgcolor'] = strip_tags($new_instance['bgcolor']);
		$instance['textcolor'] = strip_tags($new_instance['textcolor']);

		return $instance;
	}
}


// Register and load the widget
function curvert_widget_load_widget() {
	register_widget( 'CurvertConverter' );
}

add_action('widgets_init', 'curvert_widget_load_widget');

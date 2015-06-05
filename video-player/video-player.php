<?php

class Video_Player {

	protected $version = '1.0.0';

	protected $plugin_slug = 'progression';

	protected static $instance = null;

	protected $plugin_screen_hook_suffix = null;

	protected $loaded_options = array();

	private function __construct() {

		$defaults = array(
			'startvolume' => 80,
			'autoplay' => 'false',
			'controls' => 'false',
			'size' => 'normal',
			'playlist' => 'true',
			'playlist_meta' => 'true',
			'active_skin' => 'default',
			'custom_skin' => 'false',
			'colors' => array(
				'bg' => '',
				'border' => '',
				'text' => '',
				'handle' => '',
				'slider' => ''
			)
		);

		if ( empty( $this->loaded_options )) {

			$db_options = get_option( $this->plugin_slug );

			if ( empty( $db_options ) ) {
				update_option( $this->plugin_slug, $defaults );
				$this->loaded_options = $defaults;
			} else {
				$this->loaded_options = wp_parse_args( $db_options, $defaults );
			}

		}

		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'settings_api_init'));
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_filter( 'wp_audio_shortcode', array( $this, 'modify_shortcode_output' ) );
		add_filter( 'wp_video_shortcode', array( $this, 'modify_shortcode_output' ) );
		add_filter( 'wp_playlist_shortcode', array( $this, 'modify_shortcode_output' ) );
		add_filter( 'post_playlist', array( $this, 'wp_playlist_shortcode' ), 10, 2 );
		add_action( 'wp_playlist_scripts', array( $this, 'wp_playlist_scripts' ) );
		add_action( 'wp_head', array( $this, 'custom_skin_css' ) );
		add_filter( 'widget_text', 'do_shortcode' );
	}

	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	protected function options( $key = false ){
		if ( $key ) {
			return $this->loaded_options[ $key ];
		} else {
			return $this->loaded_options;
		}
	}

	public function enqueue_admin_styles() {
		if ( get_current_screen()->id == $this->plugin_screen_hook_suffix ) {
			wp_enqueue_style( $this->plugin_slug .'-admin-styles', get_stylesheet_directory_uri() . '/video-player/css/progression-admin.css', array( 'wp-color-picker'  ), $this->version );
		}
	}

	public function enqueue_admin_scripts() {
		if ( get_current_screen()->id == $this->plugin_screen_hook_suffix ) {
			wp_enqueue_script( $this->plugin_slug . '-admin-script', get_stylesheet_directory_uri() . '/video-player/js/progression-admin.js', array( 'jquery', 'wp-color-picker' ), $this->version );
		}
		wp_enqueue_media();
	}

	public function enqueue_styles() {
		wp_enqueue_style( 'wp-mediaelement' );
		wp_enqueue_style( $this->plugin_slug, get_stylesheet_directory_uri() . '/video-player/assets/css/progression-player.css', array('wp-mediaelement'), $this->version );
	}

	public function enqueue_scripts() {
		wp_deregister_script( 'wp-mediaelement' );
		wp_register_script( 'wp-mediaelement', get_stylesheet_directory_uri() . '/video-player/js/progression-mediaelement.js', array( 'mediaelement' ), false, true );

		$options = $this->options();
		$options['startvolume'] = $options['startvolume'] / 100; 
		$options['features'] = array( 'playpause', 'current', 'progress', 'duration', 'volume', 'togglePlaylist', 'fullscreen' ); 

		wp_localize_script( 'mediaelement', '_wpmejsProgressionSettings', $options);
		wp_deregister_script( 'wp-playlist' );
		wp_register_script( 'wp-playlist', get_stylesheet_directory_uri() . '/video-player/js/progression-playlist.js', array( 'wp-util', 'backbone', 'mediaelement' ), false, true );
	}

	public function add_admin_menu() {
		$this->plugin_screen_hook_suffix = add_submenu_page(
			'options-general.php',
			__( 'Progression Player', $this->plugin_slug ),
			__( 'Progression Player', $this->plugin_slug ),
			'manage_options',
			$this->plugin_slug,
			array( $this, 'display_plugin_admin_page' )
		);
	}

	public function display_plugin_admin_page() {
		include_once( 'views/admin.php' );
	}

	public function settings_api_init() {
		register_setting( $this->plugin_slug, $this->plugin_slug );

	 	add_settings_section(
	 		$this->plugin_slug . '_skin',
			__( 'Player skin' ),
			array( $this, 'settings_section_skin_cb' ),
			'progression'
		);

	 	add_settings_field(
	 		$this->plugin_slug . '_active_skin',
			__( 'Selected player skin' ),
			array( $this, 'settings_field_active_skin_cb' ),
			'progression',
			$this->plugin_slug . '_skin'
		);

 	 	add_settings_field(
 	 		$this->plugin_slug . '_custom_skin',
 			__( 'Custom skin' ),
 			array( $this, 'settings_field_custom_skin_cb' ),
 			'progression',
 			$this->plugin_slug . '_skin'
 		);

 	 	$color_zones = array(
 	 		'bg' 		=> __( 'Background color' ),
 	 		'border' 	=> __( 'Border color' ),
 	 		'text' 		=> __( 'Text and icon color' ),
 	 		'slider' 	=> __( 'Background color of the volume and timeline slider' ),
 	 		'handle' 	=> __( 'Color of the volume and timeline handle' )
 	 	);

 	 	foreach ( $color_zones as $key => $label ) {
 		 	add_settings_field(
 		 		$this->plugin_slug . '_custom_skin_colors['. $key .']',
 				$label,
 				array( $this, 'settings_field_custom_skin_colors_cb' ),
 				'progression',
 				$this->plugin_slug . '_skin',
 				array(
 					'name' => 'custom_skin_colors',
 					'key' => $key
 				)
 			);
 	 	}

 	 	add_settings_field(
 	 		$this->plugin_slug . 'size',
 			__( 'Size' ),
 			array( $this, 'settings_field_defaults_cb' ),
 			'progression',
 			$this->plugin_slug . '_skin',
 			array(
 				'key' => 'size'
 			)
 		);

 	 	add_settings_section(
 	 		$this->plugin_slug . '_defaults',
 			__( 'Player options' ),
 			array( $this, 'settings_section_defaults_cb' ),
 			'progression'
 		);

 	 	add_settings_field(
 	 		$this->plugin_slug . '_startvolume',
 			__( 'Start volume' ),
 			array( $this, 'settings_field_defaults_cb' ),
 			'progression',
 			$this->plugin_slug . '_defaults',
			array(
				'key' => 'startvolume'
			)
 		);

	 	add_settings_field(
	 		$this->plugin_slug . '_controls',
			__( 'Always show controls' ),
			array( $this, 'settings_field_defaults_cb' ),
			'progression',
			$this->plugin_slug . '_defaults',
			array(
				'key' => 'controls'
			)
		);

	 	add_settings_section(
	 		$this->plugin_slug . '_playlist',
			__( 'Playlist options' ),
			array( $this, 'settings_section_playlist_cb' ),
			'progression'
		);

	 	add_settings_field(
	 		$this->plugin_slug . '_playlist',
			__( 'Show playlist' ),
			array( $this, 'settings_field_defaults_cb' ),
			'progression',
			$this->plugin_slug . '_playlist',
			array(
				'key' => 'playlist'
			)
		);

		add_settings_field(
	 		$this->plugin_slug . '_playlist_meta',
			__( 'Show playlist meta information' ),
			array( $this, 'settings_field_defaults_cb' ),
			'progression',
			$this->plugin_slug . '_playlist',
			array(
				'key' => 'playlist_meta'
			)
		);

 	 	register_setting( 'progression', $this->plugin_slug . '_startvolume' );

	}

	function settings_section_skin_cb() {
		echo '<p>'. __( 'These settings let you choose how Progression Player will look like.'). '</p>';
	}

	function settings_field_active_skin_cb() {
		$skins = array(
			'default' 		=> __( 'Default Skin' ),
			'default-dark' 	=> __( 'Dark Skin' ),
			'minimal-dark' 	=> __( 'Minimal Dark Skin' ),
			'minimal-light' => __( 'Minimal Light Skin' ),
			'fancy' 		=> __( 'Fancy Skin' )
		);

		$active_skin = $this->options( 'active_skin' );
		$option_name = $this->plugin_slug . '[active_skin]';
		$html_option = '<option value="%s"%s>%s</option>';

		$html = '';
		$html .= "<select name='$option_name'>";

			foreach ($skins as $skin => $skin_name)
				$html .= sprintf( $html_option, $skin, selected( $active_skin, $skin, false ), $skin_name);

		$html .= '</select>';

		echo $html;
	}

	function settings_field_custom_skin_cb() { ?>
		<input type="hidden" value="false" name="<?php echo $this->plugin_slug ?>[custom_skin]">
		<label><input name="<?php echo $this->plugin_slug ?>[custom_skin]" id="progression_custom_skin" type="checkbox" value="true" class="code" <?php echo checked( $this->options( 'custom_skin' ), 'true', false)?> /> <?php _e( 'Customize selected player skin' ); ?></label>
	<?php }

	function settings_field_custom_skin_colors_cb( $args ) {
		$options = $this->options();
		$key = $args['key'];
		$name = $this->plugin_slug . '[colors]['. $key .']';
		$value = $options[ 'colors' ][ $key ];
		$class = $this->plugin_slug . '-skincolor';

		echo "<input name='$name' value='$value' class='$class' />";
	}

	function settings_section_defaults_cb() {
		echo '<p>'. __( 'These settings set the behavior of Progression Player.'). '</p>';
	}

	function settings_field_defaults_cb( $args ) {
		$options = $this->options();
		$key = $args['key'];
		$name = $this->plugin_slug . '['. $key .']';
		$value = $options[ $key ];

		if ( 'startvolume' === $key ) {
			echo "<input name='$name' type='number' value='$value' min='0' max='100' step='5' /> <span>%<span>";
		}

		if (
			'controls' === $key ||
			'playlist' === $key ||
			'playlist_meta' === $key ) {
			$checked = checked( $value, 'true', false );
			echo "<input type='hidden' value='false' name='$name'>";
			echo "<input name='$name' type='checkbox' value='true' $checked />";
		}

		if (
			'size' === $key) {
			?>
			<label><input name='<?php echo $name; ?>' type='radio' value='normal' <?php checked( $value, 'normal' ) ?> /> <span><?php _e( 'normal (default)' ); ?></span></label><br>
			<label><input name='<?php echo $name; ?>' type='radio' value='small' <?php checked( $value, 'small' ) ?> /> <span><?php _e( 'small' ); ?></span></label>
		<?php }
	}

	function settings_section_playlist_cb() {
		echo '<p>'. __( 'These settings set the behavior of the playlist feature.'). '</p>';
	}

	public function modify_shortcode_output( $html ) {
		if ( $this->options( 'size' ) === 'small') {
			$html = '<div class="pro-small-player">' . $html . '</div>';
		}

		$active_skin = $this->options( 'active_skin' );

		$class = "progression-skin";
		$class .= " progression-$active_skin";

		if ( $this->options( 'custom_skin' ) === 'true') {
			$class .= " progression-custom";
		}

		$html = '<div class="'. $class .'">' . $html . '</div>';
		return $html;
	}

	public function custom_skin_css() {
		global $content_width;

		$options = $this->options();
		$colors = $options[ 'colors' ];

		if ( ! $options[ 'custom_skin' ] ) {
			return;
		}

		$html = '';

		if ( ! isset( $content_width ) )
			$content_width = 600;

		$html .= sprintf( '.progression-skin  { max-width: %s !important }', $content_width . 'px' );

		if ( !empty( $colors ) ) {
			foreach ( $colors as $key => $color ) {
				if ( empty( $color ) ) continue;
				if ( 'bg' === $key ) {
					$html .= sprintf( 'body .progression-skin.progression-custom .mejs-container .mejs-controls, .progression-skin.progression-custom .wp-playlist .wp-playlist-next, .progression-skin.progression-custom  .wp-playlist .wp-playlist-prev{ background: %s }', $color );
					$html .= sprintf( 'body .progression-skin.progression-custom .mejs-container .mejs-controls .mejs-playlist.progression-selected button,
body .progression-skin.progression-custom .mejs-container .mejs-inner .mejs-controls .mejs-fullscreen-button button:hover,
body .progression-skin.progression-custom .mejs-container .mejs-controls .mejs-playlist button:hover,
.progression-skin.progression-custom .wp-playlist .wp-playlist-next:hover, .progression-skin.progression-custom  .wp-playlist .wp-playlist-prev:hover,
body .progression-skin.progression-custom .mejs-container .mejs-controls .mejs-playpause-button button:hover { background: %s }', $this->brightness( $color, 20 ) );
				}

				if ( 'border' === $key ) {
					$html .= sprintf( 'body .progression-skin.progression-custom .mejs-container .mejs-controls .mejs-volume-button, body .progression-skin.progression-custom .mejs-container .mejs-inner .mejs-controls .mejs-fullscreen-button, body .progression-skin.progression-custom .mejs-container .mejs-controls .mejs-playlist button, .progression-skin.progression-custom .wp-playlist.wp-audio-playlist .wp-playlist-next, .progression-skin.progression-custom  .wp-playlist.wp-audio-playlist .wp-playlist-prev, body .progression-skin.progression-custom .mejs-container .mejs-controls .mejs-playpause-button button, body .progression-skin.progression-custom .mejs-container, body .progression-skin.progression-custom .wp-playlist-tracks, body .progression-skin.progression-custom  .wp-playlist-item { border-color: %s }', $color );
				}

				if ( 'text' === $key ) {
					$html .= sprintf( '.progression-skin.progression-custom .wp-playlist .wp-playlist-next, .progression-skin.progression-custom  .wp-playlist .wp-playlist-prev,
body .progression-skin.progression-custom .mejs-inner .mejs-controls span,
body .progression-skin.progression-custom .mejs-inner .mejs-controls button  { color: %s }', $color );
					$html .= sprintf( 'body .progression-skin.progression-custom .mejs-container .mejs-controls .mejs-playlist.progression-selected button,
.progression-skin.progression-custom .wp-playlist .wp-playlist-next:hover, .progression-skin.progression-custom  .wp-playlist .wp-playlist-prev:hover,
body .progression-skin.progression-custom .mejs-inner .mejs-controls button:hover { color: %s }', $this->brightness( $color, 20 ) );
				}

				if ( 'handle' === $key ) {
					$html .= sprintf( 'body .progression-skin.progression-custom .mejs-controls .mejs-time-rail .mejs-time-handle, body .progression-skin.progression-custom .mejs-controls .mejs-horizontal-volume-slider .mejs-horizontal-volume-handle, body .progression-skin.progression-custom .mejs-container .mejs-inner .mejs-controls .mejs-time-rail span.mejs-time-loaded { background: %s; border-color: %s }', $color, $color );
				}

				if ( 'slider' === $key ) {
					$html .= sprintf( 'body .progression-skin.progression-custom .mejs-container .mejs-inner .mejs-controls .mejs-time-rail .mejs-time-total, body .progression-skin.progression-custom .mejs-controls .mejs-horizontal-volume-slider .mejs-horizontal-volume-total { background: %s }', $color );
					$html .= sprintf( 'body .progression-skin.progression-custom .mejs-controls .mejs-horizontal-volume-slider .mejs-horizontal-volume-current, body .progression-skin.progression-custom .mejs-container .mejs-inner .mejs-controls .mejs-time-rail span.mejs-time-current { background: %s }', $this->brightness( $color, 30 ) );
				}
			}
		}

		if ( ! empty( $html )) {
			echo '<style type="text/css">'. $html .'</style>';
		}
	}

	private function brightness( $hex, $diff ){
		$rgb = str_split( trim( $hex, '# ' ), 2 );
			foreach ( $rgb as &$hex ) {
				$dec = hexdec( $hex );
				if ( $diff >= 0 ) {
					$dec += $diff;
				}
				else {
					$dec -= abs( $diff );
				}
				$dec = max( 0, min( 255, $dec ));
				$hex = str_pad( dechex( $dec ), 2, '0', STR_PAD_LEFT );
			}

			return '#'.implode( $rgb );
	}

	function wp_playlist_scripts() {
		wp_enqueue_script( $this->plugin_slug . '-playlist' );
	}

	function wp_playlist_shortcode( $output, $attr ) {
		global $content_width;

		$post = get_post();
		static $instance = 0;
		$instance++;

		if ( isset( $attr['orderby'] ) ) {
			$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
			if ( ! $attr['orderby'] )
				unset( $attr['orderby'] );
		}

		extract( shortcode_atts( array(
			'type'		=> 'audio',
			'order'		=> 'ASC',
			'orderby'	=> 'menu_order ID',
			'id'		=> $post ? $post->ID : 0,
			'include'	=> '',
			'exclude'   => '',
			'style'		=> 'light',
			'tracklist' => true,
			'tracknumbers' => true,
			'images'	=> true,
			'artists'	=> true
		), $attr, 'playlist' ) );

		$id = intval( $id );
		if ( 'RAND' == $order ) {
			$orderby = 'none';
		}

		$args = array(
			'post_status' => 'inherit',
			'post_type' => 'attachment',
			'post_mime_type' => $type,
			'order' => $order,
			'orderby' => $orderby
		);

		if ( ! empty( $include ) ) {
			$args['include'] = $include;
			$_attachments = get_posts( $args );

			$attachments = array();
			foreach ( $_attachments as $key => $val ) {
				$attachments[$val->ID] = $_attachments[$key];
			}
		} elseif ( ! empty( $exclude ) ) {
			$args['post_parent'] = $id;
			$args['exclude'] = $exclude;
			$attachments = get_children( $args );
		} else {
			$args['post_parent'] = $id;
			$attachments = get_children( $args );
		}

		if ( empty( $attachments ) ) {
			return '';
		}

		if ( is_feed() ) {
			$output = "\n";
			foreach ( $attachments as $att_id => $attachment ) {
				$output .= wp_get_attachment_link( $att_id ) . "\n";
			}
			return $output;
		}

		$outer = 22; 
		$default_width = 640;
		$default_height = 360;
		$theme_width = empty( $content_width ) ? $default_width : ( $content_width - $outer );
		$theme_height = empty( $content_width ) ? $default_height : round( ( $default_height * $theme_width ) / $default_width );
		$data = compact( 'type' );

		foreach ( array( 'tracklist', 'tracknumbers', 'images', 'artists' ) as $key ) {
			$data[$key] = filter_var( $$key, FILTER_VALIDATE_BOOLEAN );
		}

		$tracks = array();
		foreach ( $attachments as $attachment ) {
			$url = wp_get_attachment_url( $attachment->ID );
			$ftype = wp_check_filetype( $url, wp_get_mime_types() );
			$track = array(
				'src' => $url,
				'type' => $ftype['type'],
				'title' => $attachment->post_title,
				'caption' => $attachment->post_excerpt,
				'description' => $attachment->post_content
			);

			$track['meta'] = array();
			$meta = wp_get_attachment_metadata( $attachment->ID );
			if ( ! empty( $meta ) ) {

				foreach ( wp_get_attachment_id3_keys( $attachment ) as $key => $label ) {
					if ( ! empty( $meta[ $key ] ) ) {
						$track['meta'][ $key ] = $meta[ $key ];
					}
				}

				if ( 'video' === $type ) {
					if ( ! empty( $meta['width'] ) && ! empty( $meta['height'] ) ) {
						$width = $meta['width'];
						$height = $meta['height'];
						$theme_height = round( ( $height * $theme_width ) / $width );
					} else {
						$width = $default_width;
						$height = $default_height;
					}

					$track['dimensions'] = array(
						'original' => compact( 'width', 'height' ),
						'resized' => array(
							'width' => $theme_width,
							'height' => $theme_height
						)
					);
				}
			}

			if ( $images ) {
				$id = get_post_thumbnail_id( $attachment->ID );
				if ( ! empty( $id ) ) {
					list( $src, $width, $height ) = wp_get_attachment_image_src( $id, 'full' );
					$track['image'] = compact( 'src', 'width', 'height' );
					list( $src, $width, $height ) = wp_get_attachment_image_src( $id, 'thumbnail' );
					$track['thumb'] = compact( 'src', 'width', 'height' );
				} else {
					$src = wp_mime_type_icon( $attachment->ID );
					$width = 48;
					$height = 64;
					$track['image'] = compact( 'src', 'width', 'height' );
					$track['thumb'] = compact( 'src', 'width', 'height' );
				}
			}

			$tracks[] = $track;
		}
		$data['tracks'] = $tracks;

		$safe_type = esc_attr( $type );
		$safe_style = esc_attr( $style );
		$playlist_meta = 'true' == $this->options( 'playlist_meta' ) ? "" : " hide-playlist-meta-pro ";

		ob_start();

		if ( 1 === $instance ) {
			do_action( 'wp_playlist_scripts', $type, $style );
		} ?>
	<div class="wp-playlist wp-<?php echo $safe_type ?>-playlist wp-playlist-<?php echo $safe_style ?> <?php echo $playlist_meta ?>">
		<?php if ( 'audio' === $type ): ?>
		<div class="wp-playlist-current-item"></div>
		<?php endif ?>
		<<?php echo $safe_type ?> controls="controls" preload="none" width="<?php
			echo (int) $theme_width;
		?>"<?php if ( 'video' === $safe_type ):
			echo ' height="', (int) $theme_height, '"';
		else:
			echo ' style="visibility: hidden"';
		endif; ?>></<?php echo $safe_type ?>>
		<div class="wp-playlist-next"></div>
		<div class="wp-playlist-prev"></div>
		<noscript>
		<ol><?php
		foreach ( $attachments as $att_id => $attachment ) {
			printf( '<li>%s</li>', wp_get_attachment_link( $att_id ) );
		}
		?></ol>
		</noscript>
		<script type="application/json"><?php echo json_encode( $data ) ?></script>
	</div>
		<?php
		return apply_filters( 'wp_playlist_shortcode', ob_get_clean() );
	}
}

Video_Player::get_instance();

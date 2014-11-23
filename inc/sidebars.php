<?php

/**
 * Unlimited Sidebars (widget areas)
 *
 * @package WordPress
 * @subpackage Twenty_Fifteen
 * @since Twenty Fifteen 1.0
 */

/**
 * Registers all the extra widget areas
 *
*/
function twentyfifteen_more_widgets_init() {

        $unlimited_sidebars_options = get_option('unlimited_sidebars_settings');

        if($unlimited_sidebars_options){

            foreach ($unlimited_sidebars_options as $sidebar_id => $sidebar_name){

		register_sidebar( array(
			'name'          => $sidebar_name['name'],
			'id'            => $sidebar_id,
			'description'   => __( 'Add widgets here to appear in your sidebar.', 'twentyfifteen' ),
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		) );

            }
	}
}

add_action( 'widgets_init', 'twentyfifteen_more_widgets_init' );

/**
 * Add Sidebars page to the appearance section
 *
*/
function add_unlimited_sidebars() {
	add_theme_page('Sidebars', 'Sidebars', 'edit_theme_options', 'fivebeers-sidebars', 'unlimited_sidebars');
}

add_action('admin_menu', 'add_unlimited_sidebars');

/**
 * Add settings section, and field for unlimited sidebars
 *
*/
function init_unlimited_sidebars(){
	register_setting( 'unlimited_sidebars_settings', 'unlimited_sidebars_settings');
	add_settings_section('unlimited_sidebars_section', '', '', 'fivebeers-sidebars');
	add_settings_field('settings_unlimited_sidebars', '', 'settings_unlimited_sidebars', 'fivebeers-sidebars', 'unlimited_sidebars_section');
}

add_action('admin_init', 'init_unlimited_sidebars');

/**
 * Markup for the settings section where you can create the sidebars
 *
*/
function settings_unlimited_sidebars() {
	$options = get_option('unlimited_sidebars_settings');

	if (isset($_GET['settings-updated'])) {
		if ($_GET['settings-updated'] == true){ ?>
			<div id="message" class="updated below-h2">
				<p><?php echo __('Sidebar settings updated.', 'fivebeers'); ?></p>
			</div>
		<?php }
	} ?>

	<div id="add-new-item">
		<input class="new-item-name" autocomplete="off" type="text" placeholder="<?php echo __('Sidebar name...', 'fivebeers'); ?>" />
		<input class="add-item button button-primary button-large" type="submit" value="<?php echo __('Add Sidebar', 'fivebeers'); ?>" />
	</div>

	<div class="repeatables-wrap sortable-wrap">

		<input name="unlimited_sidebars_settings" type="hidden" class="item-name-input" value="" />

		<div id="dummy-item-placeholder" class="item-holder hidden">
			<div class="repeatable-name">
				<div class="item-move"></div>
				<h4></h4>
			</div>
			<div class="item-info">
				<input autocomplete="off" placeholder="<?php echo __('Sidebar name...', 'fivebeers'); ?>" name="[name]" type="text" class="item-name-input" value="" />
				<a class="delete-single-repeat" href=""><?php echo __('Delete Sidebar', 'fivebeers'); ?></a>
			</div>
		</div>

		<?php if(!$options){ ?>

			<h4 class="no-items"><?php echo __('There are no extra sidebars currently, add one!', 'fivebeers'); ?></h4>

		<?php } else { ?>

			<?php foreach ($options as $sidebar_id => $sidebar_name) { ?>

				<div id="<?php echo $sidebar_id ?>" class="item-holder">
					<div class="repeatable-name">
						<div class="item-move"></div>
						<h4><?php echo $sidebar_name['name']; ?></h4>
					</div>
					<div class="item-info">
						<input autocomplete="off" placeholder="<?php echo __('Sidebar name...', 'fivebeers'); ?>" name="unlimited_sidebars_settings[<?php echo $sidebar_id; ?>][name]" type="text" class="item-name-input" value="<?php echo $sidebar_name['name']; ?>" />
						<a class="delete-single-repeat" href=""><?php echo __('Delete Sidebar', 'fivebeers'); ?></a>
					</div>
				</div>
			<?php } ?>

		<?php } ?>

	</div>
	<?php
}

/**
 * Load the scripts to allow sorting, and execute the settings
 *
*/
function unlimited_sidebars(){

	global $post;

	if (!current_user_can('manage_options')) {  
		wp_die( __('You do not have sufficient permissions to access this page.', 'fivebeers') );  
	}

	wp_register_script( 'sidebar-script', get_stylesheet_directory_uri() . '/js/sidebars.js' );

	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'jquery-ui-core' );
	wp_enqueue_script( 'jquery-ui-sortable' ); 
	wp_enqueue_script( 'sidebar-script' );

	wp_register_style( 'sidebar-style', get_stylesheet_directory_uri() . '/css/sidebars.css' );
	wp_enqueue_style( 'sidebar-style' );

	wp_localize_script( 'sidebar-script', 'fivebeersRepeatables', array(
		'alert' => __('Are you sure you want to delete this sidebar?', 'fivebeers'),
		)
	);

	?>

	<div class="wrap">
		<div id="icon-themes" class="icon32">
			<br>
		</div>
		<h2><?php echo __('Sidebars', 'fivebeers'); ?></h2>
		<p><?php echo __('Add a new sidebar which you can then use on any post or page by selecting it from the "Sidebars" metabox. Number of sidebars is unlimited.', 'fivebeers'); ?></p>

		<form action="options.php" method="post" class="repeatable-form unlimited-sidebars-form">
			<?php settings_fields('unlimited_sidebars_settings'); ?>
			<?php do_settings_sections('fivebeers-sidebars'); ?>
			<input class="button button-primary button-large" type="submit" value="<?php echo __('Save Sidebars', 'fivebeers'); ?>" name="save">
		</form>
	</div>
<?php }

$fivebeers_options = get_option('fivebeers_theme_options');

add_action( 'add_meta_boxes', 'choose_sidebar_metabox_add' );

/**
 * Add Metaboxes for sidebar options for posts and pages
 *
*/
function choose_sidebar_metabox_add(){
	add_meta_box( '_choose-sidebar', 'Sidebar', 'choose_sidebar_metabox', 'post', 'side' );
	add_meta_box( '_choose-sidebar', 'Sidebar', 'choose_sidebar_metabox', 'page', 'side' );
}

/**
 * Add Dropdown for a list of created sidebars plus the default one
 *
*/
function choose_sidebar_metabox( $post ){

	$selected_value = get_post_meta( $post->ID, '_choose_sidebar', true );

	if(isset($selected_value) == false){
		$selected_value = 'sidebar-1';
	}

	$sidebars = $GLOBALS['wp_registered_sidebars'];

	sort($sidebars);

	wp_nonce_field( 'choose-sidebar-nonce', 'choose-sidebar-check' ); ?>

	<select name="choose_sidebar" id="choose_sidebar" style="width:100%;">

		<?php foreach ( $sidebars as $sidebar ) { ?>
			<option value="<?php echo $sidebar['id']; ?>" <?php selected( $selected_value, $sidebar['id'] ); ?> >
				<?php echo $sidebar['name']; ?>
			</option>
		<?php } ?>

	</select>
<?php }

add_action( 'save_post', 'choose_sidebar_metabox_save' );

/**
 * Save the sidebar for each single post or page
 *
*/
function choose_sidebar_metabox_save($post_id){

	if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return; 
	if( !isset( $_POST['choose-sidebar-check'] ) || !wp_verify_nonce( $_POST['choose-sidebar-check'], 'choose-sidebar-nonce' ) ) return; 
	if( !current_user_can( 'edit_post', $post_id ) ) return;  

	if( isset( $_POST['choose_sidebar'] )){
		update_post_meta( $post_id, '_choose_sidebar', esc_attr( $_POST['choose_sidebar'] ) );  
	}
}


<div class="wrap">

	<?php screen_icon(); ?>
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<form method="POST" action="options.php">

		<?php

		settings_fields('progression');
		do_settings_sections('progression');
		submit_button();

		?>

	</form>

</div>

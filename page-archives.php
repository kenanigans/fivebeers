<?php
/**
 * The template for displaying the dropdown for the ajax archives
 *
 * @package WordPress
 * @subpackage Twenty_Fifteen
 * @since Twenty Fifteen 1.0
 */

get_header(); ?>

	<div class="clear" style="height:4px;clear:both;"></div>

        <div id="archive-browser" align="center">
		<h2>Archives</h2>
              <select id="month-choice" name="" class="form-control">
                <option value="no-choice" selected> &mdash; </option>
		<?php wp_get_archives(array('type' => 'monthly', 'format'  => 'option')); ?>
              </select>

		<?php wp_dropdown_categories('hide_empty=0&show_option_none= -- ');?> 

	<div class="clear" style="height:14px;clear:both;"></div>

	<div id="archive-wrapper">
		<div class="message" align="center">Please choose from above.</div>
	</div>
	</div>

	<div class="clear" style="height:14px;clear:both;"></div>

	<div id="archive-content"></div>


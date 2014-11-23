<?php

/**
 * Minimal Lightbox
 *
 * @package WordPress
 * @subpackage Twenty_Fifteen
 * @since Twenty Fifteen 1.0
 */

/**
 * Add Image URL oEmbed for lightbox, and enable imgur.com oEmbeds
 *
*/
function strip_embeds() { 
	wp_embed_register_handler( 'detect_lightbox', '#^http://.+\.(jpe?g|gif|png)$#i', 'strip_embed_register_handler' , 10, 3);
	wp_oembed_remove_provider( '#https?://(.+\.)?imgur\.com/.*#i' );
}

add_action( 'init', 'strip_embeds' );

/**
 * Convert YouTube and Vimeo oEmbeds to markup for the lightbox
 *
*/
function strip_embed_html( $html, $url, $args, $post_ID ) {

	$screenshot = wp_get_attachment_url( get_post_thumbnail_id($post_ID) ) ? wp_get_attachment_url( get_post_thumbnail_id($post_ID) ) : 'http://fakeimg.pl/439x230/282828/eae0d0/?text=Click%20to%20Play!';

        if ( strstr($url, 'youtube.com') || strstr($url, 'vimeo.com')) {
        	$html = sprintf('<a href="%1$s" class="strip"><img src="%2$s" /></a>', $url, $screenshot);
        }

        return $html;
}

add_action( 'embed_oembed_html', 'strip_embed_html' , 10, 4);

/**
 * Alter markup when clicking insert into post
 *
*/
function strip_media_filter($html, $attachment_id) {

	$attachment = get_post($attachment_id);

	$types = array('image/jpeg', 'image/gif', 'image/png');

	if(in_array($attachment->post_mime_type, $types) ) {
		$strip_attr = sprintf('class="strip thumbnail" data-strip-group="gallery-%s" data-strip-options="side: %s"', $attachment->post_parent, "'right'");
    		$html = '<a href="'. wp_get_attachment_url($attachment_id) .'" '. $strip_attr .'"><img src="'. wp_get_attachment_thumb_url($attachment_id) .'"></a>';
	}

	return $html;
}

add_filter( 'media_send_to_editor', 'strip_media_filter', 20, 2);

/**
 * Convert image urls including imgur.com urls to lightbox markup
 *
*/
function strip_embed_register_handler( $matches, $attr, $url, $rawattr ) {
	global $post;

	if (preg_match('#^http://.+\.(jpe?g|gif|png)$#i', $url)) {
      			$embed = sprintf('<a href="%s" class="strip thumbnail" data-strip-group="gallery-%s" data-strip-options="side: %s"><img src="%s"></a>', $matches[0], $post->ID, "'right'", $matches[0]);
	}

	$embed = apply_filters( 'oembed_detect_lightbox', $embed, $matches, $attr, $url, $rawattr );
	return apply_filters( 'oembed_result', $embed, $url);
}

/**
 * Modify Gallery Shortcode Markup for lightbox
 *
*/
function strip_gallery( $content, $attr ) {
    	global $instance, $post;

    	$instance++;

    	if ( isset( $attr['orderby'] ) ) {
        	$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
        	if ( ! $attr['orderby'] )
        		unset( $attr['orderby'] );
    	}

    	extract( shortcode_atts( array(
        	'order'      =>  'ASC',
        	'orderby'    =>  'menu_order ID',
        	'id'         =>  $post->ID,
        	'itemtag'    =>  'figure',
        	'icontag'    =>  'div',
        	'captiontag' =>  'figcaption',
        	'columns'    =>   3,
        	'size'       =>   'thumbnail',
        	'include'    =>   '',
        	'exclude'    =>   ''
    	), $attr ) );

    	$id = intval( $id );

    	if ( 'RAND' == $order ) {
        	$orderby = 'none';
    	}

    	if ( $include ) {
        
        	$include = preg_replace( '/[^0-9,]+/', '', $include );
        
        	$_attachments = get_posts( array(
			'include'        => $include,
			'post_status'    => 'inherit',
        		'post_type'      => 'attachment',
        		'post_mime_type' => 'image',
        		'order'          => $order,
        		'orderby'        => $orderby
        	) );

        	$attachments = array();
        
        	foreach ( $_attachments as $key => $val ) {
        		$attachments[$val->ID] = $_attachments[$key];
        	}

    		} elseif ( $exclude ) {
        
        		$exclude = preg_replace( '/[^0-9,]+/', '', $exclude );
        
        		$attachments = get_children( array(
        			'post_parent'    => $id,
        			'exclude'        => $exclude,
        			'post_status'    => 'inherit',
        			'post_type'      => 'attachment',
        			'post_mime_type' => 'image',
        			'order'          => $order,
        			'orderby'        => $orderby
        		) );

    		} else {

        		$attachments = get_children( array(
        			'post_parent'    => $id,
        			'post_status'    => 'inherit',
        			'post_type'      => 'attachment',
        			'post_mime_type' => 'image',
        			'order'          => $order,
        			'orderby'        => $orderby
        		) );

    		}

    		if ( empty( $attachments ) ) {
        		return;
    		}

    		if ( is_feed() ) {
        		$output = "\n";
        		foreach ( $attachments as $att_id => $attachment )
        			$output .= wp_get_attachment_link( $att_id, $size, true ) . "\n";
        		return $output;
    		}

    		$output = "\n" . '<div class="strip_gallery">' . "\n";

    		foreach ( $attachments as $id => $attachment ) {
			$strip_attr = sprintf('class="strip thumbnail" data-strip-group="gallery-%s" data-strip-caption="%s" data-strip-options="side: %s"', $post->ID, $post->post_title, "'right'");
        		$output .= '<a href="'. wp_get_attachment_url($id) .'" '. $strip_attr. '"><img src="'. wp_get_attachment_thumb_url($id) .'" class="strip thumbnail"></a>' . "\n";
    		}

    		$output .= "</div>" . "\n";
		return $output;
}

add_filter( 'post_gallery', 'strip_gallery', 10, 2 );

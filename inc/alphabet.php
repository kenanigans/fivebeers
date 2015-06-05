<?php

/**
 * Ajax Alphavet A-Z Post Loading
 *
 * @package WordPress
 * @subpackage Twenty_Fifteen
 * @since Twenty Fifteen 1.0
 */

/**
 * List letters A-Z Based on posts in the database
 */
function fivebeers_aznav() {
    $num = array(
        '0',
        '1',
        '2',
        '3',
        '4',
        '5',
        '6',
        '7',
        '8',
        '9'
    );
    $alphabet = array(
        'A',
        'B',
        'C',
        'D',
        'E',
        'F',
        'G',
        'H',
        'I',
        'J',
        'K',
        'L',
        'M',
        'N',
        'O',
        'P',
        'Q',
        'R',
        'S',
        'T',
        'U',
        'V',
        'W',
        'X',
        'Y',
        'Z'
    );

    global $wpdb;
    $myposts = $wpdb->get_results("SELECT post_title FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' ORDER BY post_title");

    $n[] = '';

    foreach ($myposts as $mypost) {
        $n[] = strtoupper(SUBSTR(trim($mypost->post_title), 0, 1));
    }

    $output = '<div id="aznav"><div class="nav-links azindex">';
    //$output = '<div id="aznav"><div class="nav-links azindex">';
    for ($i = 0; $i < count($num); $i++) {
        if (in_array($num[$i], $n)) {
            $number = true;
            break;
        }
    }
    if (isset($number)) {
        $output .= '<a href="#" class="alphabet azindex" rel="num">0-9</a>';
    } else {
        $output .= '<span class="alphabet current">0-9</span>';

    }

    for ($i = 0; $i < count($alphabet); $i++) {
        if (in_array($alphabet[$i], $n)) {
            $output .= '<a href="#" class="alphabet azindex" rel="' . $alphabet[$i] . '">' . $alphabet[$i] . '</a>';
        } else {
            $output .= '<span class="alphabet current">' . $alphabet[$i] . '</span>';

        }
    }
    $output .= '</div><div style="clear:both"></div>';
    echo $output;
}

/**
 * Ajax function to load the posts by Letter
 */
function fivebeers_alphabet() {
    $letter = isset($_GET['letter']) ? $_GET['letter'] : null;
    if (!$letter) {
        fail(__('Error post letter.','bits'));
    } else {

        global $wpdb;

        if ($letter == 'num') {
            $myposts = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE SUBSTRING(LOWER(post_title),1,1) BETWEEN '0' AND '9' AND post_status = 'publish' AND post_type = 'post' ORDER BY post_title ASC");
        } else {
            $myposts = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE post_title LIKE '" . $letter . "%' AND post_status = 'publish' AND post_type = 'post' ORDER BY post_title ASC");
        }
        //$output = '<div id="#content">';
        $output = '';
        if (empty($myposts)) {
            $output .= 'Apologies, but no results were found for the requested archive. Perhaps searching will help find a related post.';

        } else {

                foreach($myposts as $mypost) {
		    $output.= '<article class="post hentry">';
		    $output.= '<header class="entry-header">';
		    $output.= '<h2 class="entry-title">';
		    $output.= '<a href="'. get_permalink($mypost->ID) .'" rel="bookmark">';
		    $output.= get_the_title($mypost->ID);
		    $output.= '</a><h2>';
                    $output.= '</header>';
	            $output.= '<div class="entry-content">';
		    $output.= $mypost->post_content;
		    $output.= '</div>';
		    $output.= '</article>';
            }
        }
        echo do_shortcode($output);
        die();
    }
}

add_action('wp_ajax_myajax_alphabet', 'fivebeers_alphabet');
add_action('wp_ajax_nopriv_myajax_alphabet', 'fivebeers_alphabet');


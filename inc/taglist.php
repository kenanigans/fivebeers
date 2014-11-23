<?php

/**
 * Sitewide Tag List
 *
 * @package WordPress
 * @subpackage Twenty_Fifteen
 * @since Twenty Fifteen 1.0
 */

/**
 * Compare Tag by Name callback for usort
 */

function taggify_cmp($a, $b) {
        return strcmp($a->name, $b->name);
}

/**
 * Show a list of tags by Letter used on the tags page (page-tags.php)
 *
 */
function tag_index() {

            global $content_width;

             $columns = 4;
             $width = 600;


            $full_width = $content_width;

            if ( !empty($width) )
                $full_width = $width;

            $width = ($full_width/$columns)-20;

            $tags = get_tags(array('orderby' => 'name', 'order' => 'ASC'));

            usort($tags, 'taggify_cmp');

            $letters = array();

            $break_letters = array();
            $break_count = 0;

            foreach ($tags as $tag){

                $break_tag_name = ucfirst( $tag->name );

                $letter = mb_substr($break_tag_name, 0, 1);

                if (!in_array($letter, $break_letters)) {
                    array_push($break_letters, $letter);

                    $break_count++;

                }

            }

            $break = $break_count/$columns;

            $content = '<ul style="width:'.$width.'px">';

            $i=0;

            foreach ($tags as $tag){

                $tag_link = get_tag_link($tag->term_id);
                $tag_name = ucfirst( $tag->name );
                $tag_slug = $tag->slug;
                $tag_count = $tag->count;
                $letter = mb_substr($tag_name, 0, 1);

                if ($tag_count == 1) {

                    $post_level = 'Post';

                } else {

                    $post_level = 'Posts';

                }

                $title = $tag_count.' '.$post_level.' in '.$tag_name.' Tag';

                if(is_numeric($letter)) $letter = '#';

                if (!in_array($letter, $letters)) {

                    if ($i>=$break) {
                        $content .= '</ul><ul style="width:'.$width.'px">';
                        $i = 0;
                    }

                    array_push($letters, $letter);

                    if ($i > 0) {
                        $content .= '</div>';
                    }

                    $id = $letter;

                    if ($letter=='#') {
                        $id = 'num';
                    }

                    $content .= '<div class="tag-wrap" id="'.$id.'"><h4 class="tag-ind">'.$letter.'</h4>';

                    $i++;

                };

                $content .= '<a href="'.$tag_link.'" class="tag-link '.$tag_slug.'"><li class="tag"><p>'.$tag_name.'</p><span title="'.$title.'">'.$tag_count.'</span></li></a>';

            }

            $content .= '</ul></div>';

            $content = '<div class="tag-index">' . $content;

            return $content;
}


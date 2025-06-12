<?php

if (isset($attributes['selected'])) {
    echo do_shortcode("[doc id=" . esc_attr($attributes['selected']) . "]");
} else if (isset($attributes['data']['tringle_text'])) {
    echo do_shortcode("[doc id=" . esc_attr($attributes['data']['tringle_text']) . "]");
}

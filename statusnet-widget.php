<?php
/*
Plugin Name: StatusNet Widget
Plugin URI: http://github.com/evgeni/wp-statusnet-widget
Description: A Widget for StatusNet
Version: 0.5
Author: Evgeni Golov
Author URI: http://www.die-welt.net
License: GPL2
Text Domain: statusnet-widget
Domain Path: /languages
*/

/*  Copyright 2010 Evgeni Golov <sargentd@die-welt.net>

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * StatusNetWidget Class
 */
class StatusNetWidget extends WP_Widget {
    /** constructor */
    function StatusNetWidget() {
        parent::WP_Widget(false, $name = 'StatusNetWidget');
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance) {
        extract( $args );
        $title = apply_filters('widget_title', $instance['title']);
        ?>
              <?php echo $before_widget; ?>
                  <?php if ( $title )
                        echo $before_title . $title . $after_title; ?>
                  <?php $this->render_content($instance); ?>
              <?php echo $after_widget; ?>
        <?php
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['merged'] = strip_tags($new_instance['merged']);
        $instance['prefer_content'] = strip_tags($new_instance['prefer_content']);
        $instance['source_list'] = trim(strip_tags($new_instance['source_list']));
        if (ctype_digit($new_instance['max_items'])) $instance['max_items'] = $new_instance['max_items'];
        else $instance['max_items'] = 10;
        if (ctype_digit($new_instance['cache_lifetime'])) $instance['cache_lifetime'] = $new_instance['cache_lifetime'];
        else $instance['cache_lifetime'] = 30;
        return $instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {
        $instance = wp_parse_args( (array) $instance, array( 'merged' => '1', 'prefer_content' => 0, 'title' => '', 'source_list' => '', 'max_items' => 10, 'cache_lifetime' => 30) );
        $title = esc_attr($instance['title']);
        $merged = esc_attr($instance['merged']);
        $prefer_content = esc_attr($instance['prefer_content']);
        $max_items = esc_attr($instance['max_items']);
        $source_list = esc_attr($instance['source_list']);
        $cache_lifetime = esc_attr($instance['cache_lifetime']);
        ?>
            <p>
               <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'statusnet-widget'); ?>
                 <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
               </label>
            </p>
            <p>
               <label for="<?php echo $this->get_field_id('merged'); ?>"><?php _e('Merge Mode:', 'statusnet-widget'); ?>
                 <input class="widefat" id="<?php echo $this->get_field_id('merged'); ?>" name="<?php echo $this->get_field_name('merged'); ?>" type="checkbox" value="1" <?php checked('1', $merged); ?> />
               </label>
            </p>
            <p>
               <label for="<?php echo $this->get_field_id('prefer_content'); ?>"><?php _e('Prefer content with markup:', 'statusnet-widget'); ?>
                 <input class="widefat" id="<?php echo $this->get_field_id('prefer_content'); ?>" name="<?php echo $this->get_field_name('prefer_content'); ?>" type="checkbox" value="1" <?php checked('1', $prefer_content); ?> />
               </label>
            </p>
            <p>
               <label for="<?php echo $this->get_field_id('max_items'); ?>"><?php _e('Max Items:', 'statusnet-widget'); ?>
                 <input class="widefat" id="<?php echo $this->get_field_id('max_items'); ?>" name="<?php echo $this->get_field_name('max_items'); ?>" type="text" value="<?php echo $max_items; ?>" />
               </label>
            </p>
            <p>
               <label for="<?php echo $this->get_field_id('cache_lifetime'); ?>"><?php _e('Cache Lifetime (minutes):', 'statusnet-widget'); ?>
                 <input class="widefat" id="<?php echo $this->get_field_id('cache_lifetime'); ?>" name="<?php echo $this->get_field_name('cache_lifetime'); ?>" type="text" value="<?php echo $cache_lifetime; ?>" />
               </label>
            </p>
            <p>
               <label for="<?php echo $this->get_field_id('source_list'); ?>"><?php _e('Source List:', 'statusnet-widget'); ?>
                 <textarea class="widefat" id="<?php echo $this->get_field_id('source_list'); ?>" name="<?php echo $this->get_field_name('source_list'); ?>"><?php echo $source_list; ?></textarea>
               </label>
            </p>
        <?php 
    }

    function render_content($instance) {
        include_once(ABSPATH . WPINC . '/feed.php');

        $merged = esc_attr($instance['merged']);
        $prefer_content = esc_attr($instance['prefer_content']);
        $max_items = esc_attr($instance['max_items']);
        $source_list = trim(esc_attr($instance['source_list']));
        $cache_lifetime = esc_attr($instance['cache_lifetime']);

        $source_list = explode("\n", $source_list);
        $all_messages = array();
        $feeds = array();

        foreach($source_list as $key=>$source) {
          $orig_source = $source;
          $source = trim($source);
          $source = rtrim($source, '/');
          if (stripos($source, '//twitter.com/') === false && stripos($source, '//search.twitter.com/') === false && stripos($source, '//www.ohloh.net/') === false)
              $feeds[] = $source.'/rss';
        }
        add_filter('wp_feed_cache_transient_lifetime', array(&$this, 'get_cache_lifetime'));
        $feed = fetch_feed($feeds);
        remove_filter('wp_feed_cache_transient_lifetime', array(&$this, 'get_cache_lifetime'));
        $max_items_in_feed = 0;
        if (!is_wp_error( $feed ) ) { // Checks that the object is created correctly
            // Figure out how many total items there are, but limit it to $max_items*count($feeds).
            $max_items_in_feed = $feed->get_item_quantity($max_items*count($feeds));
            // Build an array of all the items, starting with element 0 (first element).
            $rss_items = $feed->get_items(0, $max_items_in_feed);
        }

        echo '<ul class="statusnet">';

        if ( ! $max_items_in_feed ) {
            echo '<li>'.__('No public messages.', 'statusnet-widget').'</li>';
        } else {
            if ($merged) {
                foreach ( $rss_items as $msg ) {
                    $found_old = false;
                    $striped_msg = str_replace(array('#', '!'), '', $msg->get_title());
                    $striped_msg = substr($striped_msg, strpos($striped_msg, ':'));
                    foreach($all_messages as $k=>$m) {
                        $s = str_replace(array('#', '!'), '', $m->get_title());
                        $s = substr($s, strpos($s, ':'));
                        if (strcmp($striped_msg, $s) == 0) {
                            $found_old = true;
                        }
                    }
                    if (!$found_old) {
                        $all_messages[] = $msg;
                    }
                }
                $rss_items = $all_messages;
            }
            $i = 0;
            foreach ( $rss_items as $message ) {
                if ($i < $max_items) {
                    echo '<li class="statusnet-item">'.$this->prepare_message($message, $prefer_content).'</li>';
                    $i++;
                }
            }
        }

        echo '</ul>';
    }

    function prepare_message($message, $prefer_content) {
        $link = $message->get_feed()->get_link();
        $link_base = implode('/', explode('/', $link, -1));
            $search_base=$link_base.'/tag/';
            $group_base=$link_base.'/group/';
            $user_base=$link_base;
            if ($prefer_content)
                $m = $message->get_content();
            else {
                $m = $message->get_title();
                $m = substr($m, strpos($m, ':')+2);
            }

        $time = $message->get_date('U');
        if ((abs(time() - $time)) < 86400) {
            $h_time = sprintf( __('%s ago', 'statusnet-widget'), human_time_diff( $time ) );
        } else {
            $h_time = date(__('Y/m/d', 'statusnet-widget'), $time);
        }

        if (!$prefer_content) {
            $m = preg_replace('/(http:\/\/[\S]+)/', '<a href="\1">\1</a>', $m);
            $m = preg_replace('/(^|[^\w\d]+)@([\w\d_-]+)/', '\1<a href="'.$user_base.'/\2">@\2</a>', $m);
            $m = preg_replace('/(^|[^\w\d]+)#([^\s.,!?]+)/', '\1<a href="'.$search_base.'\2">#\2</a>', $m);
            if ($group_base) $m = preg_replace('/(^|[^\w\d]+)!([^\s.,!?]+)/', '\1<a href="'.$group_base.'\2">!\2</a>', $m);
        }

        $final = $m;
        $final .= ' <span class="statusnet-timestamp"><abbr title="'.date(__('Y/m/d H:i:s', 'statusnet-widget'), $time).'">';
        $final .= '<a href="'.$message->get_permalink().'">'.$h_time.'</a>';
        $final .= '</abbr></span>';

        return $final;
    }

    function get_cache_lifetime($a) {
        $s = $this->get_settings();
        if (array_key_exists($this->number, $s)) {
            $s = $s[$this->number];
        }
        $cache_lifetime = $s['cache_lifetime'];
        return 60*(int)$cache_lifetime;
    }

}

add_action('widgets_init', create_function('', 'return register_widget("StatusNetWidget");'));
$plugin_dir = basename(dirname(__FILE__));
load_plugin_textdomain( 'statusnet-widget', null, $plugin_dir.'/languages' );
add_action('wp_print_styles', 'add_statusnet_stylesheet');

/*
 * Enqueue style-file, if it exists.
 */

function add_statusnet_stylesheet() {
    $stylesheet = 'statusnet-widget.css';
    $statusnet_style_url  = plugins_url($stylesheet, __FILE__);
    $statusnet_style_file = dirname(__FILE__) . '/' . $stylesheet;
    if ( file_exists($statusnet_style_file) ) {
        wp_register_style('statusnet-widget-style', $statusnet_style_url);
        wp_enqueue_style('statusnet-widget-style');
    }
}

?>

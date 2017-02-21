<?php

/*
Plugin Name: Foundation Top Bar Navigation Menu
Description: Display a WordPress navigation menu or page list as a responsive Foundation top bar.
Version: 1.0
Author: Jethin
License: GPL2
*/

register_nav_menu( 'topbar', 'Top Bar' );

function ftb_settings(){
	
	register_setting( 'general','ftb_load','intval' );
	
	add_settings_section( 'ftb_settings', 'Foundation Top Bar', '', 'general' );

	add_settings_field( 'ftb_load', 'Load Foundation Basic in Theme', 'ftb_settings_callback', 'general', 'ftb_settings', array( 'label_for' => 'ftb_load' ) );
	
	function ftb_settings_callback($args) { 
		$ftb_option = get_option('ftb_load');
		$selected = empty( $ftb_option ) ? array(' selected','') : array('',' selected');
		echo '<select id="ftb_load" name="ftb_load"><option value="0"' . $selected[0] . '>False</option><option value="1"' . $selected[1] . '>True</option></select>';
	}

}

add_action( 'admin_init', 'ftb_settings' );

function ftb_enqueue_scripts() {
	$ftb_load = get_option('ftb_load');
	if( $ftb_load == 1 ){
		$foundation_enqueued = wp_script_is( 'foundation', $list = 'enqueued' );
		if( $foundation_enqueued == false ){
			
			$foundation_path = plugins_url( 'foundation/', __FILE__ );
			
			wp_register_style( 'foundation', $foundation_path . 'css/ftb.foundation.topbar.min.css', '', '5.5.2', 'screen,handheld' );
			wp_enqueue_style( 'foundation' );
			
			wp_register_script( 'foundation', $foundation_path . 'js/foundation/foundation.js', array('jquery'), '5.5.2', true );
			wp_enqueue_script( 'foundation' );
			
			wp_register_script( 'foundation_topbar', $foundation_path . 'js/foundation/foundation.topbar.js', array('foundation'), '5.5.2', true );
			wp_enqueue_script( 'foundation_topbar' );
			
			add_action( 'wp_footer', 'ftb_footer', 20 );

		}
	}
}
add_action( 'wp_enqueue_scripts', 'ftb_enqueue_scripts', 20 );

function ftb_footer() {
	echo '<script>jQuery(document).foundation();</script>';
}
// add_action( 'wp_footer', 'ftb_footer', 20 );

// ftb primary class -- outputs html
class ftb_menu extends Walker_Nav_Menu{
	
	private $no_wrap;

	function __construct($no_wrap = ''){
		$this->no_wrap = $no_wrap;
		if( empty($this->no_wrap) ){ $this->top_bar_html_open(); }
	}
	
	function top_bar_html_open(){
		print '
	<nav class="top-bar" data-topbar role="navigation">
		<ul class="title-area">
			<li class="name"></li>
			<li class="toggle-topbar menu-icon"><a href="#"><span>Menu</span></a></li>
		</ul>
		<section class="top-bar-section">';
	}
 
	function start_lvl(&$output, $depth=0){
		$indent = $depth > 0 ? str_repeat("\t", $depth) : '';
		$output .= "\n" . $indent . '<ul class="dropdown">' . "\n";
	}
	
	function end_lvl(&$output, $depth=0){
		$indent = str_repeat("\t", $depth);
        $output .= $indent . "</ul>\n";
    }
	
	function start_el(&$output, $item, $depth=0, $args=array(), $ftb_classes = ''){
		$title = !empty($item->title) ? esc_attr($item->title) : esc_attr($item->post_title);
		$url = !empty($item->url) ? $item->url : get_permalink($item->ID);
		$classes = 'menu-item-' . $item->ID;
		if( !empty($item->classes) ){
			$classes .= in_array('menu-item-has-children',$item->classes) ? ' has-dropdown' : '';
			$classes .= in_array('current-menu-item',$item->classes) ? ' active' : '';
		}
		// classes passed ftb_pages walker
		$classes .= !empty($ftb_classes) ? $ftb_classes : '';
		
		$class_att = !empty($classes) ? ' class="' . trim($classes) . '"' : '';
		$indent = $depth > 0 ? str_repeat("\t", $depth) : '';
        $output .= $indent . "<li$class_att><a href=\"$url\">$title</a>";
    }
	
	function end_el(&$output, $item){
        $output .= "</li>\n";
    }
	
	function top_bar_html_close(){
		print '	</section>
	</nav>';
	}
	
	function __destruct(){
		if( empty($this->no_wrap) ){ $this->top_bar_html_close(); }
	}
}

class ftb_pages extends Walker_Page{
	
	private $no_wrap;

	function __construct($no_wrap = ''){
		// $this->db_fields = array('parent' => 'post_parent', 'id' => 'ID'); // set db schema to pages in Walker_Nav_Menu
		$this->no_wrap = $no_wrap;
		if( empty($no_wrap) ){ ftb_menu::top_bar_html_open(); }
		echo '<ul class="menu">' . "\n";
	}

	function start_lvl(&$output, $depth=0){
		ftb_menu::start_lvl($output, $depth=0);
	}
	
	function end_lvl(&$output, $depth=0){
		ftb_menu::end_lvl($output, $depth=0);
    }
	
	function start_el(&$output, $item, $depth=0, $args=array()){
		$current_page_id = get_the_ID();
		$children = get_pages( 'child_of=' . $item->ID );
		$ftb_classes = count( $children ) != 0 ? ' has-dropdown' : '';
		$ftb_classes .= $current_page_id == $item->ID ? ' active' : '';
		ftb_menu::start_el($output, $item, $depth=0, $args, $ftb_classes);
    }
	
	function end_el(&$output, $item){
        ftb_menu::end_el($output, $item);
    }
	
	function __destruct(){
		print '</ul>';  
		if( empty($this->no_wrap) ){ ftb_menu::top_bar_html_close(); }
	}
}

?>
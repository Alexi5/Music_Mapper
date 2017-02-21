<?php
/***************************************************************************
 *  					Theme Functions
 * 	----------------------------------------------------------------------
 * 						DO NOT EDIT THIS FILE
 *	----------------------------------------------------------------------
 *
 *  					Copyright (C) Themify
 * 						http://themify.me
 *
 *  To add custom PHP functions to the theme, create a new 'custom-functions.php' file in the theme folder.
 *  They will be added to the theme automatically.
 *
 ***************************************************************************/

/////// Actions ////////
// Init post, page and additional post types if they exist
add_action( 'after_setup_theme', 'themify_theme_after_setup_theme' );

// Enqueue scripts and styles required by theme
add_action( 'wp_enqueue_scripts', 'themify_theme_enqueue_scripts', 11 );

// Browser compatibility
add_action( 'wp_head', 'themify_ie_enhancements' );
add_action( 'wp_head', 'themify_viewport_tag' );
add_action( 'wp_head', 'themify_ie_standards_compliant' );

if ( themify_get( 'setting-disable_responsive_design' ) == 'on' ) {
	add_action( 'init', 'themify_disable_responsive_design' );
}

// Register custom menu
add_action( 'init', 'themify_register_custom_nav' );

// Register sidebars
add_action( 'widgets_init', 'themify_theme_register_sidebars' );

/////// Filters ////////

/**
 * Enqueue Stylesheets and Scripts
 * @since 1.0.0
 */
function themify_theme_enqueue_scripts(){
	global $themify;

	// Get theme version for Themify theme scripts and styles
	$theme_version = wp_get_theme()->display( 'Version' );

	///////////////////
	// Enqueue styles
	///////////////////

	// Themify base styling
	wp_enqueue_style( 'theme-style', get_stylesheet_uri(), array(), $theme_version);

	// Themify Media Queries CSS
	wp_enqueue_style( 'themify-media-queries', THEME_URI . '/media-queries.css', array(), $theme_version);

        // Themify Icons
	wp_enqueue_style('themify-icons', THEMIFY_URI . '/themify-icons/themify-icons.css', array(), THEMIFY_VERSION);

	// User stylesheet
	if ( is_file( TEMPLATEPATH . '/custom_style.css' ) ) {
		wp_enqueue_style( 'custom-style', THEME_URI . '/custom_style.css' );
	}

	// Google Web Fonts embedding
	wp_enqueue_style( 'google-fonts', themify_https_esc( '//fonts.googleapis.com/css' ). '?family=Poppins:300,400,500,600,700|Open+Sans:400,600,700' );

	///////////////////
	// Enqueue scripts
	///////////////////

	// Slide mobile navigation menu
	wp_enqueue_script( 'slide-nav', THEMIFY_URI . '/js/themify.sidemenu.js', array( 'jquery' ), THEMIFY_VERSION, true );

	// Themify internal scripts
	wp_enqueue_script( 'theme-script',	THEME_URI . '/js/themify.script.js', array('jquery'), $theme_version, false );

        // Prepare JS variables
	$themify_script_vars = array(
		'lightbox' => themify_lightbox_vars_init(),
		'lightboxContext' => apply_filters( 'themify_lightbox_context', '#pagewrap' )
	);

	// Pass variable values to JavaScript
	wp_localize_script( 'theme-script', 'themifyScript', apply_filters( 'themify_script_vars', $themify_script_vars ));

	// WordPress internal script to move the comment box to the right place when replying to a user
	if ( is_single() || is_page() ) wp_enqueue_script( 'comment-reply' );
}

/**
 * Add JavaScript files if IE version is lower than 9
 * @since 1.0.0
 */
function themify_ie_enhancements() {
	echo "\n".'
	<!-- media-queries.js -->
	<!--[if lt IE 9]>
		<script src="' . THEME_URI . '/js/respond.js"></script>
	<![endif]-->

	<!-- html5.js -->
	<!--[if lt IE 9]>
		<script src="'.themify_https_esc( 'http://html5shim.googlecode.com/svn/trunk/html5.js' ).'"></script>
	<![endif]-->
	'."\n";
}

/**
 * Add viewport tag for responsive layouts
 * @since 1.0.0
 */
function themify_viewport_tag() {
	echo "\n".'<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">'."\n";
}

/**
 * Make IE behave like a standards-compliant browser
 * @since 1.0.0
 */
function themify_ie_standards_compliant() {
	echo "\n".'
	<!--[if lt IE 9]>
	<script src="'.themify_https_esc( 'http://s3.amazonaws.com/nwapi/nwmatcher/nwmatcher-1.2.5-min.js' ).'"></script>
	<script type="text/javascript" src="'.themify_https_esc( 'http://cdnjs.cloudflare.com/ajax/libs/selectivizr/1.0.2/selectivizr-min.js' ).'"></script>
	<![endif]-->
	'."\n";
}

if ( ! function_exists( 'themify_theme_after_setup_theme' ) ) {
	/**
	 * Register theme support.
	 *
	 * Initialize custom panel with its definitions.
	 * Custom panel definitions are located in admin/post-type-TYPE.php
	 *
	 * @since 1.0.7
	 */
	function themify_theme_after_setup_theme() {
		// Enable WordPress feature image
		add_theme_support( 'post-thumbnails' );

		// Load required files for post, page and custom post types where it applies
		foreach ( array( 'post', 'page' ) as $type ) {
			require_once( "admin/post-type-$type.php" );
		}
		/**
		 * Navigation menus used in page custom panel to specify a custom menu for the page.
		 * @since 1.0.0
		 * @var array
		 */
		$nav_menus = array(	array( 'name' => '', 'value' => '', 'selected' => true ) );
		foreach ( get_terms( 'nav_menu' ) as $menu ) {
			$nav_menus[] = array( 'name' => $menu->name, 'value' => $menu->slug );
		}

		themify_build_write_panels( apply_filters( 'themify_theme_meta_boxes',
			array(
				array(
					'name'		=> __( 'Post Options', 'themify' ),
					'id'		=> 'post-options',
					'options'	=> themify_theme_post_meta_box(),
					'pages'		=> 'post'
				),
				array(
					'name'		=> __( 'Page Options', 'themify' ),
					'id'		=> 'page-options',
					'options'	=> themify_theme_page_meta_box( array(
						'nav_menus' => $nav_menus,
					) ),
					'pages'		=> 'page'
				),
                               array(
                                        'name'    => __( 'Post Appearance', 'themify' ),
                                        'id'      => 'post-appearance',
                                        'options' => themify_theme_post_appearance_meta_box(),
                                        'pages'   => 'post'
                                ),
                                array(
                                        'name'    => __( 'Page Appearance', 'themify' ),
                                        'id'      => 'page-appearance',
                                        'options' => themify_theme_page_appearance_meta_box(),
                                        'pages'   => 'page'
                                ),
				array(
					'name'		=> __( 'Query Posts', 'themify' ),
					'id'		=> 'query-posts',
					'options'	=> themify_theme_query_post_meta_box(),
					'pages'		=> 'page'
				),
			)
		));
	}
}

if ( ! function_exists( 'themify_register_custom_nav' ) ) {
	/**
	 * Register Custom Menu Function
	 * @since 1.0.0
	 */
	function themify_register_custom_nav() {
		register_nav_menus( array(
			'main-nav' => __( 'Main Navigation', 'themify' ),
			'footer-nav' => __( 'Footer Navigation', 'themify' ),
		));
	}
}

if ( ! function_exists( 'themify_default_main_nav' ) ) {
	/**
	 * Default Main Nav Function
	 * @since 1.0.0
	 */
	function themify_default_main_nav() {
		echo '<ul id="main-nav" class="main-nav clearfix">';
			wp_list_pages( 'title_li=' );
		echo '</ul>';
	}
}

if ( ! function_exists( 'themify_theme_menu_nav' ) ) {
	/**
	 * Sets custom menu selected in page custom panel as navigation, otherwise sets the default.
	 *
	 * @since 1.0.0
	 */
	function themify_theme_menu_nav() {
		$args = array(
			'theme_location' => 'main-nav',
			'fallback_cb'    => 'themify_default_main_nav',
			'container'      => '',
			'menu_id'        => 'main-nav',
			'menu_class'     => 'main-nav'
		);
		// Get entry ID reliably
		$queried_object = get_queried_object();
		$entry_id = isset( $queried_object->ID ) ? $queried_object->ID : 0;

		// Compile menu arguments
		$args = wp_parse_args( $args, array(
			'theme_location' => 'main-nav',
			'fallback_cb' => 'themify_default_main_nav',
			'container'   => '',
			'menu_id'     => 'main-nav',
			'menu_class'  => 'main-nav'
		));

		// See if the page has a menu assigned
		$custom_menu = get_post_meta( $entry_id, 'custom_menu', true );
		if ( ! empty( $custom_menu ) ) {
			$args['menu'] = $custom_menu;
		}

		// Render the menu
		wp_nav_menu( $args );
	}
}


if ( ! function_exists( 'themify_term_description' ) ) {

	function themify_term_description( $taxonomy = 'category' ) {
            $term_description = term_description( 0, $taxonomy );
            $output = ! empty( $term_description ) ?'<p class="category-description">' . strip_tags($term_description). '</p>':'';
            return apply_filters( 'themify_get_term_description', $output );
	}
}

if ( ! function_exists( 'themify_theme_register_sidebars' ) ) {
	/**
	 * Register sidebars
	 * @since 1.0.0
	 */
	function themify_theme_register_sidebars() {
		$sidebars = array(
			array(
				'name' => __( 'Sidebar', 'themify' ),
				'id' => 'sidebar-main',
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget' => '</div>',
				'before_title' => '<h4 class="widgettitle">',
				'after_title' => '</h4>',
			),
			array(
				'name' => __( 'Social Widget', 'themify' ),
				'id' => 'social-widget',
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget' => '</div>',
				'before_title' => '<strong>',
				'after_title' => '</strong>',
			),
                        array(
				'name' => __('Footer Social Widget', 'themify'),
				'id' => 'footer-social-widget',
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget' => '</div>',
				'before_title' => '<strong class="widgettitle">',
				'after_title' => '</strong>',
			)
		);
		foreach( $sidebars as $sidebar ) {
			register_sidebar( $sidebar );
		}

		// Footer Sidebars
		themify_register_grouped_widgets();
	}
}

if ( ! function_exists( '_wp_render_title_tag' ) ) {
	/**
	 * Fallback to render title before WP 4.1
	 *
	 * @since 1.0.0
	 */
	function themify_theme_render_title() { ?>
		<title><?php wp_title(); ?></title>
	<?php
	}
	// Fallback WP Title
	add_action( 'wp_head', 'themify_theme_render_title' );
} else {
	// Add Title Tag support
	add_theme_support( 'title-tag' );
	/**
	 * Remove last part of title to keep the traditional Themify theme title.
	 *
	 * @since 1.0.0
	 *
	 * @param string $title
	 *
	 * @return string
	 */
	function themify_theme_title_tag( $title ) {
		if ( ! is_front_page() ) {
			$title = str_replace( get_bloginfo( 'name' ), '', $title );
		}
		return $title;
	}
	// Generate title
	add_filter( 'wp_title', 'themify_theme_title_tag', 10 );
}

if ( ! function_exists( 'themify_theme_comment' ) ) {
	/**
	 * Custom Theme Comment
	 *
	 * @since 1.0.0
	 *
	 * @param object $comment Current comment.
	 * @param array $args Parameters for comment reply link.
	 * @param int $depth Maximum comment nesting depth.
	 */
	function themify_theme_comment($comment, $args, $depth) {
	   $GLOBALS['comment'] = $comment; ?>

		<li id="comment-<?php comment_ID() ?>">
			<p class="comment-author">
				<?php echo get_avatar( $comment, $size = '48' ); ?>
				<cite <?php comment_class(); ?>><span <?php comment_class(); ?>><?php echo get_comment_author_link(); ?></span></cite>
				<br />
				<small class="comment-time">
					<?php comment_date( apply_filters( 'themify_comment_date', '' ) ); ?>
					 @
					<?php comment_time( apply_filters( 'themify_comment_time', '' ) ); ?>
					<?php edit_comment_link( __( 'Edit', 'themify' ),' [',']' ); ?>
				</small>
			</p>
			<div class="commententry">
				<?php if ($comment->comment_approved == '0' ) : ?>
					<p><em><?php _e( 'Your comment is awaiting moderation.', 'themify' ) ?></em></p>
				<?php endif; ?>
				<?php comment_text(); ?>
			</div>
			<p class="reply">
				<?php comment_reply_link(array_merge( $args, array( 'add_below' => 'comment', 'depth' => $depth, 'reply_text' => __( 'Reply', 'themify' ), 'max_depth' => $args['max_depth']))) ?>
			</p>
		<?php
	}
}


/**
 * Set the fixed-header selector for the scroll highlight script
 *
 * @since 1.1.3
 */
function themify_theme_scroll_highlight_vars( $vars ) {
	$vars['fixedHeaderSelector'] = '#headerwrap.fixed-header';
	return $vars;
}
add_filter( 'themify_builder_scroll_highlight_vars', 'themify_theme_scroll_highlight_vars' );

if ( ! function_exists( 'themify_theme_custom_post_css' ) ) {
	/**
	 * Outputs custom post CSS at the end of a post
	 * @since 1.0.0
	 */
	function themify_theme_custom_post_css() {
		global $themify;
		if (!is_category() && in_array( get_post_type(), array( 'post', 'page') ) ) {
			$post_id = get_the_ID();
                        $entry_id = is_page() ?'.page-id-':'.postid-' ;
                        $entry_id.= $post_id;
			$headerwrap = $entry_id . ' #headerwrap';
			$site_logo = $entry_id . ' #site-logo';
			$site_description = $entry_id . ' #site-description';
			$main_nav = $entry_id . ' #main-nav';
			$social_widget = $entry_id . ' .social-widget';
			$css = array();
			$style = '';
			$rules = array();

			if ( 'transparent' != themify_get( 'header_wrap' ) ) {
				$rules = array(
					$headerwrap => array(
						array(
							'prop' => 'background-color',
							'key'  => 'background_color'
						),
						array(
							'prop' => 'background-image',
							'key'  => 'background_image'
						),
						array(
							'prop' => 'background-repeat',
							'key'  => 'background_repeat',
							'dependson' => array(
								'prop' => 'background-image',
								'key'  => 'background_image'
							),
						),
					),
					"$entry_id #site-logo span:after, $entry_id #headerwrap #searchform, $entry_id #main-nav .current_page_item a, $entry_id #main-nav .current-menu-item a" => array(
							array(
								'prop' => 'border-color',
								'key'  => 'headerwrap_text_color'
							),
					),
				);
			}

			$rules["$headerwrap, $site_logo, $site_description"] = array(
				array(
					'prop' => 'color',
					'key'  => 'headerwrap_text_color'
				),
			);

			$rules["$site_logo a, $site_description a, $social_widget a, $main_nav > li > a"] = array(
				array(
					'prop' => 'color',
					'key'  => 'headerwrap_link_color'
				),
			);

			if ( is_singular( array( 'portfolio', 'event' ) ) ) {
				$rules['.postid-' . $post_id . ' .featured-area'] =	array(
					array(	'prop' => 'background-color',
							'key' => 'featured_area_background_color'
					),
					array(	'prop' => 'background-image',
							'key' => 'featured_area_background_image'
					),
					array(	'prop' => 'background-repeat',
							'key' => 'featured_area_background_repeat',
							'dependson' => array(
								'prop' => 'background-image',
								'key'  => 'featured_area_background_image'
							),
					),
				);
				$rules['.postid-' . $post_id . ' .portfolio-post-wrap, .postid-' . $post_id . ' .portfolio-post-wrap .post-date'] = array(
					array(	'prop' => 'color',
							'key' => 'featured_area_text_color'
					),
				);
				$rules['.postid-' . $post_id . ' .portfolio-post-wrap a'] =	array(
					array(	'prop' => 'color',
							'key' => 'featured_area_link_color'
					),
				);
			}

			foreach ( $rules as $selector => $property ) {
				foreach ( $property as $val ) {
					$prop = $val['prop'];
					$key = $val['key'];
					if ( is_array( $key ) ) {
						if ( $prop == 'font-size' && themify_check( $key[0] ) ) {
							$css[$selector][$prop] = $prop . ': ' . themify_get( $key[0] ) . themify_get( $key[1] );
						}
					} elseif ( themify_check( $key ) && 'default' != themify_get( $key ) ) {
						if ( $prop == 'color' || stripos( $prop, 'color' ) ) {
							$css[$selector][$prop] = $prop . ': #' . themify_get( $key );
						}
						elseif ( $prop == 'background-image' && 'default' != themify_get( $key ) ) {
							$css[$selector][$prop] = $prop .': url(' . themify_get( $key ) . ')';
						}
						elseif ( $prop == 'background-repeat' && 'fullcover' == themify_get( $key ) ) {
							if ( isset( $val['dependson'] ) ) {
								if ( $val['dependson']['prop'] == 'background-image' && ( themify_check( $val['dependson']['key'] ) && 'default' != themify_get( $val['dependson']['key'] ) ) ) {
									$css[$selector]['background-size'] = 'background-size: cover';
								}
							} else {
								$css[$selector]['background-size'] = 'background-size: cover';
							}
						}
						elseif ( $prop == 'font-family' ) {
							$font = themify_get( $key );
							$css[$selector][$prop] = $prop .': '. $font;
							if ( ! in_array( $font, themify_get_web_safe_font_list( true ) ) ) {
								$themify->google_fonts .= str_replace( ' ', '+', $font.'|' );
							}
						}
						else {
							$css[$selector][$prop] = $prop .': '. themify_get( $key );
						}
					}
				}
				if ( ! empty( $css[$selector] ) ) {
					$style .= "$selector {\n\t" . implode( ";\n\t", $css[$selector] ) . "\n}\n";
				}
			}
                        if(is_page()){

                            $img = themify_get('page_title_background_image');
                            if($img){
                                $style.= '.page-category-title-wrap{background:url("'.esc_url($img).'") no-repeat;background-size: cover;}';
                            }
                            $color = themify_get('page_title_background_color');
                            if($color){
                                $style.= '.category-title-overlay{background-color:#'.$color.';}';
                            }
                        }
			if ( '' != $style ) {
				echo "\n<!-- Entry Style -->\n<style>\n$style</style>\n<!-- End Entry Style -->\n";
			}
		}
                elseif(is_category()){
                    $categories = get_category(get_query_var('cat'));
                    $category_id = $categories->cat_ID;
                    $category_meta = get_option( 'themify_category_bg' );
                    if(isset($category_meta[$category_id])){
                        $style = '';
                        if(isset($category_meta[$category_id]['image']) && $category_meta[$category_id]['image']){
                            $style = '.page-category-title-wrap{background:url("'.esc_url($category_meta[$category_id]['image']).'") no-repeat;background-size: cover;}';
                        }
                        if($category_meta[$category_id]['color'] && $category_meta[$category_id]['color']){
                            $style.= '.category-title-overlay{background-color:#'.$category_meta[$category_id]['color'].';}';
                        }
                        if($style){
                            echo "\n<!-- Entry Style -->\n<style>\n$style</style>\n<!-- End Entry Style -->\n";
                        }
                    }
                }
	}
	add_action( 'wp_head', 'themify_theme_custom_post_css', 77 );
}



if ( ! function_exists( 'themify_theme_body_class' ) ) {
	/**
	 * Adds body classes for special theme features.
	 *
	 * @param $classes
	 *
	 * @return array
	 */
	function themify_theme_body_class( $classes ) {
		// Add transparent-header class to body if user selected it in custom panel
		if ( ( is_single() || is_page() ) && 'transparent' == themify_get( 'header_wrap' ) ) {
			$classes[] = 'transparent-header';
		}
		// Header Design
		$header = themify_area_design( 'header' );
		$classes[] = 'none' == $header ? 'header-none' : $header;

		return $classes;
	}
        add_filter( 'body_class', 'themify_theme_body_class', 99 );
}

//Change comments fields position
add_filter('comment_form_fields', 'themify_reorder_comment_fields' );
function themify_reorder_comment_fields($fields ){
    $new_fields = array();
    $order = array('author','email','url','comment');
    foreach( $order as $key ){
            $new_fields[ $key ] = $fields[ $key ];
            unset( $fields[ $key ] );
    }
    if( $fields ){
        foreach( $fields as $key => $val ){
                $new_fields[ $key ] = $val;
        }
    }
    return $new_fields;
}

add_action( 'edit_category_form_fields', 'themify_category_custom_fields' );
add_action( 'edit_category', 'themify_save_custom_fields' );
function themify_category_custom_fields( $tag ) {
    wp_enqueue_media();
    themify_enqueue_scripts('post.php');
    $category_meta = get_option( 'themify_category_bg' );
    $post = get_posts(array('posts_per_page'=>1));
    $post = current($post);
    $img = isset($category_meta[$tag->term_id]) && isset($category_meta[$tag->term_id]['image']) && $category_meta[$tag->term_id]['image'];

    ?>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="category-bg"><?php _e("Background",'themify'); ?></label></th>
        <td>
            <div id="themify_builder_alert" class="alert"></div>
             <div class="themify_field_row clearfix  hide-if none">
                <div class="themify_field themify_field-color" style="width:auto;">
                    <span class="colorSelect"></span>
                    <input style="height: 33px;" type="text"  id="category-bg" name="category_meta[<?php echo $tag->term_id ?>][color]" value="<?php if ( isset( $category_meta[ $tag->term_id ] ) ) esc_attr_e( $category_meta[ $tag->term_id ]['color'] ); ?>"  class="themify_input_field colorSelectInput"/>
                    <input type="button" class="button clearColor" value="×"/>
                    &nbsp;&nbsp;<?php _e("image",'themify'); ?>&nbsp;&nbsp;
                </div>
                <div class="themify_field" style="width:25%;margin-top: 7px;">
                    <div id="remove-themify_category_image" class="themify_featimg_remove <?php if(!$img):?>hide<?php endif;?>">
                        <a href="#"><?php _e("Remove image",'themify'); ?></a>
                    </div>
                    <div class="themify_upload_preview"<?php if($img):?> style="display:block;"<?php endif;?>>
                        <?php if($img):?>
                            <a href="<?php echo esc_url_raw($category_meta[$tag->term_id]['image'])?>" target="_blank">
                                <img src="<?php echo esc_url_raw($category_meta[$tag->term_id]['image'])?>" width="40" />
                            </a>
                        <?php endif;?>
                    </div>
                    <input type="hidden" id="themify_category_image" name="category_meta[<?php echo $tag->term_id ?>][image]" value="<?php echo $img?esc_url_raw($category_meta[$tag->term_id]['image']):''?>" class="themify_input_field themify_upload_field" />
                    <div class="themify_upload_buttons">
                        <?php themify_uploader('themify_category_image',array('preview'=>true,'tomedia'=>true,'medialib'=>true,'type'=>'image','fields'=>'themify_category_image','topost'=>$post->ID));?>
                    </div>
                </div>
             </div>
            <script type="text/javascript">
                    jQuery(function($){
                            var $remove = $('#remove-themify_category_image');
                            $remove.find('a').on('click', function(e){
                                    e.preventDefault();
                                    var $parent = $(this).parent().parent();
                                    $parent.find('.themify_upload_field').val('');
                                    $parent.find('.themify_upload_preview').fadeOut();
                                    $remove.addClass('hide');
                            });
                    });
            </script>
        </td>
    </tr>
    <?php
}

function themify_save_custom_fields(){

    if ( isset( $_POST['category_meta'] )){
        $id = key($_POST['category_meta']);
        $category_meta = get_option( 'themify_category_bg' );
        $category_meta[$id] =  $_POST['category_meta'][$id];
        if(!update_option('themify_category_bg', $category_meta)){
             add_option('themify_category_bg', $_POST['category_meta']);
        }
    }
}
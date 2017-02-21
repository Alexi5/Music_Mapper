<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<?php
/** Themify Default Variables
 *  @var object */
global $themify; ?>
<meta charset="<?php bloginfo( 'charset' ); ?>">

<?php
/**
 * Document title is generated in theme-functions.php
 * Stylesheets and Javascript files are enqueued in theme-functions.php
 */
?>

<!-- wp_header -->
<?php wp_head(); ?>
<!--[if lt IE 9]>
	<script src="js/respond.js"></script>

	<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>

	<script src="http://s3.amazonaws.com/nwapi/nwmatcher/nwmatcher-1.2.5-min.js"></script>
	<script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/selectivizr/1.0.2/selectivizr-min.js"></script>
<![endif]-->

</head>

<body <?php body_class(); ?>>

	<?php themify_body_start(); // hook ?>

	<div id="pagewrap" class="hfeed site">

		<div id="headerwrap">

			<?php themify_header_before(); // hook ?>

			<header id="header" class="pagewidth clearfix" itemscope="itemscope" itemtype="https://schema.org/WPHeader">

				<?php themify_header_start(); // hook ?>

				<div id="logo-wrap">
					<?php echo themify_logo_image(); ?>
					<?php echo themify_site_description(); ?>
				</div>

				<a id="menu-icon" href="#mobile-menu"></a>

				<div id="mobile-menu" class="sidemenu sidemenu-off">
					<div class="social-wrap">
						<div class="social-widget">
								<?php dynamic_sidebar('social-widget'); ?>

								<?php if ( ! themify_check('setting-exclude_rss' ) ) : ?>
										<div class="rss"><a href="<?php themify_theme_feed_link(); ?>"></a></div>
								<?php endif; ?>
						</div>
						<!-- /.social-widget -->
						<?php if ( ! themify_check( 'setting-exclude_search_form' ) ) : ?>
								<?php get_search_form(); ?>
						<?php endif; ?>
                    </div>
					<nav id="main-nav-wrap" itemscope="itemscope" itemtype="https://schema.org/SiteNavigationElement">
						<?php themify_theme_menu_nav(); ?>
						<!-- /#main-nav -->
					</nav>


					<a id="menu-icon-close" href="#mobile-menu"></a>

				</div>
				<!-- /#mobile-menu -->

				<?php themify_header_end(); // hook ?>

			</header>
			<!-- /#header -->

			<?php themify_header_after(); // hook ?>

		</div>
		<!-- /#headerwrap -->

		<div id="body" class="clearfix">

			<?php themify_layout_before(); //hook ?>

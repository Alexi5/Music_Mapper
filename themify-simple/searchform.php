<?php
/**
 * Template for search form.
 * @package themify
 * @since 1.0.0
 */
?>
<form method="get" id="searchform" action="<?php echo home_url(); ?>/">

	<i class="icon-search"></i>

	<input type="text" name="s" id="s" placeholder="<?php esc_attr_e( 'Search', 'themify' ); ?>" />

</form>
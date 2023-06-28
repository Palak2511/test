<?php
/**
 * Demo Theme search Form template
 *
 * @package WordPress
 * @subpackage Demo Theme
 * @since Demo Theme 1.0
 */
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
    <input type="search" id="search" placeholder="Search" class="search-field" value="<?php echo get_search_query(); ?>" name="s" />
    <input type="submit" class="search-submit" />
</form>

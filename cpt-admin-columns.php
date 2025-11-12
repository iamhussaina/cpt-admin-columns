<?php
/**
 * Hussainas CPT Admin Column Utility
 *
 * This utility provides a configurable framework for adding, rendering,
 * and sorting custom columns in the WordPress admin list table for
 * specified Custom Post Types.
 *
 * @version     1.0.0
 * @author      Hussain Ahmed Shrabon
 * @license     MIT
 * @link        https://github.com/iamhussaina
 * @textdomain  hussainas
 */

// Prevent direct file access for security.
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

/**
 * Registers all hooks required for the CPT admin columns.
 *
 * This function acts as the main controller, reading the configuration
 * and applying the necessary filters and actions.
 *
 * @since 1.0.0
 */
function hussainas_cpt_columns_init() {
	/**
	 * Define the Custom Post Type slug you want to modify.
	 *
	 * @example 'portfolio'
	 * @example 'product'
	 */
	$cpt_slug = 'product';

	// Ensure the CPT slug is set before proceeding.
	if ( empty( $cpt_slug ) ) {
		return;
	}

	// 1. Filter: Add new column headers.
	add_filter( "manage_{$cpt_slug}_posts_columns", 'hussainas_add_cpt_column_headers' );

	// 2. Action: Render the content for the new columns.
	add_action( "manage_{$cpt_slug}_posts_custom_column", 'hussainas_render_cpt_column_content', 10, 2 );

	// 3. Filter: Make the new columns sortable.
	add_filter( "manage_edit-{$cpt_slug}_sortable_columns", 'hussainas_make_cpt_columns_sortable' );

	// 4. Action: Handle the actual sorting logic when a column header is clicked.
	add_action( 'pre_get_posts', 'hussainas_handle_cpt_column_sorting' );
}
add_action( 'admin_init', 'hussainas_cpt_columns_init' );

/**
 * Adds the custom column headers to the CPT admin list table.
 *
 * Fired by the `manage_{$cpt_slug}_posts_columns` filter.
 *
 * @since 1.0.0
 * @param array $columns The existing array of columns.
 * @return array The modified array of columns.
 */
function hussainas_add_cpt_column_headers( $columns ) {
	// Add your custom columns here.
	// Format: 'column_id' => 'Column Label'
	$custom_columns = [
		'hussainas_price'          => __( 'Price', 'hussainas' ),
	];

	// This simple merge adds new columns to the end.
	// For reordering, a more complex array manipulation (e.g., array_slice)
	// would be needed, but merging is cleanest for a utility.
	return array_merge( $columns, $custom_columns );
}

/**
 * Renders the content for the custom columns.
 *
 * Fired by the `manage_{$cpt_slug}_posts_custom_column` action.
 *
 * @since 1.0.0
 * @param string $column_id The ID of the column being rendered (defined in the function above).
 * @param int    $post_id   The ID of the current post.
 */
function hussainas_render_cpt_column_content( $column_id, $post_id ) {
	// Use a switch statement to handle each custom column ID.
	switch ( $column_id ) {

		/**
		 * Case: Price Column (from a meta field)
		 */
		case 'hussainas_price':
			$price_meta_key = '_price'; // Change this to your meta key (e.g., '_product_price')
			$currency_symbol = '$';   // Change this to your currency symbol

			$price = get_post_meta( $post_id, $price_meta_key, true );
			
			if ( ! empty( $price ) && is_numeric( $price ) ) {
				// Format the price as currency.
				echo esc_html( $currency_symbol . number_format( (float) $price, 2 ) );
			} else {
				// Display a dash if no price is set or it's not numeric.
				echo '—';
			}
			break;

		/**
		 * Add more cases here if you add more columns.
		 * * @example
		 * case 'hussainas_sku':
		 * $sku = get_post_meta( $post_id, '_sku', true );
		 * echo esc_html( $sku );
		 * break;
		 */
	}
}

/**
 * Registers the custom columns as sortable.
 *
 * Fired by the `manage_edit-{$cpt_slug}_sortable_columns` filter.
 *
 * @since 1.0.0
 * @param array $sortable_columns The array of existing sortable columns.
 * @return array The modified array.
 */
function hussainas_make_cpt_columns_sortable( $sortable_columns ) {
	/**
	 * Define which columns are sortable.
	 * Format: 'column_id' => 'orderby_value'
	 *
	 * 'orderby_value' is often the meta key.
	 * Using the column ID itself ('hussainas_price') is standard practice,
	 * as the 'pre_get_posts' hook will translate it.
	 */
	$sortable_columns['hussainas_price'] = 'hussainas_price';

	// Note: Sorting by featured image is complex (it's a post relationship)
	// and generally not recommended as it's not a simple meta value.
	
	return $sortable_columns;
}

/**
 * Handles the sorting logic when a custom column is clicked.
 *
 * This function intercepts the main WordPress query in the admin
 * and modifies it to sort by our custom meta field.
 *
 * Fired by the 'pre_get_posts' action.
 *
 * @since 1.0.0
 * @param WP_Query $query The main WP_Query object (passed by reference).
 */
function hussainas_handle_cpt_column_sorting( $query ) {
	// Security checks:
	// 1. Only run in the admin area.
	// 2. Only run on the main query.
	// 3. Ensure we are not on an AJAX request.
	if ( ! is_admin() || ! $query->is_main_query() ) {
		return;
	}

	$orderby = $query->get( 'orderby' );

	/**
	 * Handle sorting for the 'Price' column
	 */
	if ( 'hussainas_price' === $orderby ) {
		$price_meta_key = '_price'; // Must match the key used in hussainas_render_cpt_column_content

		$query->set( 'orderby', 'meta_value_num' ); // Sort as a number (crucial)
		$query->set( 'meta_key', $price_meta_key );
	}
	
	/**
	 * Add more 'if' blocks here to handle other sortable columns.
	 *
	 * @example
	 * if ( 'hussainas_sku' === $orderby ) {
	 * $query->set( 'orderby', 'meta_value' ); // Sort as text
	 * $query->set( 'meta_key', '_sku' );
	 * }
	 */
}

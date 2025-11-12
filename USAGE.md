# How to Use This Utility

Follow these simple steps to integrate the CPT Admin Column Utility into your WordPress theme.

## 1. Include the Utility

1.  Copy the `cpt-admin-columns.php` file into your theme's directory. A good location is a dedicated `inc` or `lib` folder.
    * **Example:** `wp-content/themes/my-theme/inc/cpt-admin-columns.php`

2.  Open your theme's `functions.php` file and add the following line to include the utility. Make sure to use `get_template_directory()` to get the correct path.

    ```php
    // Include the CPT Admin Column Utility
    require_once( get_template_directory() . '/inc/cpt-admin-columns.php' );
    ```

## 2. Configure the Utility

Now that the file is included, you need to configure it for your specific Custom Post Type (CPT).

1.  Open the `cpt-admin-columns.php` file.
2.  Follow the steps.

### Configuration Points

#### A. Set Your CPT Slug

Inside the `hussainas_cpt_columns_init()` function:
* Find the `$cpt_slug = 'product';` line.
* Change `'product'` to the slug of your CPT (e.g., `'course'`).

#### B. Add Your Column Headers

Inside the `hussainas_add_cpt_column_headers()` function:
* Modify the `$custom_columns` array.
* Add or remove columns using the format `'column_id' => 'Column Label'`.

#### C. Render Your Column Content

Inside the `hussainas_render_cpt_column_content()` function:
* Add a `case 'your_column_id':` block for each new column you added in the previous step.
* Write the logic to get and display the data (e.g., `get_post_meta()`).
* Follow the examples for `hussainas_featured_image` and `hussainas_price`.

#### D. Make Columns Sortable

1.  Inside `hussainas_make_cpt_columns_sortable()`, add your column ID to the `$sortable_columns` array.
    * **Example:** `$sortable_columns['hussainas_sku'] = 'hussainas_sku';`

2.  Inside `hussainas_handle_cpt_column_sorting()`, add a new `if` block to handle the sorting logic.
    * **Crucial:** Use `meta_value_num` for numbers (like price) and `meta_value` for text (like SKU).
    * **Example:**
        ```php
        if ( 'hussainas_sku' === $orderby ) {
            $query->set( 'orderby', 'meta_value' ); // Sort as text
            $query->set( 'meta_key', '_sku' );
        }
        ```

That's it! Your CPT admin screen will now display your new custom columns.

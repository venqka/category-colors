<?php
/*
Plugin Name: Category coloring
Plugin URI: 
Description: This plugin adds category colors
Author: venqka@shtrak.eu
Version: 1.0
Author URI: shtrak.eu
*/

function cc_admin_enqueue() {

	// Add the color picker css file       
	wp_enqueue_style( 'wp-color-picker' ); 

	wp_register_script( 'cc-backend', plugin_dir_url( __FILE__ ) . 'scripts/cc-backend.js', array( 'jquery', 'wp-color-picker' ), '1.8.0', false );

	wp_enqueue_script( 'cc-backend', plugin_dir_url( __FILE__ ) . 'scripts/cc-backend.js', array( 'jquery', 'wp-color-picker' ), '1.8.0', false );

}
add_action( 'admin_enqueue_scripts', 'cc_admin_enqueue' );

//Add color picker to category
function cc_field_on_add_new_category( $taxonomy ) {

  ?>

    <div>
        <label><?php _e( 'Category Color', 'sh' ); ?></label>
        <input name="category_color" value="" class="cc-color-picker"/>
        <p><?php _e( 'Choose category color', 'cc' ); ?></p>
    </div>

  <?php

}
add_action( 'category_add_form_fields', 'cc_field_on_add_new_category' );

//Add category color picker to edit category screen

function cc_field_on_edit_category( $term ) {

    $color = get_term_meta( $term->term_id, 'category_color', true );

    if( empty( $color ) ) {
    	$color = '#FFF';
    }

  ?>

    <tr class="form-field term-colorpicker-wrap">
        <th scope="row"><label><?php _e( 'Category Color', 'cc' ); ?></label></th>
        <td>
            <input name="category_color" value="<?php echo $color; ?>" class="cc-color-picker" />
          <p><?php _e( 'Choose category color', 'cc' ); ?></p>
        </td>
    </tr>

  <?php


}
add_action( 'category_edit_form_fields', 'cc_field_on_edit_category' );

//Save category color in term meta

function save_cc( $term_id ) {

    if( !empty( $_POST['category_color'] ) ) {
    	update_term_meta( $term_id, 'category_color', sanitize_hex_color( $_POST['category_color'] ) );
    }

}
add_action( 'created_category', 'save_cc' );
add_action( 'edited_category',  'save_cc' );

//Add category color to columns

function cc_column( $columns ) {

	$columns["category-color"] = __( 'Category color', 'cc' );

	return $columns;
}
add_filter( 'manage_edit-category_columns', 'cc_column' );

function add_cc_to_column( $content, $column_name, $term_id ) {

	$cc = get_term_meta( $term_id, 'category_color', true );

	if( $column_name == 'category-color' ) {

		if( !empty( $cc ) ) {
			$content = '<div style="width: 30px; height: 30px; background-color: ' . $cc . '"></div>';
		} else {
			$content = '';
		}
		
	}
	
	return $content;
}
add_filter( 'manage_category_custom_column', 'add_cc_to_column', 10, 3 );
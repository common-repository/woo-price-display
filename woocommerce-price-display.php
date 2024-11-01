<?php
/*
* Plugin Name: Woocommerce Price Display
* Description: Modify the front-end display of Woocommerce product prices without changing the product built-in price attributes. Works for all product types. Tested up to Woocommerce version 6.0.0.
* Version: 0.1.4
* Author: Social Oak Media Inc.
* Author URI: https://socialoak.ca
*/

defined( 'ABSPATH' ) or die();

Class Oak_Woocommerce_Price_Display {

	public function __construct() {

		// include custom pricing options on individual product pages
		add_action( 'woocommerce_product_options_general_product_data', array( $this, 'add_price_option_fields' ) );

		// save custom pricing options on individual product pages
		add_action( 'woocommerce_process_product_meta', array( $this, 'save_price_option_fields' ) );

		// display price customization on the front end
		add_filter( 'woocommerce_get_price_html', array( $this, 'display_price_option_fields' ), 20, 2 );
	}

	/**
	* Function to include custom pricing options on product pages
	* Options will be included in the built in woocommerce General Settings tab
	*
	* @hook 	woocommerce_product_options_general_product_data
	*/
	public function add_price_option_fields() {
		echo "<div class='options_group custom_price_options'>";
			echo "<p class='form-field' style='font-weight: bold;'>Customize Price Output</p>";
			
			// prefix field
			woocommerce_wp_text_input( array(
				'id' => '_oak_price_prefix',
				'value' => get_post_meta( get_the_ID(), '_oak_price_prefix', true ),
				'label' => __( 'Price Prefix', 'woocommerce' ),
				'desc_tip' => true,
				'description' => __( 'Add some text before the price for this product.', 'woocommerce' ),
			) );

			// suffix field
			woocommerce_wp_text_input( array(
				'id' => '_oak_price_suffix',
				'value' => get_post_meta( get_the_ID(), '_oak_price_suffix', true ),
				'label' => __( 'Price Suffix', 'woocommerce' ),
				'desc_tip' => true,
				'description' => __( 'Add some text after the price for this product.', 'woocommerce' ),
			) );

		echo "</div>";
	}

	/**
	* Function to save meta for individual products
	*
	* @hook 	woocommerce_process_product_meta
	*/
	public function save_price_option_fields( $id ) {

		// all custom text input fields
		$text_fields = array(
			'_oak_price_prefix',
			'_oak_price_suffix'
		);

		foreach ( $text_fields as $field ) {

			if ( isset( $_POST[$field] ) ) {
				update_post_meta( $id, $field, sanitize_text_field( $_POST[$field] ) );
			}
		}
	}

	/**
	* Function to output custom price data
	*
	* @hook 	woocommerce_get_price_html
	* @var 		$price 		string 		current price html data
	* @var 		$instance 	object 		current product data
	*/
	public function display_price_option_fields( $price, $instance ) {

		// grab post meta
		$product_meta = get_post_meta( $instance->get_id() );

		// update price with prefix if set and not empty
		if ( isset( $product_meta['_oak_price_prefix'] ) && ! empty( $product_meta['_oak_price_prefix'][0] ) ) {
			$price = $product_meta['_oak_price_prefix'][0] . ' ' . $price;
		}

		// update price with suffix if set and not empty
		if ( isset( $product_meta['_oak_price_suffix'] ) && ! empty( $product_meta['_oak_price_suffix'][0] ) ) {
			$price = $price . ' ' . $product_meta['_oak_price_suffix'][0];
		}

		// return price html
		return $price;
	}

} // end class Oak_Woocommerce_Price_Display

new Oak_Woocommerce_Price_Display();
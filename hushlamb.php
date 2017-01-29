<?php
/**
 * Plugin Name:     Hushlamb
 * Plugin URI:      https://github.com/mykedean/hushlamb.git
 * Description:     Modifications to the Hushlamb website
 * Author:          Michael Dean
 * Author URI:      https://github.com/mykedean
 * Text Domain:     hushlamb
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Hushlamb
 */
    
    /**
     * ==========================================================
     * CUSTOM METABOXES
     * ==========================================================
     */
    /**
     * Register metaboxes
     */
     
    /**
     * Store the names of all the meta keys so they can be saved in a single function
     * @see hushlamb_save_post_class_meta()
     */
    
    $hushlamb_meta_keys = array();
    
    // Hook into Wordpress loading procedure
    add_action( 'add_meta_boxes', 'hushlamb_register_metaboxes' );
    
    function hushlamb_register_metaboxes() {
        add_meta_box( 
            'release-info', 
            __( 'Release Information', 'hushlamb' ), 
            'hushlamb_release_info_fields', 
            'post'
        );
        
        add_meta_box( 
            'purchase-links', 
            __( 'Purchase Links', 'hushlamb' ), 
            'hushlamb_purchase_links_fields', 
            'post'
        );
    }
    
    /**
     * Output HTML form fields for the release-info metabox.
     * 
     * Callback function for add_meta_box(). Echos the HTML directly. Does not return anything.
     * 
     * artist
     * release date
     * catelog number
     * 
     * traxsource
     * beatport
     * 
     * @return void
     */
    function hushlamb_release_info_fields( $object ) {
        
        /**
         * Store the meta keys for each field that is created so they can be saved later
         * @see hushlamb_save_post_class_meta()
         */
        
        global $hushlamb_meta_keys;
        $hushlamb_meta_keys[] = 'hushlamb_release_date';
        $hushlamb_meta_keys[] = 'hushlamb_catalog_number';
    
        wp_nonce_field( basename( __FILE__ ), 'hushlamb_post_class_nonce' );
        
        /**
         * Release date and catalog number
        */
        ?>
            <p>
                <label for="hushlamb-catalog-number"><?php _e( "Catalog No.", 'hushlamb' ); ?></label>
                <br />
                <input class="widefat" type="text" name="hushlamb-catalog-number" id="hushlamb-catalog-number" value="<?php echo esc_attr( get_post_meta( $object->ID, 'hushlamb_catalog_number', true ) ); ?>" size="30" /><label for="hushlamb-release-date"><?php _e( "Release Date", 'hushlamb' ); ?></label>
                <br />
                <input class="date-picker" type="date" name="hushlamb-release-date" id="hushlamb-release-date" value="<?php echo esc_attr( get_post_meta( $object->ID, 'hushlamb_release_date', true ) ); ?>" style="display: block;" />
                
            </p>
        <?php
    }
    
        
    /**
     * Traxsource and Beatport links
    */
    function hushlamb_purchase_links_fields( $object ) {
        
        /**
         * Store the meta keys for each field that is created so they can be saved later
         * @see hushlamb_save_post_class_meta()
         */
        
        global $hushlamb_meta_keys;
        
        $hushlamb_meta_keys[] = 'hushlamb_beatport_link';
        $hushlamb_meta_keys[] = 'hushlamb_amazon_link';
        $hushlamb_meta_keys[] = 'hushlamb_traxsource_link';
    
        wp_nonce_field( basename( __FILE__ ), 'hushlamb_post_class_nonce' );
        
        ?>
            <p>
                <label for="hushlamb-beatport-link"><?php _e( "Beatport", 'hushlamb' ); ?></label>
                <br />
                <input class="widefat" type="text" name="hushlamb-beatport-link" id="hushlamb-beatport-link" value="<?php echo esc_attr( get_post_meta( $object->ID, 'hushlamb_beatport_link', true ) ); ?>" size="30" />
                
                <label for="hushlamb-amazon-link"><?php _e( "Amazon", 'hushlamb' ); ?></label>
                <br />
                <input class="widefat" type="text" name="hushlamb-amazon-link" id="hushlamb-amazon-link" value="<?php echo esc_attr( get_post_meta( $object->ID, 'hushlamb_amazon_link', true ) ); ?>" size="30" />
                
                <label for="hushlamb-traxsource-link"><?php _e( "Traxsource", 'hushlamb' ); ?></label>
                <br />
                <input class="widefat" type="text" name="hushlamb-traxsource-link" id="hushlamb-traxsource-link" value="<?php echo esc_attr( get_post_meta( $object->ID, 'hushlamb_traxsource_link', true ) ); ?>" size="30" />

            </p>
        <?php
        
    }
    
    /**
     * Save metabox data.
     */
     
    //Save post meta on the 'save_post' hook.
    add_action( 'save_post', 'hushlamb_save_post_class_meta', 10, 2 );
    
    function hushlamb_save_post_class_meta( $post_id, $post ) {
        
        //Save all the metadata by interating through the saved meta keys.
        //@TODO Fix this so the meta keys don't have to be decaled here
        //global $hushlamb_meta_keys;
        
        $hushlamb_meta_keys = array();
        
        $hushlamb_meta_keys[] = 'hushlamb_release_date';
        $hushlamb_meta_keys[] = 'hushlamb_catalog_number';
        
        $hushlamb_meta_keys[] = 'hushlamb_beatport_link';
        $hushlamb_meta_keys[] = 'hushlamb_amazon_link';
        $hushlamb_meta_keys[] = 'hushlamb_traxsource_link';
        
        /* Verify the nonce before proceeding. */
        if ( !isset( $_POST['hushlamb_post_class_nonce'] ) || !wp_verify_nonce( $_POST['hushlamb_post_class_nonce'], basename( __FILE__ ) ) ) {
            return $post_id;
        }
        /* Get the post type object. */
        $post_type = get_post_type_object( $post->post_type );
        
        /* Check if the current user has permission to edit the post. */
        if ( !current_user_can( $post_type->cap->edit_post, $post_id ) ) {
            return $post_id;
        }
        
        //Go through each of the meta keys registered to metaboxes and update them
        foreach( $hushlamb_meta_keys as $meta_key ) {
            $class_name = str_replace('_', '-', $meta_key);
            
            /* Get the posted data and sanitize it for use as an HTML class. */
            /**
             * @TODO Sanitize data, but allow hyperlinks to be stored in database
             */
            //$new_meta_value = ( isset( $_POST[ $class_name ] ) ? sanitize_html_class( $_POST[ $class_name ] ) : '' );
            $new_meta_value = $_POST[ $class_name ];
            
            /* Get the meta value of the custom field key. */
            $meta_value = get_post_meta( $post_id, $meta_key, true );
            
            /* If a new meta value was added and there was no previous value, add it. */
            if ( $new_meta_value && '' == $meta_value ) {
                
                add_post_meta( $post_id, $meta_key, $new_meta_value, true );
            
            /* If the new meta value does not match the old value, update it. */
            } elseif ( $new_meta_value && $new_meta_value != $meta_value ) {
                
                update_post_meta( $post_id, $meta_key, $new_meta_value );
                
            /* If there is no new meta value but an old value exists, delete it. */
            } elseif ( '' == $new_meta_value && $meta_value ) {
                
                delete_post_meta( $post_id, $meta_key, $meta_value );
            }  
        }
        
        
    }
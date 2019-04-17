<?php

class nabtron_custom_swiftype_metabox
{
    /**
     * Hook into the appropriate actions when the class is constructed.
     */
    public function __construct() {
        add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
        add_action( 'save_post',      array( $this, 'save'         ) );
    }
 
    /**
     * Adds the meta box container.
     */
    public function add_meta_box( $post_type ) {
        // Limit meta box to certain post types.
        $post_types = array( 'location', 'tribe_events' );
 
        if ( in_array( $post_type, $post_types ) ) {
            add_meta_box(
                'nabtron_member_nonmember_metabox_id',
                'Non Member',
                array( $this, 'render_meta_box_content' ),
                $post_type,
                'side',
                'high'
            );
        }
    }
 
    /**
     * Save the meta when the post is saved.
     *
     * @param int $post_id The ID of the post being saved.
     */
    public function save( $post_id ) {
 
        /*
         * We need to verify this came from the our screen and with proper authorization,
         * because save_post can be triggered at other times.
         */
 
        // Check if our nonce is set.
        if ( ! isset( $_POST['nabtron_member_nonmember_metabox_nonce'] ) ) {
            return $post_id;
        }
 
        $nonce = $_POST['nabtron_member_nonmember_metabox_nonce'];
 
        // Verify that the nonce is valid.
        if ( ! wp_verify_nonce( $nonce, 'nabtron_member_nonmember_metabox_nonce_action_67' ) ) {
            return $post_id;
        }
 
        /*
         * If this is an autosave, our form has not been submitted,
         * so we don't want to do anything.
         */
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return $post_id;
        }
 
        // Check the user's permissions.
        if ( 'page' == $_POST['post_type'] ) {
            if ( ! current_user_can( 'edit_page', $post_id ) ) {
                return $post_id;
            }
        } else {
            if ( ! current_user_can( 'edit_post', $post_id ) ) {
                return $post_id;
            }
        }
 
        /* OK, it's safe for us to save the data now. */
 
        // Sanitize the user input.
        $mydata = sanitize_text_field( $_POST['nabtron_member_nonmember_metabox_value'] );
 
        // Update the meta field.
        update_post_meta( $post_id, '_nabtron_member_nonmember_metabox_value', $mydata );
    }
 
 
    /**
     * Render Meta Box content.
     *
     * @param WP_Post $post The post object.
     */
    public function render_meta_box_content( $post ) {
 
        // Add an nonce field so we can check for it later.
        wp_nonce_field( 'nabtron_member_nonmember_metabox_nonce_action_67', 'nabtron_member_nonmember_metabox_nonce' );
 
        // Use get_post_meta to retrieve an existing value from the database.
        $value = get_post_meta( $post->ID, '_nabtron_member_nonmember_metabox_value', true );
 
        // Display the form, using the current value.
        ?>
        <label for="nabtron_member_nonmember_metabox_value">
        <input type="checkbox" name="nabtron_member_nonmember_metabox_value" id="nabtron_member_nonmember_metabox_value" value="1" <?php echo esc_attr( $value ) ? 'checked' : ''; ?> />
        Non Member
        </label>
        <?php
    }
}

function call_nabtron_custom_swiftype_metabox() {
    $nabtron_custom_swiftype_metabox = new nabtron_custom_swiftype_metabox();}
 
if ( is_admin() ) {
    add_action( 'load-post.php',     'call_nabtron_custom_swiftype_metabox' );
    add_action( 'load-post-new.php', 'call_nabtron_custom_swiftype_metabox' );
}

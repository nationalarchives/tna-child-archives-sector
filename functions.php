<?php

// For breadcrumbs and URLs
function tnatheme_globals() {
    global $pre_path;
    global $pre_crumbs;
    $headers = apache_request_headers();
    if ( isset($_SERVER['HTTP_X_NGINX_PROXY']) && isset($headers['X_HOST_TYPE']) && $headers['X_HOST_TYPE'] == 'public' ) {
        $pre_crumbs = array(
            'Archives sector' => '/archives-sector/'
        );
        $pre_path = '/archives-sector';
    } elseif (substr($_SERVER['REMOTE_ADDR'], 0, 3) === '10.') {
        $pre_path = '';
        $pre_crumbs = array(
            'Archives sector' => '/'
        );
    } else {
        $pre_crumbs = array(
            'Archives sector' => '/archives-sector/'
        );
        $pre_path = '/archives-sector';
    }
}
// If web development machine
if ( $_SERVER['SERVER_ADDR'] !== $_SERVER['REMOTE_ADDR'] ) {
        tnatheme_globals();
    } else {
        $pre_path = '';
        $pre_crumbs = array(
            'Archives sector' => '/'
    );
}

// Dequeue parent styles for re-enqueuing in the correct order
function dequeue_parent_style() {
    wp_dequeue_style('tna-styles');
    wp_deregister_style('tna-styles');
}
add_action( 'wp_enqueue_scripts', 'dequeue_parent_style', 9999 );
add_action( 'wp_head', 'dequeue_parent_style', 9999 );

// Enqueue styles in correct order
function tna_child_styles() {
    wp_register_style( 'tna-parent-styles', get_template_directory_uri() . '/css/base-sass.min.css', array(), EDD_VERSION, 'all' );
    wp_register_style( 'tna-child-styles', get_stylesheet_directory_uri() . '/style.css', array(), '0.1', 'all' );
    wp_register_style( 'tna-child-publication-styles', get_stylesheet_directory_uri() . '/css/publication.css', array(), '0.1', 'all' );
    wp_enqueue_script( 'bootstrapjs', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js', array('jquery'), '', true );
    wp_enqueue_style( 'tna-parent-styles' );
    wp_enqueue_style( 'tna-child-styles' );
    if (is_page_template( 'publication.php' )){
        wp_enqueue_style( 'tna-child-publication-styles' );
    }
}
add_action( 'wp_enqueue_scripts', 'tna_child_styles' );

//Assign Category and Tags to WordPress Page
function add_taxonomies_to_pages() {
    register_taxonomy_for_object_type( 'post_tag', 'page' );
    register_taxonomy_for_object_type( 'category', 'page' );
}
add_action( 'init', 'add_taxonomies_to_pages' );


//Custom field for feature video
function video_metabox() {
    add_meta_box(
        'video_editor_metabox',
        __( 'Feature Video', 'video_editor' ),
        'video_metabox_callback',
        'page',
        'side',
        'low'
    );
}
add_action( 'add_meta_boxes', 'video_metabox', 0 );

function video_metabox_callback( $post ) {
        wp_nonce_field(basename(__FILE__), 'video_metabox_nonce');
        //$video_stored_meta = get_post_meta($post->ID);
        $content = get_post_meta( $post->ID, 'video_metabox', true );
        $editor = 'video_metabox';
        $settings = array(
            'wpautop' => false,
            'textarea_rows' => 10,
            'media_buttons' => true,
            'teeny' => true, // show minimal editor
            'dfw' => false, // replace the default fullscreen with DFW
            'tinymce' => array(
                // Items for the Visual Tab
                'toolbar1'=> 'undo,redo,',
            ),
        );
        wp_editor( $content, $editor, $settings);
    ?>
    <div class="meta-row">
        <div class="meta-th">
            <p class="howto">
                Copy and paste the required video.
            </p>
        </div>
    </div>
<?php }

function video_metabox_save($post_id){
    // Checks save status
    $is_autosave = wp_is_post_autosave( $post_id );
    $is_revision = wp_is_post_revision( $post_id );
    $is_valid_nonce = ( isset( $_POST[ 'video_metabox_nonce' ] ) && wp_verify_nonce( $_POST[ 'video_metabox_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';

    // Exits script depending on save status
    if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
        return;
    }
    if ( isset( $_POST[ 'video_metabox' ] ) ) {
        update_post_meta( $post_id, 'video_metabox', sanitize_text_field( $_POST[ 'video_metabox' ] ) );
    }
}
add_action( 'save_post', 'video_metabox_save' );


//Custom meta Feature box with call to action
function featbox_color_metabox() {
    add_meta_box(
        'featbox_meta_id',
        __( 'Feature box with call to action' ),
        'featbox_meta_callback',
        'page',
        'normal',
        'core'
    );
}
add_action( 'add_meta_boxes', 'featbox_color_metabox' );
function featbox_meta_callback( $post ) {
    wp_nonce_field( basename( __FILE__ ), 'featbox_meta_nonce' );
    $feature_stored_meta = get_post_meta( $post->ID );

        $content = get_post_meta( $post->ID, 'featbox_editor', true );
        $editor = 'featbox_editor';
        $settings = array(
            'wpautop' => true,
            'textarea_rows' => 10,
            'media_buttons' => false,
            'dfw' => true,
            'quicktags'     => true

        );
        wp_editor( $content, $editor, $settings); ?>
            <br>
            <label for="color"><strong>Select background color</strong></label>
            <select name="featbox_select" id="featbox-select" class="widefat">
                <option value="mid-light-grey" <?php if ( ! empty ( $feature_stored_meta['featbox_select'] ) ) selected( $feature_stored_meta['featbox_select'][0], 'mid-light-grey' ); ?>>Mid-light grey</option>
                <option value="light-grey" <?php if ( ! empty ( $feature_stored_meta['featbox_select'] ) ) selected( $feature_stored_meta['featbox_select'][0], 'light-grey' ); ?>>Light grey</option>
                <option value="lighter-grey" <?php if ( ! empty ( $feature_stored_meta['featbox_select'] ) ) selected( $feature_stored_meta['featbox_select'][0], 'lighter-grey' ); ?>>Lighter grey</option>
                <option value="lightest-grey" <?php if ( ! empty ( $feature_stored_meta['featbox_select'] ) ) selected( $feature_stored_meta['featbox_select'][0], 'lightest-grey' ); ?>>Lightest grey</option>
            </select>
    <?php
}
function featbox_color_meta_save( $post_id ) {
    // Checks save status
    $is_autosave = wp_is_post_autosave( $post_id );
    $is_revision = wp_is_post_revision( $post_id );
    $is_valid_nonce = ( isset( $_POST[ 'featbox_meta_nonce' ] ) && wp_verify_nonce( $_POST[ 'featbox_meta_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';
    // Exits script depending on save status
    if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
        return;
    }
    if ( isset( $_POST[ 'featbox_editor' ] ) ) {
        update_post_meta( $post_id, 'featbox_editor', $_POST[ 'featbox_editor' ] ) ;
    }
    if ( isset( $_POST[ 'featbox_select' ] ) ) {
        update_post_meta( $post_id, 'featbox_select', sanitize_text_field( $_POST[ 'featbox_select' ] ) );
    }
}
add_action( 'save_post', 'featbox_color_meta_save' );


//Add sub-heading field
function sub_heading_get_meta( $value ) {
    global $post;

    $field = get_post_meta( $post->ID, $value, true );
    if ( ! empty( $field ) ) {
        return is_array( $field ) ? stripslashes_deep( $field ) : stripslashes( wp_kses_decode_entities( $field ) );
    } else {
        return false;
    }
}

function sub_heading_add_meta_box() {
    add_meta_box(
        'sub_heading-sub-heading',
        __( 'Sub heading', 'sub_heading' ),
        'sub_heading_html',
        'page',
        'side',
        'high'
    );
}
add_action( 'add_meta_boxes', 'sub_heading_add_meta_box' );

function sub_heading_html( $post) {
    wp_nonce_field( '_sub_heading_nonce', 'sub_heading_nonce' ); ?>

    <input class="widefat" placeholder="Enter a sub heading" type="text" name="sub_heading_sub_heading" id="sub_heading_sub_heading" value="<?php echo sub_heading_get_meta( 'sub_heading_sub_heading' ); ?>">
    </p><?php
}


function sub_heading_save( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! isset( $_POST['sub_heading_nonce'] ) || ! wp_verify_nonce( $_POST['sub_heading_nonce'], '_sub_heading_nonce' ) ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    if ( isset( $_POST['sub_heading_sub_heading'] ) )
        update_post_meta( $post_id, 'sub_heading_sub_heading', esc_attr( $_POST['sub_heading_sub_heading'] ) );
}
add_action( 'save_post', 'sub_heading_save' );


add_filter( 'oembed_dataparse', function( $return, $data, $url ){
    if( false === strpos( $return,'youtube.com' ) )
        return $return;

    //$id = explode( 'watch?v=', $url );
    $add_id = str_replace( 'allowfullscreen>', 'allowfullscreen id="video">', $return );
    return $add_id;
}, 10, 3 );

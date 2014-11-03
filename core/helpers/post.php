<?php  /**
 * Post helper funtions
 *
 * This file is used to display post elements, from meta to media, to galleries, to in-post pagination, all post-related functions sit in this file.
 *
 * @package Hatch
 * @since Hatch 1.0
 */


/**
* Print post meta HTML
*
* @param    varchar         $post_id        ID of the post to use
* @param    array           $display        Configuration arguments. (date, author, categories, tags)
* @param    varchar         $wrapper        Type of html wrapper
* @param    varchar         $wrapper_class  Class of HTML wrapper
* @echo     string                          Post Meta HTML
*/

if( !function_exists( 'hatch_post_meta' ) ) {
    function hatch_post_meta( $post_id = NULL , $display = NULL, $wrapper = 'footer', $wrapper_class = 'meta-info' ) {
        // If there is no post ID specified, use the current post, does not affect post author, yet.
        if( NULL == $post_id ) {
            global $post;
            $post_id = $post->ID;
        }

        // If there are no items to display, return nothing
        if( NULL == $display ) $display = array( 'date', 'author', 'categories', 'tags' );

        foreach ( $display as $meta ) {
            switch ( $meta ) {
                case 'date' :
                    $meta_to_display[] = __( 'on ', HATCH_THEME_SLUG ) . get_the_time(  get_option( 'date_format' ) , $post_id );
                    break;
                case 'author' :
                    $meta_to_display[] = __( 'by ', HATCH_THEME_SLUG ) . hatch_get_the_author( $post_id );
                    break;
                case 'categories' :
                    $categories = '';

                    // Use different terms for different post types
                    if( 'post' == get_post_type( $post_id ) ){
                        $the_categories = get_the_category( $post_id );
                    } elseif( 'jetpack-portfolio' == get_post_type( $post_id ) ) {
                        $the_categories = get_the_terms( $post_id , 'jetpack-portfolio-type' );
                    } else {
                        $the_categories = FALSE;
                    }

                    // If there are no categories, skip to the next case
                    if( !$the_categories ) continue;

                    foreach ( $the_categories as $category ){
                        $categories .= ' <a href="'.get_category_link( $category->term_id ).'" title="' . esc_attr( sprintf( __( "View all posts in %s", HATCH_THEME_SLUG ), $category->name ) ) . '">'.$category->name.'</a>';
                    }
                    $meta_to_display[] = __( 'in ', HATCH_THEME_SLUG ) . $categories;
                    break;
                case 'tags' :
                    $tags = '';

                    if( 'post' == get_post_type( $post_id ) ){
                        $the_tags = get_the_tags( $post_id );
                    } elseif( 'jetpack-portfolio' == get_post_type( $post_id ) ) {
                        $the_tags = get_the_terms( $post_id , 'jetpack-portfolio-tag' );
                    } else {
                        $the_tags = FALSE;
                    }

                    // If there are no tags, skip to the next case
                    if( !$the_tags ) continue;

                    foreach ( $the_tags as $tag ){
                        $tags[] = ' <a href="'.get_category_link( $tag->term_id ).'" title="' . esc_attr( sprintf( __( "View all posts tagged %s", HATCH_THEME_SLUG ), $tag->name ) ) . '">'.$tag->name.'</a>';
                    }
                    $meta_to_display[] = __( 'tagged ', HATCH_THEME_SLUG ) . implode( __( ', ', HATCH_THEME_SLUG ), $tags );
                    break;
                break;
            } // switch meta
        } // foreach $display

        if( !empty( $meta_to_display ) ) {
            echo '<' . $wrapper . ( ( '' != $wrapper_class ) ? ' class="' . $wrapper_class .'"' : NULL ) . '>';
                echo '<p>';
                    echo __( 'Written ' , HATCH_THEME_SLUG ) . implode( ' ' , $meta_to_display );
                echo '</p>';
            echo '</' . $wrapper . '>';
        }
    }
} // hatch_post_meta

/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
if ( ! function_exists( 'hatch_get_the_author' ) ) {
    function hatch_get_the_author() {
        return sprintf( __( '<a href="%1$s" title="%2$s" rel="author">%3$s</a>', HATCH_THEME_SLUG ),
            esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
            esc_attr( sprintf( __( 'View all posts by %s', 'the-writer' ), get_the_author() ) ),
            esc_attr( get_the_author() )
        );
    }
} // hatch_get_the_author


/**
 * Prints Comment HTML
 *
 * @param    object          $comment        Comment objext
 * @param    array           $args           Configuration arguments.
 * @param    int             $depth          Current depth of comment, for example 2 for a reply
 * @echo     string                          Comment HTML
 */
if( !function_exists( 'hatch_comment' ) ) {
    function hatch_comment($comment, $args, $depth) {
        $GLOBALS['comment'] = $comment;?>
        <?php if( 1 < $depth && isset( $GLOBALS['lastdepth'] ) && $depth != $GLOBALS['lastdepth'] ) { ?>
            <div class="row comments-nested push-top">
        <?php } ?>
        <div <?php comment_class( 'content push-bottom well' ); ?> id="comment-<?php comment_ID(); ?>">
            <div class="avatar push-bottom clearfix">
                <?php edit_comment_link(__('(Edit)', HATCH_THEME_SLUG),'<small class="pull-right">','</small>') ?>
                <a class="avatar-image" href="">
                    <?php echo get_avatar($comment, $size = '70'); ?>
                </a>
                <div class="avatar-body">
                    <h5 class="avatar-name"><?php echo get_comment_author_link(); ?></h5>
                    <small><?php printf(__('%1$s at %2$s', HATCH_THEME_SLUG), get_comment_date(),  get_comment_time()) ?></small>
                </div>
            </div>

            <div class="copy small">
                <?php if ($comment->comment_approved == '0') : ?>
                    <em><?php _e('Your comment is awaiting moderation.', HATCH_THEME_SLUG) ?></em>
                    <br />
                <?php endif; ?>
                <?php comment_text() ?>
                <?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
            </div>
        <?php if( 1 < $depth && isset( $GLOBALS['lastdepth'] ) && $depth == $GLOBALS['lastdepth'] ) { ?>
            </div>
        <?php } ?>

        <?php $GLOBALS['lastdepth'] = $depth; ?>
<?php }
} // hatch_comment

/**
 * Backs up builder pages as HTML
 */
if( !function_exists( 'hatch_backup_builder_pages' ) ) {

    function hatch_backup_builder_pages(){

        if( !isset( $_POST[ 'pageid' ] ) ) wp_die( __( 'You shall not pass' , HATCH_THEME_SLUG ) );

        // Get the post data
        $page_id = $_POST[ 'pageid' ];
        $page = get_page( $page_id );

        // Start the output buffer
        ob_start();
        dynamic_sidebar( 'obox-hatch-builder-' . $page->ID );

        $page_content = ob_get_clean();
        $page_content = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $page_content);
        $page_content = strip_tags( $page_content , '<p><b><i><strong><em><quote><a><h1><h2><h3><h4><h5><img><script>' );

        // New page arguments
        $updated_page = array(
            'ID'           => $page_id,
            'post_content' => $page_content
        );

        // Update the page into the database
        wp_update_post( $updated_page );

        // Flush the output buffer
        ob_flush();
    }

    add_action( 'wp_ajax_hatch_backup_builder_pages', 'hatch_backup_builder_pages' );
} // hatch_builder_page_backup




/**
*  Adjust the site title for static front pages
*/
if( !function_exists( 'hatch_post_class' ) ) {
    function hatch_post_class( $classes ) {
        if( is_post_type_archive( 'product' ) ) {
            $classes[] = 'column span-4';
        }

        return $classes;
    }
    add_filter( 'post_class' , 'hatch_post_class' );
}
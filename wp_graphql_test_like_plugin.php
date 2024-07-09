<?php

/**
 * Plugin Name: WP GraphQL Test Like Plugin
 * Description: Test project
 * Requires at least: 6.1
 * Requires PHP: 8.1
 * Author: Yurii M
 * Version: 1.0.0
 */

define( 'TEST_LIKE_PLUGIN_FILE', __FILE__ );
define( 'TEST_LIKE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'TEST_LIKE_PLUGIN_TEXT_DOMAIN', 'test_like_plugin' );

// Register the custom meta field
function register_test_like_meta() {
    register_post_meta( 'post', 'test_like', array(
        'show_in_rest' => true,
        'single' => true,
        'type' => 'integer',
        'default' => 0,
        'sanitize_callback' => 'absint',
        'auth_callback' => function() {
            return current_user_can('edit_posts');
        }
    ));
}
add_action( 'init', 'register_test_like_meta' );

// Register the custom field in WPGraphQL
add_action( 'graphql_register_types', function() {
    register_graphql_field( 'Post', 'testLike', [
        'type' => 'Int',
        'description' => __( 'A custom like counter for posts', TEST_LIKE_PLUGIN_TEXT_DOMAIN ),
        'resolve' => function( $post ) {
            return get_post_meta( $post->ID, 'test_like', true );
        }
    ]);
});

// Create a mutation to update the custom field in WPGraphQL
add_action( 'graphql_register_types', function() {
    register_graphql_mutation( 'updateTestLike', [
        'inputFields' => [
            'postId' => [
                'type' => 'ID',
                'description' => __( 'The ID of the post to update', TEST_LIKE_PLUGIN_TEXT_DOMAIN ),
            ],
            'likeCount' => [
                'type' => 'Int',
                'description' => __( 'The new like count', TEST_LIKE_PLUGIN_TEXT_DOMAIN ),
            ],
        ],
        'outputFields' => [
            'post' => [
                'type' => 'Post',
                'description' => __( 'The post with the updated like count', TEST_LIKE_PLUGIN_TEXT_DOMAIN ),
                'resolve' => function( $payload, $args, $context, $info ) {
                    $post = get_post( $payload['postId'] );
                    if ( !$post ) {
                        return null;
                    }
                    return $post;
                }
            ],
        ],
        'mutateAndGetPayload' => function( $input, $context, $info ) {
            $post_id = absint( $input['postId'] );
            $like_count = absint( $input['likeCount'] );

            update_post_meta( $post_id, 'test_like', $like_count );

            return [
                'postId' => $post_id,
            ];
        },
    ]);
});

add_action( 'graphql_register_types', function() {

    add_filter( 'graphql_input_fields', function( $fields, $type_name, $config ) {
        if ( 'UpdatePostInput' === $type_name ) {
            $fields['testLike'] = [
                'type' => 'Int',
                'description' => __( 'The custom like counter for the post', TEST_LIKE_PLUGIN_TEXT_DOMAIN ),
            ];
        }
        return $fields;
    }, 10, 3 );

    add_action( 'graphql_register_mutation_update_post', function( $mutation ) {
        $mutation->add_callback( 'post_object', function( $post_object, $input, $context, $info ) {
            if ( isset( $input['testLike'] ) ) {
                update_post_meta( $post_object->ID, 'test_like', absint( $input['testLike'] ) );
            }
        });
    });
});
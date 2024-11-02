<?php

function epm_rest_api_initialize() {
    register_rest_route('epm/v1', '/posts', array(
        'methods' => 'GET',
        'callback' => 'epm_get_all_posts',
//        'permission_callback' => function () {
//            return current_user_can('read');
//        }
    ));

    register_rest_route('epm/v1', '/posts', array(
        'methods' => 'POST',
        'callback' => 'epm_create_new_posts',
        'permission_callback' => '__return_true'
    ));

    register_rest_route('epm/v1', '/posts/(?P<id>\d+)', array(
        'methods' => 'PUT',
        'callback' => 'epm_update_posts_by_id',
        'args' => array(
            'id' => array(
                'validate_callback' => function ($param, $request, $key) {
                    return is_numeric($param);
                }
            ),
        ),
        'permission_callback' => '__return_true'
    ));

    register_rest_route('epm/v1', '/posts/(?P<id>\d+)', array(
        'methods' => 'DELETE',
        'callback' => 'epm_delete_posts_by_id',
        'args' => array(
            'id' => array(
                'validate_callback' => function ($param, $request, $key) {
                    return is_numeric($param);
                }
            ),
        ),
        'permission_callback' => '__return_true'
    ));

    register_rest_route('epm/v1', '/posts/(?P<id>\d+)', array(
        'methods' => 'DELETE',
        'callback' => 'epm_get_posts_by_id',
        'args' => array(
            'id' => array(
                'validate_callback' => function ($param, $request, $key) {
                    return is_numeric($param);
                }
            ),
        ),
        'permission_callback' => '__return_true'
    ));
}

function epm_get_all_posts($request) {
    $posts = get_posts(['fields' => 'all', 'posts_per_page' => -1]);
    return new WP_REST_Response(['status' => 'success', 'data' => $posts], 200);
}

function epm_create_new_posts($request) {
    $title = trim($request['title']);
    $content = trim($request['content']);
    $status = trim($request['status']);
    $category = trim($request['category']);
    if (empty($title)) {
        return new WP_Error('no_title', 'Post Title is Empty', array('status' => 400));
    } elseif (empty(strip_tags($content))) {
        return new WP_Error('no_content', 'Post Content is Empty', array('status' => 400));
    } elseif (empty($status)) {
        return new WP_Error('no_status', 'Post Publish Status is Empty', array('status' => 400));
    } elseif (empty($category)) {
        return new WP_Error('no_category', 'Post Category is Empty', array('status' => 400));
    } else {
        $post = array(
            'post_title' => $title,
            'post_content' => $content,
            'post_category' => array($category),
            'post_status' => $status,
            'post_type' => 'post',
        );
        $post_id = wp_insert_post($post);
        if ($post_id) {
            wp_set_post_terms($post_id, array($category), 'category');

            return new WP_REST_Response(['status' => 'success', 'data' => ['msg' => 'Post Created Successfully', 'ID' => $post_id]], 200);
        } else {
            return new WP_Error('create_failed', 'Post Creation Failed', array('status' => 400));
        }
    }
}

function epm_update_posts_by_id($request) {
    $id = trim($request['id']);
    $title = trim($request['title']);
    $content = trim($request['content']);
    $status = trim($request['status']);
    $category = trim($request['category']);
    if (empty($id)) {
        return new WP_Error('no_id', 'Post ID is Empty', array('status' => 400));
    } else {
        $post = array(
            'ID' => $id,
            'post_type' => 'post',
        );
        if (!empty($title)) {
            $post['post_title'] = $title;
        }
        if (!empty($content)) {
            $post['post_content'] = $content;
        }
        if (!empty($category)) {
            $post['post_category'] = [$category];
        }
        if (!empty($status)) {
            $post['post_status'] = $status;
        }
        $post_id = wp_update_post($post);
        if ($post_id) {
            wp_set_post_terms($post_id, array($category), 'category');

            return new WP_REST_Response(['status' => 'success', 'data' => ['msg' => 'Post Updated Successfully', 'ID' => $post_id]], 200);
        } else {
            return new WP_Error('update_failed', 'Post Update Failed', array('status' => 400));
        }
    }
}

function epm_delete_posts_by_id($request) {
    $id = trim($request['id']);
    if (empty($id)) {
        return new WP_Error('no_id', 'Post ID is Empty', array('status' => 400));
    } else {
        $post_id = wp_delete_post($id);
        if ($post_id) {
            return new WP_REST_Response(['status' => 'success', 'data' => ['msg' => 'Post Deleted Successfully']], 200);
        } else {
            return new WP_Error('update_failed', 'Post Deletion Failed', array('status' => 400));
        }
    }
}

function epm_get_posts_by_id($request) {
    $id = trim($request['id']);
    if (empty($id)) {
        return new WP_Error('no_id', 'Post ID is Empty', array('status' => 400));
    } else {
        $query = new WP_Query(['p' => $id]);
        if (!empty($query)) {
            $posts = $query[0];
            return new WP_REST_Response(['status' => 'success', 'data' => $posts], 200);
        } else {
            return new WP_Error('update_failed', 'No Post Data Found', array('status' => 400));
        }
    }
    return new WP_REST_Response(['status' => 'success'], 200);
}

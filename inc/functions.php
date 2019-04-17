<?php
// use has_terms() to see if it has some category and then alter this thing if needed
// reference: https://developer.wordpress.org/reference/functions/has_term/
function nabtron_update_swiftype_document_url( $document, $post ) {
    $nabtron_member_nonmember_metabox_value = empty(get_post_meta( $post->ID, '_nabtron_member_nonmember_metabox_value', true )) ? '0' : '1';

    // or use this to check if it has a category then use that
    // $nabtron_member_nonmember_metabox_value = empty( has_term( '11', 'category', $post->ID ) ) ? '0' : '1';
    
    $document['fields'][] = array( 'name' => 'non_member',
                                   'type' => 'enum',
                                   'value' => $nabtron_member_nonmember_metabox_value);

    return $document;
}
add_filter( 'swiftype_document_builder', 'nabtron_update_swiftype_document_url', 10, 2 );

// adds sort field and direction to the params, to move the nonmember posts to the end of query results
function nabtron_swiftype_search_params($params){

    $params['sort_field'] = ['posts'  => 'non_member'];
    $params['sort_direction'] = ['posts'  => 'asc'];

    return $params;
}
add_filter( 'swiftype_search_params', 'nabtron_swiftype_search_params', 10, 1);

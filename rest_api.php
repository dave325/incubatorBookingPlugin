<?php  
    class My_REST_Posts_Controller {
    // Here initialize our namespace and resource name.
    public function __construct() {
        $this->namespace     = 'dsol-booking/v1';
        $this->resource_name = 'posts';
    }
 
    // Register our routes.
    public function register_routes() {
        register_rest_route( $this->namespace, '/test' , array(
            // Here we register the readable endpoint for collections.
            array(
                'methods'   => 'POST',
                'callback'  => array( $this, 'get_items' )
            )
        ) );
        register_rest_route( $this->namespace, '/getRoomInfo', array(
            // Notice how we are registering multiple endpoints the 'schema' equates to an OPTIONS request.
            array(
                'methods'   => 'POST',
                'callback'  => array( $this, 'getRoomInfo' )
               
            )
        ) );
    }
 
    /**
     * Check permissions for the posts.f
     *
     * @param WP_REST_Request $request Current request.
     */
    public function get_items_permissions_check( $request ) {
        if ( ! current_user_can( 'read' ) ) {
            return new WP_Error( 'rest_forbidden', esc_html__( 'You cannot view the post resource.' ), array( 'status' => $this->authorization_status_code() ) );
        }
        return true;
    }
 
    /**
     * Grabs the five most recent posts and outputs them as a rest response.
     *
     * @param WP_REST_Request $request Current request.
     */
    public function get_items( WP_REST_Request $request ) {
        global $wpdb;

        /***
         * Rewrite By David
         *     Sumaita
         *     Removed part of the where clause
         */
        $table_nameRes = $wpdb->prefix . 'bookaroom_reservations';
        $table_name = $wpdb->prefix . 'bookaroom_times';
        $sql = "SELECT reservation.res_id,
                        reservation.company_name,
                        reservation.email,
                        reservation.email,
                        reservation.attendance,
                        reservation.notes,
                        room_container.container_number,
                        room.room_number,
                        branch.b_name,
                        time_table.start_time,
                        time_table.end_time
        FROM branch
        LEFT JOIN room ON branch.b_id = room.b_id
        LEFT JOIN room_container ON room.r_id = room_container.r_id
        LEFT JOIN reservation ON room_container.c_id = reservation.c_id
        LEFT JOIN time_table ON time_table.t_id = reservation.t_id
        WHERE reservation.res_id IS NOT NULL
        GROUP BY reservation.res_id,room_container.container_number,room.room_number,branch.b_name 
        ORDER BY time_table.start_time;";
        $final = $wpdb->get_results($sql, ARRAY_A);
        // Return all of our comment response data.
        return rest_ensure_response( $final );
    }
 
    /**
     * Check permissions for the posts.
     *
     * @param WP_REST_Request $request Current request.
     */
    public function get_item_permissions_check( $request ) {
        if ( ! current_user_can( 'read' ) ) {
            return new WP_Error( 'rest_forbidden', esc_html__( 'You cannot view the post resource.' ), array( 'status' => $this->authorization_status_code() ) );
        }
        return true;
    }
 
    /**
     * Grabs the five most recent posts and outputs them as a rest response.
     *
     * @param WP_REST_Request $request Current request.
     */
    public function getRoomInfo(WP_REST_Request $request ) {
        
        global $wpdb;

        /***
         * Rewrite By David
         *     Sumaita
         *     Removed part of the where clause
         */
        $table_nameRes = $wpdb->prefix . 'bookaroom_reservations';
        $table_name = $wpdb->prefix . 'bookaroom_roomConts';
        $sql = "SELECT * FROM " . $table_name . " WHERE roomCont_isPublic=1";
        $final = $wpdb->get_results($sql, ARRAY_A);
        // Return all of our comment response data.
        return rest_ensure_response( $final );
    }
 
    /**
     * Matches the post data to the schema we want.
     *
     * @param WP_Post $post The comment object whose response is being prepared.
     */
    public function prepare_item_for_response( $post, $request ) {
        $post_data = array();
 
        $schema = $this->get_item_schema( $request );
 
        // We are also renaming the fields to more understandable names.
        if ( isset( $schema['properties']['id'] ) ) {
            $post_data['id'] = (int) $post->ID;
        }
 
        if ( isset( $schema['properties']['content'] ) ) {
            $post_data['content'] = apply_filters( 'the_content', $post->post_content, $post );
        }
 
        return rest_ensure_response( $post_data );
    }
 
    /**
     * Prepare a response for inserting into a collection of responses.
     *
     * This is copied from WP_REST_Controller class in the WP REST API v2 plugin.
     *
     * @param WP_REST_Response $response Response object.
     * @return array Response data, ready for insertion into collection data.
     */
    public function prepare_response_for_collection( $response ) {
        if ( ! ( $response instanceof WP_REST_Response ) ) {
            return $response;
        }
 
        $data = (array) $response->get_data();
        $server = rest_get_server();
 
        if ( method_exists( $server, 'get_compact_response_links' ) ) {
            $links = call_user_func( array( $server, 'get_compact_response_links' ), $response );
        } else {
            $links = call_user_func( array( $server, 'get_response_links' ), $response );
        }
 
        if ( ! empty( $links ) ) {
            $data['_links'] = $links;
        }
 
        return $data;
    }
 
    /**
     * Get our sample schema for a post.
     *
     * @param WP_REST_Request $request Current request.
     */
    public function get_item_schema( $request ) {
        $schema = array(
            // This tells the spec of JSON Schema we are using which is draft 4.
            '$schema'              => 'http://json-schema.org/draft-04/schema#',
            // The title property marks the identity of the resource.
            'title'                => 'post',
            'type'                 => 'object',
            // In JSON Schema you can specify object properties in the properties attribute.
            'properties'           => array(
                'id' => array(
                    'description'  => esc_html__( 'Unique identifier for the object.', 'my-textdomain' ),
                    'type'         => 'integer',
                    'context'      => array( 'view', 'edit', 'embed' ),
                    'readonly'     => true,
                ),
                'content' => array(
                    'description'  => esc_html__( 'The content for the object.', 'my-textdomain' ),
                    'type'         => 'string',
                ),
            ),
        );
 
        return $schema;
    }
 
    // Sets up the proper HTTP status code for authorization.
    public function authorization_status_code() {
 
        $status = 401;
 
        if ( is_user_logged_in() ) {
            $status = 403;
        }
 
        return $status;
    }
}
 
// Function to register our new routes from the controller.
function prefix_register_my_rest_routes() {
    $controller = new My_REST_Posts_Controller();
    $controller->register_routes();
}
 
add_action( 'rest_api_init', 'prefix_register_my_rest_routes' );
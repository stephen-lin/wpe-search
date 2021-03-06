<?php
namespace ep4wpe;

define( __NAMESPACE__ . '\HOST', __NAMESPACE__ . '_host' );
define( __NAMESPACE__ . '\PORT', __NAMESPACE__ . '_port' );
define( __NAMESPACE__ . '\MAX_RELATED_POSTS', 10 );
define( __NAMESPACE__ . '\POST_COUNT_FIELD', __NAMESPACE__ . '_rp_count' );
define( __NAMESPACE__ . '\SHOW_RELATED_POSTS_FIELD', __NAMESPACE__ . '_show_rp' );


class EP_Config {

	/**
	 * Get a singleton instance of the class
	 *
	 * @since 0.1.0
	 * @return EP_Config
	 */
	public static function factory() {
		static $instance = false;

		if ( ! $instance ) {
			$instance = new self();
		}

		return $instance;
	}

        public function get_server_url() {
          $host = $this->get_server_host();
          if( isset( $host ) ) {
            $port = $this->get_server_port();
            return sprintf( 'http://%s:%d', $host, $port  );
          }
          else if( defined( 'EP_HOST' ) && EP_HOST ) {
              return EP_HOST;
          }
          else {
            return null;
          }
        }

	/**
	 * Gets the host name or address of the ElasticSearch server.
	 *
	 * @since 1.1.1
	 * @return string Host name or IP address
	 */
        public function get_server_host() {
          return get_site_option( constant( __NAMESPACE__ . '\HOST' ), null );
        }

	/**
	 * Sets the host name or address of the ElasticSearch server.
	 *
	 * @param string $host (optional) Port number.  If null, do nothing.
	 * @since 1.1.1
	 * @return nil
	 */
        public function set_server_host( $host = null ) {
          if( isset( $host ) ) {
            update_site_option( constant( __NAMESPACE__ . '\HOST' ), $host );
          }
        }

	/**
	 * Gets the port number of the ElasticSearch server.
	 *
	 * @since 1.1.1
	 * @return integer A port number (1-65535)
	 */
        public function get_server_port() {
          return get_site_option( constant( __NAMESPACE__ . '\PORT' ), 9200 );
        }

	/**
	 * Sets the port number of the ElasticSearch server.
	 *
	 * @param string $port (optional) Port number.  If null, do nothing.
	 * @since 1.1.1
	 * @return nil
	 */
        public function set_server_port( $port = null ) {
          if( isset( $port ) && preg_match( '^\d{1,5}$', $port ) && $port < 65536 ) {
            update_site_option( constant( __NAMESPACE__ . '\PORT' ), $port );
          }
        }

	/**
	 * Gets the boolean value used to determine whether to show related posts below single posts.
	 *
	 * @since 1.3.1-wpengine
	 * @return bool
	 */
        public function get_show_related_posts() {
          return get_site_option( constant( __NAMESPACE__ . '\SHOW_RELATED_POSTS_FIELD' ), false );
        }

	/**
	 * Sets the boolean value used to determine whether to show related posts below single posts.
	 *
	 * @param bool $host (optional) Port number.  If null, do nothing.
	 * @since 1.3.10-wpengine
	 * @return nil
	 */
        public function set_show_related_posts( $do_show = true ) {
          update_site_option( constant( __NAMESPACE__ . '\SHOW_RELATED_POSTS_FIELD' ), $do_show );
        }

	/**
	 * Generates the index name for the current site
	 *
	 * @param int $blog_id (optional) Blog ID. Defaults to current blog.
	 * @since 0.9
	 * @return string
	 */
	public function get_index_name( $blog_id = null ) {
		if ( ! $blog_id ) {
			$blog_id = get_current_blog_id();
		}

		$site_url = get_site_url( $blog_id );

		if ( ! empty( $site_url ) ) {
			$index_name = sha1( $site_url ) . '-' . $blog_id;
		} else {
			$index_name = false;
		}

		return apply_filters( 'ep_index_name', $index_name );
	}

	/**
	 * Returns the index url given an index name. Defaults to current index
	 *
	 * @param string|array $index
	 * @since 0.9
	 * @return string
	 */
	public function get_index_url( $index = null ) {
		if ( null === $index ) {
			$index = $this->get_index_name();
		} elseif ( is_array( $index ) ) {
			$index = implode( ',', array_filter( $index ) );
		}

		return untrailingslashit( $this->get_server_url() ) . '/' . $index;
	}

	/**
	 * Returns indexable post types for the current site
	 *
	 * @since 0.9
	 * @return mixed|void
	 */
	public function get_indexable_post_types() {
		$post_types = get_post_types( array( 'public' => true ) );

		return apply_filters( 'ep_indexable_post_types', $post_types );
	}

	/**
	 * Return indexable post_status for the current site
	 *
	 * @since 1.3
	 * @return array
	 */
	public function get_indexable_post_status() {
		return apply_filters( 'ep_indexable_post_status', array( 'publish' ) );
	}

	/**
	 * Generate network index name for alias
	 *
	 * @since 0.9
	 * @return string
	 */
	public function get_network_alias() {
		$url = network_site_url();
		$slug = preg_replace( '#https?://(www\.)?#i', '', $url );
		$slug = preg_replace( '#[^\w]#', '', $slug );

		$alias = $slug . '-global';

		return apply_filters( 'ep_global_alias', $alias );
	}
}

EP_Config::factory();

/**
 * Accessor functions for methods in above class. See doc blocks above for function details.
 */

function ep_get_index_url( $index = null ) {
	return EP_Config::factory()->get_index_url( $index );
}

function ep_get_index_name( $blog_id = null ) {
	return EP_Config::factory()->get_index_name( $blog_id );
}

function ep_get_indexable_post_types() {
	return EP_Config::factory()->get_indexable_post_types();
}

function ep_get_indexable_post_status() {
	return EP_Config::factory()->get_indexable_post_status();
}

function ep_get_network_alias() {
	return EP_Config::factory()->get_network_alias();
}

function ep_get_server_host() {
  return EP_Config::factory()->get_server_host();
}

function ep_set_server_host( $host = null ) {
  return EP_Config::factory()->set_server_host( $host );
}

function ep_get_server_port() {
  return EP_Config::factory()->get_server_port();
}

function ep_set_server_port( $port = null ) {
  return EP_Config::factory()->set_server_port( $port );
}

function ep_get_server_url() {
  return EP_Config::factory()->get_server_url();
}


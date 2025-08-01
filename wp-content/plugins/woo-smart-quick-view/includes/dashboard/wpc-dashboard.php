<?php
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPCleverMenu' ) ) {
	class WPCleverMenu {
		function __construct() {
			// do nothing, moved to WPCleverDashboard
		}
	}

	new WPCleverMenu();
}

if ( ! class_exists( 'WPCleverDashboard' ) ) {
	class WPCleverDashboard {
		function __construct() {
			add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
			add_action( 'admin_menu', [ $this, 'admin_menu' ] );
			add_action( 'wp_ajax_wpc_get_plugins', [ $this, 'ajax_get_plugins' ] );
			add_action( 'wp_ajax_wpc_get_suggestion', [ $this, 'ajax_get_suggestion' ] );
		}

		function enqueue_scripts() {
			wp_enqueue_style( 'wpc-dashboard', WPC_URI . 'includes/dashboard/css/dashboard.css' );
			wp_enqueue_script( 'wpc-dashboard', WPC_URI . 'includes/dashboard/js/backend.js', [ 'jquery' ] );
			wp_localize_script( 'wpc-dashboard', 'wpc_dashboard_vars', [
					'nonce' => wp_create_nonce( 'wpc_dashboard' ),
				]
			);
		}

		function admin_menu() {
			$svg_icon     = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><path fill="#ffffff" d="M485,159.63c0,10.8,4.8,17,13.46,17.45l1,0a14.6,14.6,0,0,0,10.18-3.84,16.27,16.27,0,0,0,4.93-11.77c.56-27.68.58-56.13.08-84.54a16.48,16.48,0,0,0-4.9-11.85,14.9,14.9,0,0,0-11-4C490,61.43,485,67.73,485,78.42q0,14,0,27.93v23.79Q485,144.89,485,159.63Z"/><path fill="#ffffff" d="M513.53,394.66v32h18.26c11.52,0,18.25-6.88,18.25-15.86v-.3c0-10.32-7.18-15.86-18.7-15.86Z"/><path fill="#ffffff" d="M685.14,316C635.43,231.68,544.28,192.34,449.2,214.2,295.42,249.55,232.77,442.33,336.79,562.77c31.36,36.32,58.74,73.44,64,122.84,1.21,11.33,8.14,14.72,18.94,11.66,14.36-4.08,28.76-8,43.25-11.6,36.45-9,73-17.77,113.31-27.56a1.35,1.35,0,0,1,1.46,2c-6.18,9.38-13.65,8.58-19.36,10-48.8,12.35-97.74,24.19-146.68,36-9.62,2.34-17.71,7-15,17.77,2.4,9.68,11.38,11.08,20.33,8.79Q502.25,711,587.44,689.07c7.5-1.93,13.29-5.39,14-14.14,3.36-43.42,27-75.68,55.6-106.49C721.78,498.58,732.43,396.26,685.14,316ZM344,330.94a180.55,180.55,0,0,1,312.67.43c4.36,7.52-3.56,16.25-11.43,12.55l-.29-.13a9.11,9.11,0,0,1-4-3.67,162.57,162.57,0,0,0-281.25-.39,9,9,0,0,1-4,3.67l-.29.14C347.49,347.19,339.61,338.45,344,330.94Zm229.39,79.13v.3c0,24.24-18.85,36.81-42.34,36.81H513.53V468a11.52,11.52,0,0,1-23,0v-82.6A11.43,11.43,0,0,1,502,373.86h31.27C558.27,373.86,573.38,388.67,573.38,410.07ZM319.61,388.52a14.87,14.87,0,0,1-.9-4.34c0-6.43,5.39-11.22,11.82-11.22a11.62,11.62,0,0,1,11.22,8.23l20.8,64.19,20.95-63.59c1.8-5.39,5.84-9.13,11.67-9.13H397c5.83,0,9.87,3.59,11.67,9.13l20.95,63.59,20.8-64.19A11.52,11.52,0,0,1,461.46,373,11.12,11.12,0,0,1,472.83,384a15.86,15.86,0,0,1-.9,4.48l-29.32,82.3c-2.1,5.84-6.44,9.43-12,9.43h-2.4c-5.53,0-9.72-3.44-11.82-9.43l-20.65-60-20.65,60c-2.09,6-6.28,9.43-11.82,9.43h-2.39c-5.54,0-9.88-3.59-12-9.43ZM658.77,516a180.57,180.57,0,0,1-316.9.44c-4.18-7.58,3.72-16.15,11.54-12.43l.29.14a9,9,0,0,1,4.06,3.82,162.57,162.57,0,0,0,285.11-.4,9.13,9.13,0,0,1,4-3.83l.28-.13C655,499.88,662.93,508.42,658.77,516Zm18-48.79c-9.43,8.23-20.5,13.17-37.26,13.17-30.82,0-53.72-23.79-53.72-53.87v-.3c0-29.78,22.45-54.17,54.62-54.17,15.71,0,26.33,4.19,35,10.63a11.51,11.51,0,0,1,4.49,9.13A11.24,11.24,0,0,1,668.55,403a12.09,12.09,0,0,1-6.89-2.25c-6.43-4.78-13.16-7.48-21.39-7.48-17.66,0-30.38,14.67-30.38,32.62v.3c0,18,12.42,32.92,30.38,32.92,9.72,0,16.16-3,22.74-8.23a10.67,10.67,0,1,1,13.77,16.31Z"/><path fill="#ffffff" d="M581.78,785.79c-57.34,14.64-114.63,29.46-172,44-7.93,2-13.53,5.18-12,14.53s8.37,10.6,16.09,10.4H414a25,25,0,0,0,3.24-.41Q503.42,833,589.54,811.51c8.45-2.11,14.58-7.66,12.3-16.69C599.31,784.81,590.71,783.51,581.78,785.79Z"/><path fill="#ffffff" d="M583.81,724.72Q496,746.14,408.42,768.17c-7.21,1.8-11.67,6.14-10.66,14.34,1.05,8.45,7.08,10.32,14.13,10.92h.19a18.8,18.8,0,0,0,3.21-.25Q503,771.42,590.63,749.49c7.61-1.91,13.26-7,11.48-15.64C600,723.86,591.83,722.76,583.81,724.72Z"/><path fill="#ffffff" d="M803.13,638c-21.46-18.42-45.23-38.51-74.79-63.24a31,31,0,0,0-7.08-4.08l-.69-.32a8.91,8.91,0,0,0-5.44-.69c-5.36,1-9.06,4-11.64,9.35-5.1,10.57,3.14,17.68,7.1,21.1,22.63,19.51,46.24,39.52,72.2,61.17,4.89,4.08,9.72,6.13,14.17,6.13a14.68,14.68,0,0,0,11-5.14c2.94-3.19,4.33-6.6,4.13-10.13C811.8,647.58,808.88,643,803.13,638Z"/><path fill="#ffffff" d="M542.34,857.29c-26.87,21-54.38,21.7-83,3.45-6.76-4.3-15.3-6.65-20.22,2.56-4.47,8.34.63,15.15,7.29,19.81,15.26,10.69,32.19,17,52.17,17.35,21.37-.74,40.84-9.18,58.22-22.93,6.6-5.22,8.44-11.91,3.08-18.79C555,852.45,548.53,852.44,542.34,857.29Z"/><path fill="#ffffff" d="M279.56,571.47c-5.34-3.1-10.73-2.93-14.78.49-29.13,24.54-53.53,45.24-77.93,66.53-4.48,3.9-5.33,10.65-2.07,16.42a14.74,14.74,0,0,0,9.23,7.54,14.49,14.49,0,0,0,3.75.48,18.35,18.35,0,0,0,5.77-1,17.06,17.06,0,0,0,5.58-2.84c22.57-18.88,46.73-39.22,70.56-60,3.85-3.36,5.63-9.39,6.34-12.68C287.39,580.11,285,574.66,279.56,571.47Z"/><path fill="#ffffff" d="M869.8,382.83a15.89,15.89,0,0,0-11.46-4.7c-32.84-.44-63.21-.41-92.83.09a16.07,16.07,0,0,0-11.58,4.62,15.23,15.23,0,0,0-3.82,11.36c.45,11.53,9.23,14,16.57,14h.15c10.11,0,20.4,0,30.34,0H812v-.24l11.88,0c11.19,0,22.76.08,34.14-.11,9.32-.16,15.54-5.5,15.86-13.61A15.48,15.48,0,0,0,869.8,382.83Z"/><path fill="#ffffff" d="M682.59,252.67c6.7,0,12.67-5,15.24-7.15,16.63-13.91,33.34-28.28,49.51-42.17l9.89-8.49c5.37-4.62,8.22-9.6,8.48-14.82v-.21c-.33-6.08-2.81-10.23-7.79-13.05-8.38-4.76-15-.65-20.37,4-17.95,15.55-37,32.23-60,52.51-5.66,5-15.12,13.35-6,23.83C675.19,251.23,679,252.67,682.59,252.67Z"/><path fill="#ffffff" d="M185.52,408.12V408c3.68,0,7.36,0,11,0,10.43,0,21.21.09,31.81-.12a18.51,18.51,0,0,0,10.93-3.54,13,13,0,0,0,5.16-10,15.48,15.48,0,0,0-4.08-11.48A15.69,15.69,0,0,0,229,378.12c-32.68-.39-61.5-.29-88.13.33-4.62.1-8.46,1.69-11.09,4.59s-3.92,7.09-3.57,12c.62,8.61,5.89,13,15.67,13.08,5.71,0,11.48.05,17.2.05h26.44Z"/><path fill="#ffffff" d="M237.09,197.15c17.81,15.58,35.62,30.65,56.88,48.48,5.48,4.6,10.67,6.9,15.34,6.9,4.19,0,7.95-1.85,11.1-5.57,8.82-10.39-1.57-19.57-6.57-24-14.8-13.08-30-26.23-44.65-38.95l-12.93-11.22c-3.14-2.72-6.62-5.39-11.48-5.54a14.4,14.4,0,0,0-14.13,8.33C227.2,183,229.43,190.45,237.09,197.15Z"/></svg>';
			$encoded_icon = base64_encode( $svg_icon );
			add_menu_page(
				'WPClever',
				'WPClever',
				'manage_options',
				'wpclever',
				[ $this, 'admin_menu_content' ],
				"data:image/svg+xml;base64,$encoded_icon",
				26
			);
			add_submenu_page( 'wpclever', 'WPC About', 'About', 'manage_options', 'wpclever' );
		}

		function admin_menu_content() {
			add_thickbox();
			?>
            <div class="wpclever_page wpclever_welcome_page wrap">
                <h1>WPClever | Make clever moves</h1>
                <div class="card">
                    <h2 class="title">About</h2>
                    <p>
                        We are a team of passionate developers of plugins for WordPress, whose aspiration is to bring
                        smart utilities and functionalities to life for WordPress users, especially for those on
                        WooCommerce platform. Visit our website:
                        <a href="https://wpclever.net?utm_source=visit&utm_medium=menu&utm_campaign=wporg"
                           target="_blank">https://wpclever.net</a>
                    </p>
                </div>
                <div class="card wpclever_plugins">
                    <h2 class="title">Plugins
                        <span class="wpclever_plugins_order"><a href="#" class="wpclever_plugins_order_a" data-o="p">popular</a> |
						<a href="#" class="wpclever_plugins_order_a" data-o="u">last updated</a></span>
                    </h2>
                    <div class="wpclever_plugins_wrapper"></div>
                </div>
            </div>
			<?php
		}

		function ajax_get_plugins() {
			if ( ! isset( $_POST['security'] ) || ! wp_verify_nonce( sanitize_key( $_POST['security'] ), 'wpc_dashboard' ) ) {
				die( 'Permissions check failed!' );
			}

			if ( false === ( $plugins_arr = get_transient( 'wpclever_plugins' ) ) ) {
				$args    = (object) [
					'author'   => 'wpclever',
					'per_page' => '120',
					'page'     => '1',
					'fields'   => [
						'slug',
						'name',
						'version',
						'downloaded',
						'active_installs',
						'last_updated',
						'rating',
						'num_ratings',
						'short_description'
					]
				];
				$request = [
					'action'  => 'query_plugins',
					'timeout' => 30,
					'request' => serialize( $args )
				];
				// https://codex.wordpress.org/WordPress.org_API
				$url      = 'http://api.wordpress.org/plugins/info/1.0/';
				$response = wp_remote_post( $url, [ 'body' => $request ] );

				if ( ! is_wp_error( $response ) ) {
					$plugins_arr = [];
					$plugins     = unserialize( $response['body'] );

					if ( isset( $plugins->plugins ) && ( count( $plugins->plugins ) > 0 ) ) {
						foreach ( $plugins->plugins as $pl ) {
							$plugins_arr[] = [
								'slug'              => $pl->slug,
								'name'              => $pl->name,
								'version'           => $pl->version,
								'downloaded'        => $pl->downloaded,
								'active_installs'   => $pl->active_installs,
								'last_updated'      => strtotime( $pl->last_updated ),
								'rating'            => $pl->rating,
								'num_ratings'       => $pl->num_ratings,
								'short_description' => $pl->short_description,
							];
						}
					}

					set_transient( 'wpclever_plugins', $plugins_arr, 24 * HOUR_IN_SECONDS );
				} else {
					echo 'Have an error while loading the plugin list. Please visit our website <a href="https://wpclever.net?utm_source=visit&utm_medium=menu&utm_campaign=wporg" target="_blank">https://wpclever.net</a>';
				}
			}

			if ( is_array( $plugins_arr ) && ( count( $plugins_arr ) > 0 ) ) {
				array_multisort( array_column( $plugins_arr, 'active_installs' ), SORT_DESC, $plugins_arr );
				$i = 1;

				foreach ( $plugins_arr as $pl ) {
					if ( ! str_contains( $pl['name'], 'WPC' ) ) {
						continue;
					}

					echo '<div class="item" data-p="' . esc_attr( $pl['active_installs'] ?? 0 ) . '" data-u="' . esc_attr( $pl['last_updated'] ?? 0 ) . '" data-d="' . esc_attr( $pl['downloaded'] ?? 0 ) . '"><a class="thickbox" href="' . esc_url( network_admin_url( 'plugin-install.php?tab=plugin-information&amp;plugin=' . $pl['slug'] . '&amp;TB_iframe=true&amp;width=600&amp;height=550' ) ) . '" title="' . esc_attr( $pl['name'] ) . '"><span class="num">' . esc_html( $i ) . '</span><span class="title">' . esc_html( $pl['name'] ) . '</span><br/><span class="info">' . esc_html( 'Version ' . $pl['version'] . ( isset( $pl['last_updated'] ) ? ' - Last updated: ' . wp_date( 'M j, Y', $pl['last_updated'] ) : '' ) ) . '</span></a></div>';
					$i ++;
				}
			} else {
				echo 'Have an error while loading the plugin list. Please visit our website <a href="https://wpclever.net?utm_source=visit&utm_medium=menu&utm_campaign=wporg" target="_blank">https://wpclever.net</a>';
			}

			wp_die();
		}

		function ajax_get_suggestion() {
			if ( ! isset( $_POST['security'] ) || ! wp_verify_nonce( sanitize_key( $_POST['security'] ), 'wpc_dashboard' ) ) {
				die( 'Permissions check failed!' );
			}

			$get_suggestion = '';

			if ( false === ( $plugins_arr = get_transient( 'wpclever_plugins' ) ) ) {
				$plugins_arr = [];
				$args        = (object) [
					'author'   => 'wpclever',
					'per_page' => '120',
					'page'     => '1',
					'fields'   => [
						'slug',
						'name',
						'version',
						'downloaded',
						'active_installs',
						'last_updated',
						'rating',
						'num_ratings',
						'short_description'
					]
				];
				$request     = [
					'action'  => 'query_plugins',
					'timeout' => 30,
					'request' => serialize( $args )
				];
				// https://codex.wordpress.org/WordPress.org_API
				$url      = 'http://api.wordpress.org/plugins/info/1.0/';
				$response = wp_remote_post( $url, [ 'body' => $request ] );

				if ( ! is_wp_error( $response ) ) {
					$plugins = unserialize( $response['body'] );

					if ( isset( $plugins->plugins ) && ( count( $plugins->plugins ) > 0 ) ) {
						foreach ( $plugins->plugins as $pl ) {
							$plugins_arr[] = [
								'slug'              => $pl->slug,
								'name'              => $pl->name,
								'version'           => $pl->version,
								'downloaded'        => $pl->downloaded,
								'active_installs'   => $pl->active_installs,
								'last_updated'      => strtotime( $pl->last_updated ),
								'rating'            => $pl->rating,
								'num_ratings'       => $pl->num_ratings,
								'short_description' => $pl->short_description,
							];
						}
					}

					set_transient( 'wpclever_plugins', $plugins_arr, 24 * HOUR_IN_SECONDS );
				}
			}

			if ( is_array( $plugins_arr ) && ( count( $plugins_arr ) > 0 ) ) {
				array_multisort( array_column( $plugins_arr, 'last_updated' ), SORT_DESC, $plugins_arr );
				$plugins_arr = array_slice( $plugins_arr, 0, 5 );

				foreach ( $plugins_arr as $sg ) {
					$get_suggestion .= '<div><a href="' . esc_url( 'https://wordpress.org/plugins/' . $sg['slug'] . '/' ) . '" target="_blank">' . esc_html( $sg['name'] ) . '</a> - ' . esc_html( $sg['short_description'] ) . '</div>';
				}
			}

			echo wp_kses_post( $get_suggestion );

			wp_die();
		}
	}

	new WPCleverDashboard();
}
<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

if( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

if( ! class_exists( 'Forms_List_Table' ) ) { 
	
	/**
	 * Forms WP_List_Table
	 * @since 1.5.0
	 * @version 1.16.22
	 */
	class Forms_List_Table extends WP_List_Table {
		
		public $items;
		public $filters;
		protected $screen;
		
		/**
		 * Set up the Form list table
		 * @version 1.16.0
		 * @access public
		 */
		public function __construct(){
			// This global variable is required to create screen
			if( ! isset( $GLOBALS[ 'hook_suffix' ] ) ) { $GLOBALS[ 'hook_suffix' ] = null; }
			
			parent::__construct( array(
				'singular' => 'form',  // Singular name of the listed records
				'plural'   => 'forms', // Plural name of the listed records
				'ajax'     => false,
				'screen'   => null
			));
			
			// Hide default columns
			add_filter( 'default_hidden_columns', array( $this, 'get_default_hidden_columns' ), 10, 2 );
		}
		
		
		/**
		 * Get form list table columns
		 * @version 1.15.5
		 * @access public
		 * @return array
		 */
		public function get_columns(){
			// Set the columns
			$columns = array(
//				'cb'        => '<input type="checkbox" />',
				'id'        => _x( 'id', 'An id is a unique identification number', 'booking-activities' ),
				'title'     => __( 'Title', 'booking-activities' ),
				'author'    => __( 'Author', 'booking-activities' ),
				'date'      => __( 'Date', 'booking-activities' ),
				'status'    => _x( 'Status', 'Form status', 'booking-activities' ),
				'shortcode' => __( 'Shortcode', 'booking-activities' ),
				'active'    => __( 'Active', 'booking-activities' )
			);

			/**
			 * Columns of the form list
			 * You must use 'bookacti_form_list_columns_order' php filter to order your custom columns.
			 * You must use 'bookacti_form_list_default_hidden_columns' php filter to hide your custom columns by default.
			 * You must use 'bookacti_form_list_form_columns' php filter to fill your custom columns.
			 * 
			 * @param array $columns
			 */
			$columns = apply_filters( 'bookacti_form_list_columns', $columns );


			// Sort the columns
			$columns_order = array(
//				10 => 'cb',
				20 => 'id',
				30 => 'title',
				40 => 'shortcode',
				50 => 'author',
				60 => 'date',
				70 => 'status',
				80 => 'active'
			);

			/**
			 * Columns order of the form list
			 * Order the columns given by the filter 'bookacti_form_list_columns'
			 * 
			 * @param array $columns
			 */
			$columns_order = apply_filters( 'bookacti_form_list_columns_order', $columns_order );

			ksort( $columns_order );

			$displayed_columns = array();
			foreach( $columns_order as $column_id ) {
				$displayed_columns[ $column_id ] = $columns[ $column_id ];
			}

			// Return the columns
			return $displayed_columns;
		}
		
		
		/**
		 * Get default hidden columns
		 * @access public
		 * @param array $hidden
		 * @param WP_Screen $screen
		 * @return array
		 */
		public function get_default_hidden_columns( $hidden, $screen ) {
			if( $screen->id == $this->screen->id ) {
				$hidden = apply_filters( 'bookacti_form_list_default_hidden_columns', array(
					'status',
					'active'
				) );
			}
			return $hidden;
		}
		
		
		/**
		 * Get sortable columns
		 * @access public
		 * @return array
		 */
		protected function get_sortable_columns() {
			return array(
				'id'     => array( 'id', true ),
				'title'  => array( 'title', false ),
				'author' => array( 'user_id', false ),
				'date'   => array( 'creation_date', false ),
				'status' => array( 'status', false )
			);
		}
		
		
		/**
		 * Get the number of rows to display per page
		 * @since 1.16.22
		 * @return int
		 */
		public function get_rows_number_per_page() {
			$screen_option  = $this->screen ? $this->screen->get_option( 'per_page', 'option' ) : '';
			$screen_default = $this->screen ? $this->screen->get_option( 'per_page', 'default' ) : 0;
			$option_name    = $screen_option ? $screen_option : 'bookacti_forms_per_page';
			$option_default = $screen_default && intval( $screen_default ) > 0 ? intval( $screen_default ) : 20;
			$per_page       = $option_name ? $this->get_items_per_page( $option_name, $option_default ) : $option_default;
			
			return $per_page;
		}
		
		
		/**
		 * Prepare the items to be displayed in the list
		 * @version 1.16.22
		 * @access public
		 * @param array $filters
		 * @param boolean $no_pagination
		 */
		public function prepare_items( $filters = array(), $no_pagination = false ) {
			$this->get_column_info();
			$this->_column_headers[0] = $this->get_columns();
			
			$this->filters = $this->format_filters( $filters );
			
			if( ! $no_pagination ) {
				// Get the number of forms to display per page
				$per_page = $this->get_rows_number_per_page();

				// Set pagination
				$this->set_pagination_args( array(
					'total_items' => $this->get_total_items_count(),
					'per_page'    => $per_page
				) );

				$this->filters[ 'offset' ]   = ( $this->get_pagenum() - 1 ) * $per_page;
				$this->filters[ 'per_page' ] = $per_page;
			}
			
			$items = $this->get_form_list_items();
			
			$this->items = $items;
		}

		
		/**
		 * Fill columns
		 * @version 1.7.7
		 * @access public
		 * @param array $item
		 * @param string $column_name
		 * @return string
		 */
		public function column_default( $item, $column_name ) {
			$column_content = isset( $item[ $column_name ] ) ? $item[ $column_name ] : '';
			
			// Add primary data for responsive views
			$primary_column_name = $this->get_primary_column();
			if( $column_name === $primary_column_name && ! empty( $item[ 'primary_data_html' ] ) ) {
				$column_content .= $item[ 'primary_data_html' ];
			}
			
			return $column_content;
		}
		
		
		/**
		 * Fill "Title" column and add action buttons
		 * @version 1.16.2
		 * @access public
		 * @param array $item
		 * @return string
		 */
		public function column_title( $item ) {
			$form_id = $item[ 'id' ];
			$actions = array();
			
			if( current_user_can( 'bookacti_edit_forms' ) ) {
				if( $item[ 'active_raw' ] ) {
					$actions[ 'edit' ]      = '<a href="' . esc_url( admin_url( 'admin.php?page=bookacti_forms&action=edit&form_id=' . $form_id ) ) . '" >' . esc_html__( 'Edit' ) . '</a>';
					$actions[ 'duplicate' ] = '<a href="' . esc_url( wp_nonce_url( admin_url( 'admin.php?page=bookacti_forms&action=duplicate&form_id=' . $form_id ), 'duplicate-form_' . $form_id ) ) . '" >' . esc_html__( 'Duplicate', 'booking-activities' ) . '</a>';
				}
				if( current_user_can( 'bookacti_delete_forms' ) ) {
					if( $item[ 'active_raw' ] ) {
						$actions[ 'trash' ]   = '<a href="' . esc_url( wp_nonce_url( admin_url( 'admin.php?page=bookacti_forms&action=trash&form_id=' . $form_id ), 'trash-form_' . $form_id ) ) . '" >' . esc_html_x( 'Trash', 'verb' ) . '</a>';
					} else {
						$actions[ 'restore' ] = '<a href="' . esc_url( wp_nonce_url( admin_url( 'admin.php?page=bookacti_forms&action=restore&form_id=' . $form_id ), 'restore-form_' . $form_id ) ) . '" >' . esc_html__( 'Restore' ) . '</a>';
						$actions[ 'delete' ]  = '<a href="' . esc_url( wp_nonce_url( admin_url( 'admin.php?page=bookacti_forms&status=trash&action=delete&form_id=' . $form_id ), 'delete-form_' . $form_id ) ) . '" >' . esc_html__( 'Delete Permanently' ) . '</a>';
					}
				}
			}
			
			// Add primary data for responsive views
			$primary_column_name = $this->get_primary_column();
			$primary_data_html = '';
			if( $primary_column_name === 'title' && ! empty( $item[ 'primary_data_html' ] ) ) {
				$primary_data_html = $item[ 'primary_data_html' ];
			}
			
			// Add a span and a class to each action
			$actions = apply_filters( 'bookacti_form_list_row_actions', $actions, $item );
			foreach( $actions as $action_id => $link ) {
				$actions[ $action_id ] = '<span class="' . $action_id . '">' . $link . '</span>';
			}
			
			// Display an error message if the form author cannot manage displayed templates
			$errors = '';
			if( ! empty( $item[ 'errors' ] ) ) {
				$errors .= '<div class="bookacti-error"><span class="dashicons dashicons-warning"></span><span><ul class="bookacti-error-list"><li>';
				$errors .= implode( '<li>', $item[ 'errors' ] );
				$errors .= '</ul></span></div>';
			}
			
			return sprintf( '%1$s%2$s %3$s', $item[ 'title' ], $primary_data_html, $this->row_actions( $actions, false ) ) . $errors;
		}
		
		
		/**
		 * Get form list items. Parameters can be passed in the URL.
		 * @version 1.16.13
		 * @access public
		 * @return array
		 */
		public function get_form_list_items() {
			$forms    = bookacti_get_forms( $this->filters );
			$form_ids = $forms ? array_keys( $forms ) : array();
			
			$forms_calendar = $form_ids ? bookacti_get_forms_field_data_by_name( $form_ids, 'calendar' ) : array();
			
			$can_edit_forms = current_user_can( 'bookacti_edit_forms' );
			$can_edit_users = current_user_can( 'edit_users' );
			
			// Get author users
			$user_ids = array();
			foreach( $forms as $form ) {
				if( $form->user_id && is_numeric( $form->user_id ) && ! in_array( $form->user_id, $user_ids, true ) ){ $user_ids[] = $form->user_id; }
			}
			$users = $user_ids ? bookacti_get_users_data( array( 'include' => $user_ids ) ) : array();
			
			$date_format      = get_option( 'date_format' );
			$utc_timezone_obj = new DateTimeZone( 'UTC' );
			$timezone         = function_exists( 'wp_timezone_string' ) ? wp_timezone_string() : get_option( 'timezone_string' );
			try { $timezone_obj = new DateTimeZone( $timezone ); }
			catch ( Exception $ex ) { $timezone_obj = clone $utc_timezone_obj; }
			
			// Form list
			$form_list_items = array();
			foreach( $forms as $form ) {
				// If the user is not allowed to manage this form, do not display it at all
				if( ! bookacti_user_can_manage_form( $form->id ) ) { continue; }
				
				$id     = $form->id;
				$active = $form->active ? __( 'Yes', 'booking-activities' ) : __( 'No', 'booking-activities' );
				
				// Format title column
				$title = ! empty( $form->title ) ? esc_html( apply_filters( 'bookacti_translate_text', $form->title ) ) : sprintf( esc_html__( 'Form #%d', 'booking-activities' ), $id );
				if( $can_edit_forms ) {
					$title = '<a href="' . esc_url( admin_url( 'admin.php?page=bookacti_forms&action=edit&form_id=' . $id ) ) . '" >' . $title . '</a>';
				}
				
				// Build shortcode
				$shortcode = "<input type='text' onfocus='this.select();' readonly='readonly' value='" . esc_attr( '[bookingactivities_form form="' . $id . '"]' ) . "' class='large-text code'>";
				
				// Author name
				$user_object = ! empty( $users[ $form->user_id ] ) ? $users[ $form->user_id ] : null;
				$author      = $user_object ? $user_object->display_name : esc_html( __( 'Unknown user', 'booking-activities' ) . ' (' . $form->user_id . ')' );
				if( $can_edit_users && $user_object ) {
					$author = '<a href="' . get_edit_user_link( $user_object->ID ) . '">' . $author . '</a>';
				}
				
				// Creation date
				$creation_date_raw = ! empty( $form->creation_date ) ? bookacti_sanitize_datetime( $form->creation_date ) : '';
				$creation_date_dt = new DateTime( $creation_date_raw, $utc_timezone_obj );
				$creation_date_dt->setTimezone( $timezone_obj );
				$creation_date = $creation_date_raw ? bookacti_format_datetime( $creation_date_dt->format( 'Y-m-d H:i:s' ), $date_format ) : '';
				$creation_date = $creation_date ? '<span title="' . esc_attr( $form->creation_date ) . '">' . $creation_date . '</span>' : '';
				
				// Add info on the primary column to make them directly visible in responsive view
				$primary_data = array( '<span class="bookacti-column-id" >(' . esc_html_x( 'id', 'An id is a unique identification number', 'booking-activities' ) . ': ' . $id . ')</span>' );
				$primary_data_html = '<div class="bookacti-primary-data-container">';
				foreach( $primary_data as $single_primary_data ) {
					$primary_data_html .= '<span class="bookacti-primary-data">' . $single_primary_data . '</span>';
				}
				$primary_data_html .= '</div>';
				
				// Check calendar permissions
				$calendar_field = isset( $forms_calendar[ $form->id ] ) ? $forms_calendar[ $form->id ] : array();
				$template_ids   = ! empty( $calendar_field[ 'calendars' ] ) ? array_values( bookacti_ids_to_array( $calendar_field[ 'calendars' ] ) ) : array();
				$can_manage_calendars = $user_object ? bookacti_user_can_manage_template( $template_ids, intval( $user_object->ID ) ) : false;
				
				// Errors
				$errors = array();
				if( ! $can_manage_calendars ) {
					$docs_link      = 'https://booking-activities.fr/en/docs/user-documentation/advanced-use-of-booking-activities/give-access-rights-to-calendars-and-bookings-to-your-collaborators/';
					$docs_link_html = '<a href="' . $docs_link . '" target="_blank">' . esc_html__( 'documentation', 'booking-activities' ) . '</a>';
					$errors[ 'calendars_not_allowed' ] = sprintf( 
						/* translators: %1$s = "Administrator", %2$s = "Users", %3$s = "All users", %4$s = "Role", %5$s = Link to the "documentation" */
						esc_html__( 'Some events may not be displayed because the form author is not "%1$s" (%2$s > %3$s > the author > %4$s), or does not have permission to manage the displayed calendars (%5$s).', 'booking-activities' ), 
						_x( 'Administrator', 'User role' ), __( 'Users' ), __( 'All Users' ), __( 'Role' ), $docs_link_html
					);
				}
				
				$form_item = apply_filters( 'bookacti_form_list_form_columns', array( 
					'id'                => $id,
					'title'             => $title,
					'shortcode'         => $shortcode,
					'author'            => $author,
					'user_id'           => $form->user_id,
					'date'              => $creation_date,
					'status'            => $form->status,
					'active'            => $active,
					'active_raw'        => $form->active,
					'errors'            => $errors,
					'primary_data'      => $primary_data,
					'primary_data_html' => $primary_data_html
				), $form );
				
				$form_list_items[] = $form_item;
			}
			
			return $form_list_items;
		}
		
		
		/**
		 * Format filters passed as argument or retrieved via POST or GET
		 * @version 1.16.0
		 * @access public
		 * @param array $filters_raw
		 * @return array
		 */
		public function format_filters( $filters_raw = array() ) {
			// Get filters from URL if no filter was directly passed
			if( ! $filters_raw ) {
				$filters_raw = array(
					'id'       => isset( $_REQUEST[ 'id' ] )      ? $_REQUEST[ 'id' ] : array(), 
					'title'    => isset( $_REQUEST[ 'title' ] )   ? $_REQUEST[ 'title' ] : '', 
					'user_id'  => isset( $_REQUEST[ 'user_id' ] ) ? $_REQUEST[ 'user_id' ] : 0, 
					'status'   => isset( $_REQUEST[ 'status' ] )  ? $_REQUEST[ 'status' ] : array(), 
					'active'   => isset( $_REQUEST[ 'active' ] )  ? ( $_REQUEST[ 'active' ] ? 1 : 0 ) : false, 
					'order_by' => isset( $_REQUEST[ 'orderby' ] ) ? $_REQUEST[ 'orderby' ] : array( 'id' ),
					'order'    => isset( $_REQUEST[ 'order' ] )   ? $_REQUEST[ 'order' ] : 'DESC'
				);
			}
			
			// Format filters before making the request
			$filters = bookacti_format_form_filters( $filters_raw );
			
			if( empty( $filters[ 'status' ] ) ) {
				$filters[ 'status' ] = array( 'publish' );
			}
			
			return $filters;
		}
		
		
		/**
		 * Get the total amount of forms according to filters
		 * @access public
		 * @return int
		 */
		public function get_total_items_count() {
			return bookacti_get_number_of_form_rows( $this->filters );
		}
		
		
		/**
		 * Get the tbody element for the list table
		 * @access public
		 * @return string
		 */
		public function get_rows_or_placeholder() {
			if ( $this->has_items() ) {
				return $this->get_rows();
			} else {
				return '<tr class="no-items"><td class="colspanchange" colspan="' . esc_attr( $this->get_column_count() ) . '">' . esc_html__( 'No items found.', 'booking-activities' ) . '</td></tr>';
			}
		}
		
		
		/**
		 * Generate the table rows
		 * @access public
		 * @return string
		 */
		public function get_rows() {
			$rows = '';
			foreach ( $this->items as $item ) {
				$rows .= $this->get_single_row( $item );
			}
			return $rows;
		}
		
		
		/**
		 * Returns content for a single row of the table
		 * @access public
		 * @param array $item The current item
		 * @return string
		 */
		public function get_single_row( $item ) {
			$row  = '<tr>';
			$row .= $this->get_single_row_columns( $item );
			$row .= '</tr>';
			
			return $row;
		}
		
		/**
		 * Returns the columns for a single row of the table
		 * 
		 * @access public
		 * @param object $item The current item
		 */
		public function get_single_row_columns( $item ) {
			
			list( $columns, $hidden, $sortable, $primary ) = $this->get_column_info();
			
			$returned_columns = '';
			foreach ( $columns as $column_name => $column_display_name ) {
				$classes = "$column_name column-$column_name";
				if ( $primary === $column_name ) {
					$classes .= ' has-row-actions column-primary';
				}

				if ( in_array( $column_name, $hidden, true ) ) {
					$classes .= ' hidden';
				}

				// Comments column uses HTML in the display name with screen reader text.
				// Instead of using esc_attr(), we strip tags to get closer to a user-friendly string.
				$data = 'data-colname="' . wp_strip_all_tags( $column_display_name ) . '"';

				$attributes = "class='$classes' $data";
				
				if ( 'cb' === $column_name ) {
					$returned_columns .= '<th scope="row" class="check-column">';
					$returned_columns .=  $this->column_cb( $item );
					$returned_columns .=  '</th>';
				} elseif ( method_exists( $this, '_column_' . $column_name ) ) {
					$returned_columns .=  call_user_func(
											array( $this, '_column_' . $column_name ),
											$item,
											$classes,
											$data,
											$primary
										);
				} elseif ( method_exists( $this, 'column_' . $column_name ) ) {
					$returned_columns .=  "<td $attributes>";
					$returned_columns .=  call_user_func( array( $this, 'column_' . $column_name ), $item );
					$returned_columns .=  $this->handle_row_actions( $item, $column_name, $primary );
					$returned_columns .=  "</td>";
				} else {
					$returned_columns .=  "<td $attributes>";
					$returned_columns .=  $this->column_default( $item, $column_name );
					$returned_columns .=  $this->handle_row_actions( $item, $column_name, $primary );
					$returned_columns .=  "</td>";
				}
			}
			
			return $returned_columns;
		}
		
		
		/**
		 * Display content for a single row of the table
		 * 
		 * @access public
		 * @param array $item The current item
		 */
		public function single_row( $item ) {
			echo '<tr>';
			$this->single_row_columns( $item );
			echo '</tr>';
		}

		
		/**
		 * Get an associative array( id => link ) with the list of views available on this table
		 * @version 1.16.0
		 * @return array
		 */
		protected function get_views() {
			$is_trash        = isset( $_REQUEST[ 'status' ] ) && $_REQUEST[ 'status' ] === 'trash';
			$filters         = bookacti_format_form_filters();
			$published_count = bookacti_get_number_of_form_rows( array_merge( $filters, array( 'status' => array( 'publish' ) ) ) );
			$trash_count     = bookacti_get_number_of_form_rows( array_merge( $filters, array( 'status' => array( 'trash' ) ) ) );
			
			return array(
				'published' => '<a href="' . esc_url( admin_url( 'admin.php?page=bookacti_forms' ) ) . '" class="' . ( ! $is_trash ? 'current' : '' ) . '" >' . esc_html__( 'Published' ) . ' <span class="count">(' . $published_count . ')</span></a>',
				'trash'     => '<a href="' . esc_url( admin_url( 'admin.php?page=bookacti_forms&status=trash' ) ) . '" class="' . ( $is_trash ? 'current' : '' ) . '" >' . esc_html_x( 'Trash', 'noun' ) . ' <span class="count">(' . $trash_count . ')</span></a>'
			);
		}
		
		
		/**
		 * Generate row actions div
		 * @access protected
		 * @param array $actions
		 * @param bool $always_visible
		 * @return string
		 */
		protected function row_actions( $actions, $always_visible = false ) {
			$action_count = count( $actions );
			$i = 0;

			if( ! $action_count ) { return ''; }

			$class_visible = $always_visible ? 'visible' : '';
			$out = '<div class="row-actions ' . esc_attr( $class_visible ) . '">';
			foreach ( $actions as $action => $link ) {
				++$i;
				$sep = $i == $action_count ? '' : ' | ';
				$out .= $link . $sep;
			}
			$out .= '</div>';

			return $out;
		}
		
		
		/**
		 * Get default primary column name
		 * 
		 * @access public
		 * @return string
		 */
		public function get_default_primary_column_name() {
			return apply_filters( 'bookacti_form_list_primary_column', 'title', $this->screen );
		}
		
		
		/**
		 * Gets a list of CSS classes for the WP_List_Table table tag.
		 * @since 1.15.5
		 * @return string[] Array of CSS classes for the table tag.
		 */
		protected function get_table_classes() {
			$classes = parent::get_table_classes();
			$classes[] = 'bookacti-list-table';
			return $classes;
		}
	}
}
<?php

add_action('admin_menu', 'add_export_report_menu_item');
add_action('admin_init', 'handle_export_report');

function add_export_report_menu_item() {
    add_submenu_page(
        'tutor',
        'Export Report',
        'Export Report',
        'manage_options',
        'export_lms_report',
        'export_report_page_callback'
    );
}

function export_report_page_callback()
{
	$last_export = get_option('_last_users_data_export');
	$last_export_by = get_option('_last_users_data_export_by');
	$last_export = $last_export ? $last_export : 'N/A';
	$last_export_by = $last_export_by ? $last_export_by : 'N/A';

    // Get first course
    $courses = get_posts(array(
        'post_type' => 'courses',
        'numberposts' => -1,
        'order' => 'ASC',
        'orderby' => 'ID'
    ));

    // Set default start and end dates
    $default_start_date = $courses ? $courses[0]->post_date : '';
    if(!empty($default_start_date)) {
        $default_start_date = date('Y-m-d', strtotime($default_start_date));
    }
    $default_end_date = date('Y-m-d', strtotime('today'));

    ob_start();
    ?>
    <div class="wrap">
        <h2>Export All Report Data</h2><br/>
        <form method="post" action="">

            <!-- Filter by Course -->
            <div class="course-filter">
                <label for="course_filter"><strong>Select Course:</strong></label>
                <select name="course_filter" id="course_filter">
                    <option value="all">All Courses</option>
                    <?php
                    foreach ($courses as $course) {
                        echo '<option value="' . esc_attr($course->ID) . '">' . esc_html($course->post_title) . ' (#' . esc_attr($course->ID) .')</option>';
                    }
                    ?>
                </select>
            </div><br/>

            <!-- Filter by Date Range -->
            <div class="date-filter">
                <label for="start_date"><strong>Start Date:</strong></label>
                    <input type="date" name="start_date" id="start_date" value="<?php echo $default_start_date ?>" min="<?php echo $default_start_date ?>"/>
                <label for="end_date"><strong>End Date:</strong></label>
                    <input type="date" name="end_date" id="end_date" value="<?php echo $default_end_date ?>" max="<?php echo $default_end_date ?>"/>
            </div><br/>

            <!-- Filter by Payment Status -->
            <!-- <div class="payment-filter">
                <label for="payment_status"><strong>Payment Status:</strong></label>
                    <select name="payment_status" id="payment_status">
                        <option value="wc-completed">Completed</option>
                        <option value="wc-processing">Processing</option>
                        <option value="wc-pending">Pending payment</option>
                        <option value="wc-on-hold">On hold</option>
                        <option value="wc-cancelled">Cancelled</option>
                        <option value="wc-refunded">Refunded</option>
                        <option value="wc-failed">Failed</option>
                        <option value="">All</option>
                    </select>
            </div><br/> -->

            <!-- Export button -->
            <div class="export-button">
                <input type="hidden" name="export_report" value="true" />
                <input type="submit" value="Export Report Data" class="button-primary" />
            </div>
        </form>
    </div>
	<p>Last Export: <?php echo $last_export; ?> By: <?php echo $last_export_by; ?></p>
    <?php
    $output = ob_get_clean();
    echo $output;
}

function handle_export_report()
{
    if (isset($_POST['export_report']) && $_POST['export_report'] === 'true') {
		// Save last export date
		$timestamp = current_datetime()->format('Y-m-d H:i:s');
		update_option('_last_users_data_export', $timestamp);
		update_option('_last_users_data_export_by', wp_get_current_user()->user_login . ' (#' . wp_get_current_user()->ID . ')');

        // Set default values
        $posts = get_posts(array(
            'post_type' => 'any',
            'numberposts' => 1,
            'order' => 'ASC',
            'orderby' => 'ID'
        ));
        $default_start_date = date('Y-m-d', $posts->post_date);
        $default_end_date = date('Y-m-d', strtotime('today'));

        // Get filter values
        $course_filter = isset($_POST['course_filter']) ? $_POST['course_filter'] : '';
        $start_date = isset($_POST['start_date']) ? sanitize_text_field($_POST['start_date']) : $default_start_date;
        $end_date = isset($_POST['end_date']) ? sanitize_text_field($_POST['end_date']) : $default_end_date;
        $payment_status_filter = isset($_POST['payment_status']) ? sanitize_text_field($_POST['payment_status']) : '';

        $payment_status_default = array(
            'wc-completed',
            'wc-processing',
            // 'wc-pending',
            // 'wc-on-hold',
            // 'wc-cancelled',
            // 'wc-refunded',
            // 'wc-failed',
        );
        $payment_status_filter = empty($payment_status_filter) ? $payment_status_default : array($payment_status_filter);


        // Prepare CSV Header
		$csv_data_header = "ID,Username,Display Name,Email,Enrolled Courses,";
		foreach (CUSTOM_FIELDS as $key => $value) {
			$value = str_replace(',', ' ', $value);
			$csv_data_header .= $value . ',';
		}

		$csv_data_header .= "Order IDs,Payment Statuses,Transaction Numbers,Transaction dates,Payments,Total Amount\n";

        // Query all users
		$args = array(
            'role'         => 'subscriber',
            'number'       => -1,
        );
        $users = get_users($args);
        
        $csv_data_body_row = '';

        // Prepare CSV Body Data
        foreach ($users as $user) {
            // Fetch order data
            $order_ids = array();
            $payment_statuses = array();
            $transaction_numbers = array();
            $transaction_dates = array();
            $total_payments = array();
            $has_valid_order = false;

			if (class_exists('WooCommerce')) {
				$user_orders = wc_get_orders(array(
					'customer' => $user->ID,
					'status' => $payment_status_default,
                    'date_created' => $start_date . '...' . $end_date,
					// 'status' => $payment_status_filter
				));

				if (!empty($user_orders)) {

                    foreach($user_orders as $order) {
                        $order_date = $order->get_date_completed();
                        $transaction_dates[] = wc_format_datetime( $order_date, 'd M Y' );
                        $order_ids[] = $order->get_order_number();
                        $payment_statuses[] = $order->get_status();
                        $total_payments[] = $order->get_total();
                        
                        // Extract PayuBiz transaction number from order notes
                        $order_notes = wc_get_order_notes(array(
                            'order_id' => $order->get_id(),
                        ));
                        
                        $transaction_number;
                        foreach ($order_notes as $note) {
                            if (strpos($note->content, 'Ref Number') !== false) {
                                $note_content = $note->content;
                                preg_match('/Ref Number:\s*(\d+)/', $note_content, $matches);
                                if (!empty($matches[1])) {
                                    $transaction_number = $matches[1];
                                    break; // Stop searching once transaction number is found
                                }
                            }
                        }
                        $transaction_numbers[] = $transaction_number;
                        $has_valid_order = true;
                    }
				}
			}

            // If no valid order found, skip this user
            if (!$has_valid_order) {
                continue;
            }

            // Get enrolled courses list
			$enrolled_courses = array();
			if ( function_exists('tutor_utils') ) {
                // Get all courses user enrolled
				$enrolled_courses = tutor_utils()->get_enrolled_courses_ids_by_user($user->ID);
                $enrolled_courses['all'] = 'all'; // Add all in course list for the filter
                
                if (!empty($course_filter) && !in_array($course_filter, $enrolled_courses)) {
                    continue; // Skip user not enrolled in the selected course in filter
                }

                unset($enrolled_courses['all']); // Remove added all in course enrolled

				if(!empty($enrolled_courses) && is_array($enrolled_courses)) {
					foreach($enrolled_courses as $key => $course_id) {
						$course_title = get_the_title($course_id);
						$enrolled_courses[$key] = str_replace(',', ' ', $course_title); // Escape the comma for csv
					}
					$enrolled_courses = implode('; ', $enrolled_courses);
				} else {
					$enrolled_courses = '-';
				}  
            } else {
				$enrolled_courses = '-';
			}

            // Start user data
			$csv_data_body_data = "{$user->ID},{$user->user_login},{$user->display_name},{$user->user_email},{$enrolled_courses},";

            // Append custom fields
			foreach(CUSTOM_FIELDS as $key => $value) {
				$userdata = get_user_meta($user->ID, '_' . $key, true);
				$userdata = str_replace(',', ' ', $userdata); // Escape the comma for csv
				$csv_data_body_data .= $userdata . ',';
			}

            // Start Order data
            $csv_data_order_data = implode('; ', $order_ids) . ',' . 
                        implode('; ', $payment_statuses) . ',' . 
                        implode('; ', $transaction_numbers) . ',' . 
                        implode('; ', $transaction_dates) . ',' . 
                        implode('; ', $total_payments) . ',';
			
			// Get total spend
			$total_spent = wc_get_customer_total_spent($user->ID);
			$csv_data_order_data .= $total_spent ? $total_spent : 0;
			
            // Merge User & Order Data
			$csv_data_body_row .= $csv_data_body_data . $csv_data_order_data . "\n";
        }

        // Handle epostraphy escape
        $csv_data_body = str_replace('&#8217;', '\'', $csv_data_body_row);

        // Output CSV file
        header("Content-type: text/csv; charset=UTF-8");
        header("Content-Disposition: attachment; filename=" . get_option('blogname') . " " . str_replace(' ', '-', $timestamp) . ".csv");
        header("Pragma: no-cache");
        header("Expires: 0");
        echo $csv_data_header;
        echo $csv_data_body;
        exit;
    }
}

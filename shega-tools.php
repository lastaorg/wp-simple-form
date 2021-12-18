<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://example.com
 * @since             1.0.0
 * @package           Shega_Tool
 *
 * @wordpress-plugin
 * Plugin Name:       Shega Tools
 * Plugin URI:        https://shega.co/tools/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Shega
 * Author URI:        https://ba5liel.github.io
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       shega-tool
 * Domain Path:       /languages
 */


require_once(__DIR__ . '/includes/class-PHPFormBuilder.php');
require_once(__DIR__ . '/admin/class-WPSimpleFormAdmin.php');

// Main Plugin Class
if (!class_exists('WPSimpleForm')) {
    class WPSimpleForm
    {
        public function __construct()
        {
            add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
            add_shortcode('shega-tool-income-tax', array($this, 'incometax'));
            add_shortcode('shega-tool-vat', array($this, 'vat'));
            add_action('admin_post_nopriv_wpsf_contact_form', array($this, 'form_handler'));
            add_action('admin_post_wpsf_contact_form', array($this, 'form_handler'));
        }

        public function enqueue_scripts()
        {
            wp_enqueue_style('wpsimpleform', plugins_url('/public/css/style.css', __FILE__), array(), 0.1);
            wp_enqueue_script('wpsimpleform', plugins_url('/public/js/main.js', __FILE__), array(), 0.1);
        }

        public function incometax($atts)
        {
            global $post;

            $atts = shortcode_atts(
                array(
                    'add_honeypot' => false,
                ),
                $atts,
                'wpsimpleform'
            );



            // Shortcodes should not output data directly
            ob_start();

            // Status message
            $status = filter_input(INPUT_GET, 'status', FILTER_VALIDATE_INT);

            if ($status == 1) {
                printf('<div class="wp-simpleform message success"><p>%s</p></div>', __('Submitted successfully!', 'wp-simple-form'));
            }

            // Build the form
?>

            <div class="" style="margin: 0; width: 60%">
                <h1 id="calc-header">Personal Income Tax Calculator</h1>
                <p id="calc-desc">
                    We have created this Income Tax calculator as a free to use tool for calculating Income Tax rates in Ethiopia.
                </p>
                <div class="formQuestion">
                    <div class="question">
                        <!-- 
                        <label for="monthlySalary">Monthly Salary</label> -->
                        <input id="monthlySalary" name="monthlySalary" type="number" placeholder="Monthly Salary" />
                        <div class="select">
                            <select id="income-tax-type">
                                <option selected="selected" value="gross">Gross Salary</option>
                                <option value="net">Net Salary</option>
                            </select>
                        </div>

                        <p><small>This calculator assumes you are Ethiopian tax payer.</small></p>
                        <p id="taxError"><small></small></p>
                    </div>
                    <button id="submitTax" onclick="submitTaxFuc()" class="btn btn-small bg-grey-dark-one" style="background: #faa31b;">Calculate your tax</button>
                </div>


                <div id="results">
                    <table class="calc-result-table">
                        <tr>
                            <td id="grossSalary-text">Gross Salary</td>
                            <td class="sub-result grossSalary"></td>
                        </tr>
                        <tr>
                            <td id="incomeTax-text">Income Tax</td>
                            <td class="sub-result incomeTax"></td>
                        </tr>
                        <tr>
                            <td id="pention-text">Pension Contribution</td>
                            <td class="sub-result pention"></td>
                        </tr>
                        <tr id="main-result-row">
                            <td id="main-result-text">Net Salary</td>
                            <td class="main-result netSalary"></td>
                        </tr>
                    </table>

                    <div><button id="back-to-calc" onclick="back_to_calc()">&lt;&lt;Back to Calculator</button></div>

                </div>

                <div id="results-2">
                    <table class="calc-result-table">
                        <tr>
                            <td id="netsalary-text">Net Salary</td>
                            <td class="sub-result netSalary"></td>
                        </tr>
                        <tr>
                            <td id="incomeTax-text">Income Tax</td>
                            <td class="sub-result incomeTax"></td>
                        </tr>
                        <tr>
                            <td id="pention-text">Pension Contribution</td>
                            <td class="sub-result pention"></td>
                        </tr>
                        <tr id="main-result-row">
                            <td id="main-result-text">Gross Salary</td>
                            <td class="main-result grossSalary"></td>
                        </tr>
                    </table>

                    <div><button id="back-to-calc" onclick="back_to_calc()">&lt;&lt;Back to Calculator</button></div>

                </div>
            </div>
            <script type="text/javascript">
                function submitTaxFuc() {
                    (function($) {

                        console.log("clikecled");
                        $("#taxError").hide();
                        $("#results").hide();
                        $("#results-2").hide();
                        let netSalary;
                        let grossSalary;
                        let pention;
                        let incomeTax;
                        let rate;
                        let deducatable;
                        let pention_rate = .07;
                        if ($("#income-tax-type").val() == "gross") {
                            grossSalary = Number.parseFloat($("#monthlySalary").val());
                            if (!grossSalary || grossSalary == 0) {

                                $("#taxError").html("<small>Please Enter a  valid salary.</small>");
                                $("#taxError").show();
                                return;
                            }

                            if (grossSalary <= 600) {
                                rate = 0;
                                deducatable = 0;
                            } else if (600 < grossSalary && grossSalary <= 1650) {
                                rate = 0.1;
                                deducatable = 60;
                            } else if (1650 < grossSalary && grossSalary <= 3200) {
                                rate = 0.15;
                                deducatable = 142.5;
                            } else if (3200 < grossSalary && grossSalary <= 5250) {
                                rate = 0.20;
                                deducatable = 302.50;
                            } else if (5250 < grossSalary && grossSalary <= 7800) {
                                rate = 0.25;
                                deducatable = 565.00;
                            } else if (7800 < grossSalary && grossSalary <= 10900) {
                                rate = 0.30;
                                deducatable = 955.00;
                            } else if (10900 < grossSalary) {
                                rate = 0.35;
                                deducatable = 1500.00;
                            } else {
                                console.log("noting matxched", grossSalary, typeof grossSalary);
                            }

                            pention = pention_rate * grossSalary;
                            incomeTax = (rate * grossSalary) - deducatable;
                            netSalary = grossSalary - pention - incomeTax;

                            $(".pention").text(pention.toFixed(2) + " ETB");
                            $(".incomeTax").text(incomeTax.toFixed(2) + " ETB");
                            $(".netSalary").text(netSalary.toFixed(2) + " ETB");
                            $(".grossSalary").text(grossSalary.toFixed(2) + " ETB");
                            $(".formQuestion").hide();
                            $("#results").show(500);

                        } else {
                            netSalary = Number.parseFloat($("#monthlySalary").val());
                            if (!grossSalary || grossSalary == 0) {

                                $("#taxError").html("<small>Please Enter a  valid salary.</small>");
                                $("#taxError").show();
                                return;
                            }

                            if (netSalary <= 558.00) {
                                rate = 0;
                                deducatable = 0;
                            } else if (558 < netSalary && netSalary <= 1429.50) {
                                rate = 0.1;
                                deducatable = 60;
                            } else if (1429.50 < netSalary && netSalary <= 2638.50) {
                                rate = 0.15;
                                deducatable = 142.5;
                            } else if (2638.50 < netSalary && netSalary <= 4135.00) {
                                rate = 0.20;
                                deducatable = 302.50;
                            } else if (4135.00 < netSalary && netSalary <= 5869.00) {
                                rate = 0.25;
                                deducatable = 565.00;
                            } else if (5869.00 < netSalary && netSalary <= 7822.00) {
                                rate = 0.30;
                                deducatable = 955.00;
                            } else if (7822.00 < netSalary) {
                                rate = 0.35;
                                deducatable = 1500.00;
                            } else {
                                console.log("noting matxched", netSalary, typeof netSalary);
                            }
                            grossSalary = (netSalary + deducatable) / (1 - rate - pention_rate)
                            pention = pention_rate * grossSalary;
                            incomeTax = (rate * grossSalary) - deducatable;
                            netSalary = grossSalary - pention - incomeTax;

                            $(".pention").text(pention.toFixed(2) + " ETB");
                            $(".incomeTax").text(incomeTax.toFixed(2) + " ETB");
                            $(".netSalary").text(netSalary.toFixed(2) + " ETB");
                            $(".grossSalary").text(grossSalary.toFixed(2) + " ETB");
                            $(".formQuestion").hide();
                            $("#results-2").show(500);

                        }



                    })(jQuery)
                }

                function back_to_calc() {
                    (function($) {
                        console.log("cliked1")
                        $("#results").hide();

                        $(".formQuestion").show(500);
                    })(jQuery)
                }
            </script>
        <?php
            // Return and clean buffer contents
            return ob_get_clean();
        }

        public function vat($atts)
        {
            global $post;

            $atts = shortcode_atts(
                array(
                    'add_honeypot' => false,
                ),
                $atts,
                'wpsimpleform'
            );



            // Shortcodes should not output data directly
            ob_start();

            // Status message
            $status = filter_input(INPUT_GET, 'status', FILTER_VALIDATE_INT);

            if ($status == 1) {
                printf('<div class="wp-simpleform message success"><p>%s</p></div>', __('Submitted successfully!', 'wp-simple-form'));
            }

            // Build the form
        ?>

            <div class="" style="margin: 0; width: 60%">
                <h1 id="calc-header">VAT Calculator</h1>
                <p id="calc-desc">
                    This tool will help you calculate VAT from your payment in Ethiopia.
                </p>
                <div class="formQuestion">
                    <div class="question">
                        <!-- 
                        <label for="monthlySalary">Monthly Salary</label> -->
                        <input id="payment" name="payment" type="number" placeholder="Payment" />
                        <div class="select">
                            <select id="vat-type">
                                <option selected="selected" value="inclusive">Including Tax</option>
                                <option value="exclusive">Excluding VAT</option>
                            </select>
                        </div>

                        <p><small>This calculator assumes you are in Ethiopia.</small></p>
                        <p id="taxError"><small></small></p>
                    </div>
                    <button id="submitTax" onclick="submitTaxFuc()" class="btn btn-small bg-grey-dark-one" style="background: #faa31b;">Calculate your tax</button>
                </div>


                <div id="results">
                    <table class="calc-result-table">
                        <tr>
                            <td id="pention-text">Total Payment</td>
                            <td class="sub-result totalpayment"></td>
                        </tr>
                        <tr>
                            <td id="incomeTax-text">VAT</td>
                            <td class="sub-result vat"></td>
                        </tr>
                        <tr id="main-result-row">
                            <td id="main-result-text">Exclusive Payment</td>
                            <td class="main-result payment"></td>
                        </tr>
                    </table>

                    <div><button id="back-to-calc" onclick="back_to_calc()">&lt;&lt;Back to Calculator</button></div>

                </div>

                <div id="results-2">
                    <table class="calc-result-table">

                        <tr>
                            <td id="pention-text">Payment without VAT</td>
                            <td class="sub-result payment"></td>
                        </tr>
                        <tr>
                            <td id="incomeTax-text">VAT</td>
                            <td class="sub-result vat"></td>
                        </tr>

                        <tr id="main-result-row">
                            <td id="main-result-text">Total Payment</td>
                            <td class="main-result totalpayment"></td>
                        </tr>
                    </table>

                    <div><button id="back-to-calc" onclick="back_to_calc()">&lt;&lt;Back to Calculator</button></div>

                </div>
            </div>
            <script type="text/javascript">
                function submitTaxFuc() {
                    (function($) {

                        console.log("clikecled");
                        $("#taxError").hide();
                        $("#results").hide();
                        let payment;
                        let totalPayment;
                        let vat;

                        let vat_rate = .15;
                        if ($("#vat-type").val() == "inclusive") {
                            totalPayment = Number.parseFloat($("#payment").val());
                            if (!totalPayment || totalPayment == 0) {

                                $("#taxError").html("<small>Please Enter a  valid Payment.</small>");
                                $("#taxError").show();
                                return;
                            }


                            payment = totalPayment / (1 + vat_rate);
                            vat = payment * vat_rate;

                            $(".payment").text(payment.toFixed(2) + " ETB");
                            $(".vat").text(vat.toFixed(2) + " ETB");
                            $(".totalPayment").text(totalPayment.toFixed(2) + " ETB");
                            $(".formQuestion").hide();
                            $("#results").show(500);

                        } else {
                            payment = Number.parseFloat($("#payment").val());
                            if (!payment || payment == 0) {

                                $("#taxError").html("<small>Please Enter a  valid Payment.</small>");
                                $("#taxError").show();
                                return;
                            }


                            vat = payment * vat_rate;
                            totalPayment = payment + vat;

                            $(".payment").text(payment.toFixed(2) + " ETB");
                            $(".vat").text(vat.toFixed(2) + " ETB");
                            $(".totalPayment").text(totalPayment.toFixed(2) + " ETB");
                            $(".formQuestion").hide();
                            $("#results").show(500);

                        }



                    })(jQuery)
                }

                function back_to_calc() {
                    (function($) {
                        console.log("cliked1")
                        $("#results").hide();

                        $(".formQuestion").show(500);
                    })(jQuery)
                }
            </script>
<?php
            // Return and clean buffer contents
            return ob_get_clean();
        }

        public function form_handler()
        {
            $post = $_POST;

            // Verify nonce
            if (!isset($post['wp_nonce']) || !wp_verify_nonce($post['wp_nonce'], 'submit_wp_simple_form')) {
                wp_die(__("Cheatin' uh?", 'wp-simple-form'));
            }

            // Verify required fields
            $required_fields = array('name', 'email', 'message');

            foreach ($required_fields as $field) {
                if (empty($post[$field])) {
                    wp_die(__("Name, email and message fields are required.", 'wp-simple-form'));
                }
            }

            // Build post arguments
            $postarr = array(
                'post_author' => 1,
                'post_title' => sanitize_text_field($post['name']),
                'post_content' => sanitize_textarea_field($post['message']),
                'post_type' => 'wpsf_contact_form',
                'post_status' => 'publish',
                'meta_input' => array(
                    'submission_email' => sanitize_email($post['email']),
                    'submission_website' => sanitize_text_field($post['website']),
                )
            );

            // Insert the post
            $postid = wp_insert_post($postarr, true);

            if (is_wp_error($postid)) {
                wp_die(__("There was problem with your submission. Please try again.", 'wp-simple-form'));
            }

            // Send emails to admins
            $to = array();
            $post_edit_url = sprintf('%s?post=%s&action=edit', admin_url('post.php'), $postid);
            $admins = get_users(array('role' => 'administrator'));

            foreach ($admins as $admin) {
                $to[] = $admin->user_email;
            }

            // Build the email
            $subject = __('New feedback!', 'wp-simple-form');
            $message = sprintf('<p>%s</p>', __('Here are the details:', 'wp-simple-form'));
            $message .= sprintf('<p>%s: %s<br>', __('Name', 'wp-simple-form'), sanitize_text_field($post['name']));
            $message .= sprintf('<p>%s: %s<p>', __('Name', 'wp-simple-form'), sanitize_textarea_field($post['message']));
            $message .= sprintf('<p>%s: <a href="%s">%s</a>', __('View/edit the full message here', 'wp-simple-form'), $post_edit_url, $post_edit_url);
            $headers = array('Content-Type: text/html; charset=UTF-8');

            // Send the email
            wp_mail($to, $subject, $message, $headers);

            // Redirect back to page
            wp_redirect(add_query_arg('status', '1', get_permalink($post['redirect_id'])));
        }
    }
}

$wpsimpleform = new WPSimpleForm;

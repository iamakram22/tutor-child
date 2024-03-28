<?php
/**
 * Invoice List Page
 *
 * The list of Invoices for the Portal 
 *
 * @author 		ZeroBSCRM
 * @package 	Templates/Portal/Invoices
 * @see			https://jetpackcrm.com/kb/
 * @version     3.0
 * 
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Don't allow direct access

global $zbs;
$portal = $zbs->modules->portal;

do_action( 'zbs_enqueue_scripts_and_styles' );

$ZBSuseInvoices = zeroBSCRM_getSetting('feat_invs');

if($ZBSuseInvoices < 0){
	status_header( 404 );
	nocache_headers();
	include get_query_template( '404' );
	die();
}


$portalLink = zeroBS_portal_link();
$invoice_endpoint = $portal->get_endpoint( ZBS_TYPE_INVOICE );

?>
<style>
h2.invoice_title {
    font-size: 2rem;
}
.zbs-portal-invoices-list .paid {
    background: green;
    color: white;
    font-weight: 700;
    line-height: 35px;
    border-radius: 0px !important;
}
.zbs-invoice-list thead {
    text-align: left;
	color: var(--tutor-color-primary);
}
.zbs-invoice-list tr {
    border-bottom: 1px solid var(--jp-gray);
}
</style>
<div class="alignwide zbs-site-main">
    <div class='zbs-portal-content zbs-portal-invoices-list'>
		<h2 class="invoice_title">Invoices</h2>
        <div class='zbs-entry-content zbs-responsive-table' style="position:relative;">
		    <?php 
				$invoices_endpoint = new Automattic\JetpackCRM\Invoices_Endpoint( $portal );
				$invoices_endpoint->list_invoices_html_output();
			 ?>
        </div>
    </div>
</div>

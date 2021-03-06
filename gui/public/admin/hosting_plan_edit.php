<?php
/**
 * i-MSCP a internet Multi Server Control Panel
 *
 * The contents of this file are subject to the Mozilla Public License
 * Version 1.1 (the "License"); you may not use this file except in
 * compliance with the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Software distributed under the License is distributed on an "AS IS"
 * basis, WITHOUT WARRANTY OF ANY KIND, either express or implied. See the
 * License for the specific language governing rights and limitations
 * under the License.
 *
 * The Original Code is "VHCS - Virtual Hosting Control System".
 *
 * The Initial Developer of the Original Code is moleSoftware GmbH.
 * Portions created by Initial Developer are Copyright (C) 2001-2006
 * by moleSoftware GmbH. All Rights Reserved.
 *
 * Portions created by the ispCP Team are Copyright (C) 2006-2010 by
 * isp Control Panel. All Rights Reserved.
 *
 * Portions created by the i-MSCP Team are Copyright (C) 2010-2012 by
 * i-MSCP a internet Multi Server Control Panel. All Rights Reserved.
 *
 * @category	i-MSCP
 * @package		iMSCP_Core
 * @subpackage	Admin
 * @copyright	2001-2006 by moleSoftware GmbH
 * @copyright	2006-2010 by ispCP | http://isp-control.net
 * @copyright	2010-2012 by i-MSCP | http://i-mscp.net
 * @author		ispCP Team
 * @author		i-MSCP Team
 * @link		http://i-mscp.net
 */

/*******************************************************************************
 * Script functions
 */

/**
 * Generate load data from sql for requested hosting plan.
 *
 * @param iMSCP_pTemplate $tpl Template engine instance
 * @param int $hostingPlanId Hosting plan unique identifier
 * @return void
 */
function admin_generatePage($tpl, $hostingPlanId)
{
	/** @var $cfg iMSCP_Config_Handler_File */
	$cfg = iMSCP_Registry::get('config');

	if(empty($_POST)) {
		$query = "SELECT * FROM `hosting_plans` WHERE `id` = ?";
		$stmt = exec_query($query, $hostingPlanId);

		if (!$stmt->rowCount()) {
			set_page_message(tr("Hosting plan with Id %d doesn't exist.", $hostingPlanId), 'error');
			redirectTo('hosting_plan.php');
			exit; // Useless but avoid IDE warning about possible undefined variable
		} else {
			$hostingPlanData = $stmt->fetchRow();
		}

		list(
			$php, $cgi, $subdomains, $aliases, $mail, $ftp, $sqlDb, $sqlUser, $traffic, $diskSpace, $backup,
			$customDnsRecords, $softwareInstaller
		) = explode(';', $hostingPlanData['props']);

		$tpl->assign(
			array(
				'HPID' => $hostingPlanId,
				'HP_NAME_VALUE' => tohtml($hostingPlanData['name']),
				'HOSTING_PLAN_ID' => tohtml($hostingPlanId),
				'TR_MAX_SUB_LIMITS' => tohtml($subdomains),
				'TR_MAX_ALS_VALUES' => tohtml($aliases),
				'HP_MAIL_VALUE' => tohtml($mail),
				'HP_FTP_VALUE' => tohtml($ftp),
				'HP_SQL_DB_VALUE' => tohtml($sqlDb),
				'HP_SQL_USER_VALUE' => tohtml($sqlUser),
				'HP_TRAFF_VALUE' => tohtml($traffic),
				'HP_DISK_VALUE' => tohtml($diskSpace),
				'HP_DESCRIPTION_VALUE' => tohtml($hostingPlanData['description']),
				'HP_PRICE' => tohtml($hostingPlanData['price']),
				'HP_SETUPFEE' => tohtml($hostingPlanData['setup_fee']),
				'HP_CURRENCY' => tohtml($hostingPlanData['value']),
				'HP_PAYMENT' => tohtml($hostingPlanData['payment']),
				'HP_TOS_VALUE' => tohtml($hostingPlanData['tos']),
				'TR_PHP_YES' => ($php == '_yes_') ? $cfg->HTML_CHECKED : '',
				'TR_PHP_NO' => ($php == '_no_') ? $cfg->HTML_CHECKED : '',
				'TR_CGI_YES' => ($cgi == '_yes_') ? $cfg->HTML_CHECKED : '',
				'TR_CGI_NO' => ($cgi == '_no_') ? $cfg->HTML_CHECKED : '',
				'VL_BACKUPD' => ($backup == '_dmn_') ? $cfg->HTML_CHECKED : '',
				'VL_BACKUPS' => ($backup == '_sql_') ? $cfg->HTML_CHECKED : '',
				'VL_BACKUPF' => ($backup == '_full_') ? $cfg->HTML_CHECKED : '',
				'VL_BACKUPN' => ($backup == '_no_') ? $cfg->HTML_CHECKED : '',
				'TR_DNS_YES' => ($customDnsRecords == '_yes_') ? $cfg->HTML_CHECKED : '',
				'TR_DNS_NO' => ($customDnsRecords == '_no_') ? $cfg->HTML_CHECKED : '',
				'TR_SOFTWARE_YES' => ($softwareInstaller == '_yes_') ? $cfg->HTML_CHECKED : '',
				'TR_SOFTWARE_NO' => ($softwareInstaller == '_no_' || !$softwareInstaller) ? $cfg->HTML_CHECKED : '',
				'TR_STATUS_YES' => ($hostingPlanData['status']) ? $cfg->HTML_CHECKED : '',
				'TR_STATUS_NO' => (!$hostingPlanData['status']) ? $cfg->HTML_CHECKED : ''));
	} else { // Restore form on error
		$tpl->assign(
			array(
				'HPID' => $hostingPlanId,
				'HP_NAME_VALUE' => clean_input($_POST['hp_name'], true),
				'HP_DESCRIPTION_VALUE' => clean_input($_POST['hp_description'], true),
				'TR_MAX_SUB_LIMITS' => clean_input($_POST['hp_sub'], true),
				'TR_MAX_ALS_VALUES' => clean_input($_POST['hp_als'], true),
				'HP_MAIL_VALUE' => clean_input($_POST['hp_mail'], true),
				'HP_FTP_VALUE' => clean_input($_POST['hp_ftp'], true),
				'HP_SQL_DB_VALUE' => clean_input($_POST['hp_sql_db'], true),
				'HP_SQL_USER_VALUE' => clean_input($_POST['hp_sql_user'], true),
				'HP_TRAFF_VALUE' => clean_input($_POST['hp_traff'], true),
				'HP_TRAFF' => clean_input($_POST['hp_traff'], true),
				'HP_DISK_VALUE' => clean_input($_POST['hp_disk'], true),
				'HP_PRICE' => clean_input($_POST['hp_price'], true),
				'HP_SETUPFEE' => clean_input($_POST['hp_setupfee'], true),
				'HP_CURRENCY' => clean_input($_POST['hp_currency'], true),
				'HP_PAYMENT' => clean_input($_POST['hp_payment'], true),
				'HP_TOS_VALUE' => clean_input($_POST['hp_tos'], true),
				'TR_PHP_YES' => ($_POST['php'] == '_yes_') ? $cfg->HTML_CHECKED : '',
				'TR_PHP_NO' => ($_POST['php'] == '_no_') ? $cfg->HTML_CHECKED : '',
				'TR_CGI_YES' => ($_POST['cgi'] == '_yes_') ? $cfg->HTML_CHECKED : '',
				'TR_CGI_NO' => ($_POST['cgi'] == '_no_') ? $cfg->HTML_CHECKED : '',
				'VL_BACKUPD' => ($_POST['backup'] == '_dmn_') ? $cfg->HTML_CHECKED : '',
				'VL_BACKUPS' => ($_POST['backup'] == '_sql_') ? $cfg->HTML_CHECKED : '',
				'VL_BACKUPF' => ($_POST['backup'] == '_full_') ? $cfg->HTML_CHECKED : '',
				'VL_BACKUPN' => ($_POST['backup'] == '_no_') ? $cfg->HTML_CHECKED : '',
				'TR_DNS_YES' => ($_POST['dns'] == '_yes_') ? $cfg->HTML_CHECKED : '',
				'TR_DNS_NO' => ($_POST['dns'] == '_no_') ? $cfg->HTML_CHECKED : '',
				'TR_SOFTWARE_YES' => ($_POST['software_allowed'] == '_yes_') ? $cfg->HTML_CHECKED : '',
				'TR_SOFTWARE_NO' => ($_POST['software_allowed'] == '_no_') ? $cfg->HTML_CHECKED : '',
				'TR_STATUS_YES' => ($_POST['status']) ? $cfg->HTML_CHECKED : '',
				'TR_STATUS_NO' => (!$_POST['status']) ? $cfg->HTML_CHECKED : ''));
	}
}

/**
 * Check hosting plan data.
 *
 * @return bool TRUE if hosting plan data are valid, FALSE otherwise
 */
function admin_checkHostingPlanData()
{
	global $name, $php, $cgi, $subdomains, $aliases, $mail, $ftp, $sqlDb, $sqlUser, $traffic, $diskSpace, $backup,
		   $customDns, $softwareInstaller;

	$name = clean_input($_POST['hp_name']);
	$subdomains = clean_input($_POST['hp_sub']);
	$aliases = clean_input($_POST['hp_als']);
	$mail = clean_input($_POST['hp_mail']);
	$ftp = clean_input($_POST['hp_ftp']);
	$sqlDb = clean_input($_POST['hp_sql_db']);
	$sqlUser = clean_input($_POST['hp_sql_user']);
	$traffic = clean_input($_POST['hp_traff']);
	$diskSpace = clean_input($_POST['hp_disk']);

	if (isset($_POST['php'])) {
		$php = $_POST['php'];
	}

	if (isset($_POST['cgi'])) {
		$cgi = $_POST['cgi'];
	}

	if (isset($_POST['backup'])) {
		$backup = $_POST['backup'];
	}

	if (isset($_POST['dns'])) {
		$customDns = $_POST['dns'];
	}

	if (isset($_POST['software_allowed'])) {
		$softwareInstaller = $_POST['software_allowed'];
	} else {
		$softwareInstaller = '_no_';
	}

	if ($php == '_no_' && $softwareInstaller == '_yes_') {
		set_page_message(tr('Software installer require PHP feature.'), 'error');
	}

	if (!is_numeric($_POST['hp_price'])) {
		set_page_message(tr('Incorrect price. Example: 9.99'), 'error');
	}

	if (!is_numeric($_POST['hp_setupfee'])) {
		set_page_message(tr('Incorrect setup fee. Example: 19.99'), 'error');
	}

	if (!imscp_limit_check($subdomains, -1)) {
		set_page_message(tr('Incorrect subdomains limit.'), 'error');
	}

	if (!imscp_limit_check($aliases, -1)) {
		set_page_message(tr('Incorrect aliases limit.'), 'error');
	}

	if (!imscp_limit_check($mail, -1)) {
		set_page_message(tr('Incorrect mail accounts limit.'), 'error');
	}

	if (!imscp_limit_check($ftp, -1)) {
		set_page_message(tr('Incorrect Ftp accounts limit.'), 'error');
	}

	if (!imscp_limit_check($sqlUser, -1)) {
		set_page_message(tr('Incorrect Sql databases limit.'), 'error');
	}

	if (!imscp_limit_check($sqlDb, -1)) {
		set_page_message(tr('Incorrect Sql users limit.'), 'error');
	}

	if (!imscp_limit_check($traffic, null)) {
		set_page_message(tr('Incorrect traffic limit.'), 'error');
	}

	if (!imscp_limit_check($diskSpace, null)) {
		set_page_message(tr('Incorrect disk space limit.'), 'error');
	}

	if (Zend_Session::namespaceIsset('pageMessages')) {
		return false;
	}

	return true;
}

/**
 * Update hosting plan.
 *
 * @param int $hostingPlanId Hosting plan unique identifier
 * @return void
 */
function admin_updateHostingPlan($hostingPlanId)
{
	global $name, $php, $cgi, $subdomains, $aliases, $mail, $ftp, $sqlDb, $sqlUser, $traffic, $diskSpace, $backup,
		   $customDns, $softwareInstaller;

	$description = clean_input($_POST['hp_description']);
	$price = clean_input($_POST['hp_price']);
	$setup_fee = clean_input($_POST['hp_setupfee']);
	$value = clean_input($_POST['hp_currency']);
	$payment = clean_input($_POST['hp_payment']);
	$status = clean_input($_POST['status']);
	$tos = clean_input($_POST['hp_tos']);

	$hp_props = "$php;$cgi;$subdomains;$aliases;$mail;$ftp;$sqlDb;$sqlUser;$traffic;$diskSpace;$backup;";
	$hp_props .= "$customDns;$softwareInstaller";

	$query = "
		UPDATE
			`hosting_plans`
		SET
			`name` = ?, `description` = ?, `props` = ?, `price` = ?, `setup_fee` = ?, `value` = ?, `payment` = ?,
			`status` = ?, `tos` = ?
		WHERE
			`id` = ?
	";
	exec_query($query, array($name, $description, $hp_props, $price, $setup_fee, $value, $payment, $status, $tos, $hostingPlanId));
}

/*******************************************************************************
 * Main script
 */

// Include core library
require 'imscp-lib.php';

iMSCP_Events_Manager::getInstance()->dispatch(iMSCP_Events::onAdminScriptStart);

check_login(__FILE__);

/** @var $cfg iMSCP_Config_Handler_File */
$cfg = iMSCP_Registry::get('config');

if (strtolower($cfg->HOSTING_PLANS_LEVEL) != 'admin') {
	redirectTo('index.php');
}

// Dispatches request
if(isset($_GET['hpid'])) {
	if (!empty($_POST) && admin_checkHostingPlanData()) {
		admin_updateHostingPlan(intval($_GET['hpid']));
		set_page_message(tr('Hosting plan successfully updated.'), 'success');
		redirectTo('hosting_plan.php');
	}

	$tpl = new iMSCP_pTemplate();
	$tpl->define_dynamic(
		array(
			'layout' => 'shared/layouts/ui.tpl',
			'page' => 'admin/hosting_plan_edit.tpl',
			'page_message' => 'layout'));

	admin_generatePage($tpl, intval($_GET['hpid']));
} else {
	set_page_message(tr('Wrong_request.'), 'error');
	redirectTo('hosting_plan.php');
}

generateNavigation($tpl);

$tpl->assign(
	array(
		'TR_PAGE_TITLE' => tr('i-MSCP - Admin / Edit hosting plan'),
		'THEME_CHARSET' => tr('encoding'),
		'ISP_LOGO' => layout_getUserLogo(),
		'TR_HOSTING_PLAN_PROPS' => tr('Hosting plan properties'),
		'TR_TEMPLATE_NAME' => tr('Template name'),
		'TR_MAX_SUBDOMAINS' => tr('Max subdomains<br><i>(-1 disabled, 0 unlimited)</i>'),
		'TR_MAX_ALIASES' => tr('Max aliases<br><i>(-1 disabled, 0 unlimited)</i>'),
		'TR_MAX_MAILACCOUNTS' => tr('Mail accounts limit<br><i>(-1 disabled, 0 unlimited)</i>'),
		'TR_MAX_FTP' => tr('FTP accounts limit<br><i>(-1 disabled, 0 unlimited)</i>'),
		'TR_MAX_SQL' => tr('SQL databases limit<br><i>(-1 disabled, 0 unlimited)</i>'),
		'TR_MAX_SQL_USERS' => tr('SQL users limit<br><i>(-1 disabled, 0 unlimited)</i>'),
		'TR_MAX_TRAFFIC' => tr('Traffic limit [MiB]<br><i>(0 unlimited)</i>'),
		'TR_DISK_LIMIT' => tr('Disk limit [MiB]<br><i>(0 unlimited)</i>'),
		'TR_PHP' => tr('PHP'),
		'TR_SOFTWARE_SUPP' => tr('Software installer'),
		'TR_CGI' => tr('CGI'),
		'TR_DNS' => tr('Custom DNS records'),
		'TR_BACKUP' => tr('Backup'),
		'TR_BACKUP_DOMAIN' => tr('Domain'),
		'TR_BACKUP_SQL' => tr('Sql'),
		'TR_BACKUP_FULL' => tr('Full'),
		'TR_BACKUP_NO' => tr('No'),
		'TR_APACHE_LOGS' => tr('Apache logfiles'),
		'TR_AWSTATS' => tr('AwStats'),
		'TR_YES' => tr('yes'),
		'TR_NO' => tr('no'),
		'TR_BILLING_PROPS' => tr('Billing Settings'),
		'TR_PRICE_STYLE' => tr('Price style'),
		'TR_PRICE' => tr('Price'),
		'TR_SETUP_FEE' => tr('Setup fee'),
		'TR_VALUE' => tr('Currency'),
		'TR_PAYMENT' => tr('Payment period'),
		'TR_STATUS' => tr('Available for purchasing'),
		'TR_TEMPLATE_DESCRIPTON' => tr('Description'),
		'TR_EXAMPLE' => tr('(e.g. EUR)'),
		'TR_TOS_PROPS' => tr('Term of service'),
		'TR_TOS_NOTE' => tr('Leave this field empty if you do not want term of service for this hosting plan.'),
		'TR_TOS_DESCRIPTION' => tr('Text'),
		'TR_UPDATE' => tr('Update')));

generatePageMessage($tpl);

$tpl->parse('LAYOUT_CONTENT', 'page');

iMSCP_Events_Manager::getInstance()->dispatch(iMSCP_Events::onAdminScriptEnd, array('templateEngine' => $tpl));

$tpl->prnt();

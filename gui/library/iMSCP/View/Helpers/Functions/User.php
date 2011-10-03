<?php
/**
 * i-MSCP a internet Multi Server Control Panel
 *
 * @copyright   2001-2006 by moleSoftware GmbH
 * @copyright   2006-2010 by ispCP | http://isp-control.net
 * @copyright   2010-2011 by i-MSCP | http://i-mscp.net
 * @version     SVN: $Id$
 * @link        http://i-mscp.net
 * @author      ispCP Team
 * @author      i-MSCP Team
 *
 * @license
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
 *
 * Portions created by Initial Developer are Copyright (C) 2001-2006
 * by moleSoftware GmbH. All Rights Reserved.
 *
 * Portions created by the ispCP Team are Copyright (C) 2006-2010 by
 * isp Control Panel. All Rights Reserved.
 *
 * Portions created by the i-MSCP Team are Copyright (C) 2010-2011 by
 * i-MSCP a internet Multi Server Control Panel. All Rights Reserved.
 */

/************************************************************************************
 * This file contains view helpers functions that are responsible to generate
 * template parts for reseller interface such as the main and left menus.
 */

/**
 * Helper function to generate main menu from partial template file.
 *
 * @param  iMSCP_pTemplate $tpl Template engine
 * @param  $menuTemplateFile Partial template file path
 * @return void
 */
function gen_client_mainmenu($tpl, $menuTemplateFile)
{
    /** @var $cfg iMSCP_Config_Handler_File */
    $cfg = iMSCP_Registry::get('config');

	$tpl->define_dynamic(array(
							 'menu' => $menuTemplateFile,
							 'http_feature' => 'menu',
							 'ftp_feature' => 'menu',
							 'sql_feature' => 'menu',
							 'mail_feature' => 'menu',
							 'support' => 'menu',
							 'custom_buttons_feature' => 'menu'));

	$tpl->assign(array(
					 'TR_MENU_GENERAL_INFORMATION' => tr('General Information'),
					 'TR_MENU_STATISTICS' => tr('Statistics'),
					 'TR_MENU_WEBTOOLS' => tr('Webtools'),

					 'TR_MENU_LOGOUT' => tr('Logout')));

	// Per feature menu -- begin

	// Getting domain properties
	$domainProperties = get_domain_default_props($_SESSION['user_id'], true);

	// domain_feature feature is available?
	if ($domainProperties['domain_alias_limit'] != '-1'
		|| $domainProperties['domain_subd_limit'] == '-1'
		|| $domainProperties['domain_dns'] != 'yes'
	) {
        $tpl->assign('TR_MENU_MANAGE_DOMAINS', tr('Manage Domains'));
	} else {
		$tpl->assign('DOMAIN_FEATURE', '');
	}

	// ftp_feature feature is available?
	if ($domainProperties['domain_alias_limit'] != '-1') {
		$tpl->assign('TR_MENU_FTP_ACCOUNTS', tr('Ftp Accounts'));
	} else {
		$tpl->assign('FTP_FEATURE', '');
	}

	// sql_feature feature is available?
	if ($domainProperties['domain_sqld_limit'] != '-1') {
		$tpl->assign('TR_MENU_MANAGE_SQL', tr('Manage SQL'));
	} else {
		$tpl->assign('SQL_FEATURE', '');
	}

	// mail_feature feature is available?
	if ($domainProperties['domain_sqld_limit'] != '-1') {
		$tpl->assign('TR_MENU_MAIL_ACCOUNTS', 'Mail Accounts');
	} else {
		$tpl->assign('MAIL_FEATURE', '');
	}

	// Todo: must be review for external support system
    $query = "SELECT `support_system` FROM `reseller_props` WHERE `reseller_id` = ?";
    $stmt = exec_query($query, $_SESSION['user_created_by']);

    if (!$cfg->IMSCP_SUPPORT_SYSTEM || $stmt->fields['support_system'] != 'no') {
		$tpl->assign('TR_MENU_SUPPORT',  tr('Support'));
    } else {
		$tpl->assign('SUPPORT_FEATURE', '');
	}

	// Per feature menu -- end

	// Custom menus feature - begin

    $query = "SELECT * FROM `custom_menus` WHERE `menu_level` = ? OR `menu_level` = ?";
    $stmt = exec_query($query, array('user', 'all'));

    if ($stmt->rowCount() == 0) {
        $tpl->assign('CUSTOM_BUTTONS_FEATURE', '');
    } else {
        while (!$stmt->EOF) {
            $customMenuTarget = $stmt->fields['menu_target'];

            if ($customMenuTarget !== '') {
                $customMenuTarget = 'target="' . tohtml($customMenuTarget) . '"';
            }

            $tpl->assign(array(
                              'BUTTON_LINK' => tohtml($stmt->fields['menu_link']),
                              'BUTTON_NAME' => tohtml($stmt->fields['menu_name']),
                              'BUTTON_TARGET' => $customMenuTarget));

            $tpl->parse('CUSTOM_BUTTONS_FEATURE', '.custom_buttons_feature');
            $stmt->moveNext();
        }
    }
	// Custom menus feature - end

    $tpl->parse('MAIN_MENU', 'menu');
}

/**
 * Helper function to generate client left menu from partial template file.
 *
 * @param  iMSCP_pTemplate $tpl Template engine
 * @param  $menuTemplateFile menu partial template file
 * @return void
 */
function gen_client_menu($tpl, $menuTemplateFile)
{
    /** @var $cfg iMSCP_Config_Handler_File */
    $cfg = iMSCP_Registry::get('config');

	$tpl->define_dynamic(array(
							  'menu' => $menuTemplateFile,
							  'subdomains_feature' => 'menu',
							  'domain_aliases_features' => 'menu',
							  'ftp_feature' => 'menu',
							  'mail_feature' => 'menu',
							  'sql_feature' => 'menu',
							  'php_directives_editor_feature' => 'menu',
							  'awstats_feature' => 'menu',
							  'protected_areas_feature' => 'menu',
							  'aps_feature' => 'menu',
							  'support_system_feature' => 'menu',
							  'backup_feature' => 'menu',
							  'custom_dns_records_feature' => 'menu',
							  'update_hosting_plan_feature' => 'menu'));

	$tpl->assign(array(
					  'TR_LMENU_OVERVIEW' => tr('Overview'),
					  'TR_LMENU_CHANGE_PASSWORD' => tr('Change password'),
					  'TR_LMENU_CHANGE_PERSONAL_DATA' => tr('Personal data'),
					  'TR_LMENU_LANGUAGE' => tr('Change language'),
					  'TR_LMENU_UPDATE_HOSTING_PLAN' => tr('Update hosting plan'),

					  // Todo move these entries tha don't really belong to the menu
					  'VERSION' => $cfg->Version,
					  'BUILDDATE' => $cfg->BuildDate,
					  'CODENAME' => $cfg->CodeName
				 ));

	// Per feature left menu -- begin

	// Getting domain properties
	$domainProperties = get_domain_default_props($_SESSION['user_id'], true);

	// Subdomains feature is available?
	if($domainProperties['domain_subd_limit'] != '-1') {
		$tpl->assign('TR_LMENU_ADD_SUBDOMAIN', tr('Add subdomain'));
	} else {
		$tpl->assign('SUBDOMAINS_FEATURE', '');
	}

	// Domain aliases feature is available?
	if($domainProperties['domain_alias_limit'] != '-1') {
		$tpl->assign('TR_LMENU_ADD_DOMAIN_ALIAS', tr('Add domain alias'));
	} else {
		$tpl->assign('DOMAIN_ALIASES_FEATURE', '');
	}

	// Ftp feature is available?
	if($domainProperties['domain_ftpacc_limit'] != '-1') {
		$tpl->assign(array(
						  'TR_LMENU_ADD_FTP_USER' => tr('Add FTP user'),
						  'TR_LMENU_FILEMANAGER' => tr('Filemanager'),
						  'TR_LMENU_FTP_ACCOUNTS' => tr('FTP Accounts'),
						  'FILEMANAGER_PATH' => $cfg->FILEMANAGER_PATH,
						  'FILEMANAGER_TARGET' => $cfg->FILEMANAGER_TARGET));
	} else {
		$tpl->assign('FTP_FEATURE', '');
	}

	// Mail feature is available?
	if($domainProperties['domain_mailacc_limit'] != '-1') {
		$tpl->assign(array(
						 'TR_LMENU_EMAIL_ACCOUNTS' => tr('Email Accounts'),
						 'TR_LMENU_ADD_MAIL_USER' => tr('Add mail user'),
						 'TR_LMENU_MAIL_CATCH_ALL' => tr('Catch all'),
						 'TR_LMENU_WEBMAIL' => tr('Webmail'),
						 'WEBMAIL_PATH' => $cfg->WEBMAIL_PATH,
						 'WEBMAIL_TARGET' => $cfg->WEBMAIL_TARGET));
	} else {
		$tpl->assign('MAIL_FEATURE', '');
	}

	// SQL feature is available?
	if($domainProperties['domain_sqld_limit'] = '-1'
	   && $domainProperties['domain_sqlu_limit'] != '-1'
	){
		$tpl->assign(array(
						 'TR_LMENU_ADD_SQL_DATABASE' => tr('Add SQL database'),
						 'TR_LMENU_PMA' => tr('PhpMyAdmin'),
                         'PMA_PATH' => $cfg->PMA_PATH,
                         'PMA_TARGET' => $cfg->PMA_TARGET));
	} else {
		$tpl->assign('SQL_FEATURE', '');
	}

	// Custom DNS records feature is available?
	if($domainProperties['domain_dns'] != 'no') {
		$tpl->assign('TR_LMENU_ADD_CUSTOM_DNS_RECORD', tr('Add custom DNS record'));
	} else {
		$tpl->assign('CUSTOM_DNS_RECORDS_FEATURE', '');
	}

	// PHP directives editor feature is available?
	if($domainProperties['phpini_perm_system'] != 'no') {
		$tpl->assign('TR_LMENU_PHP_DIRECTIVES_EDITOR', tr('PHP directives editor'));
	} else {
		$tpl->assign('PHP_DIRECTIVES_EDITOR_FEATURE', '');
	}

	// Awstats feature is available
	if($cfg->AWSTATS_ACTIVE != 'no') {
		$tpl->assign(array(
						  'TR_LMENU_AWSTATS' => tr('Web statistics'),
						  'AWSTATS_PATH' => 'http://' . $domainProperties['domain_name'] . $cfg->AWSTATS_PATH,
						  'AWSTATS_TARGET' => $cfg->AWSTATS_TARGET));
	} else {
		$tpl->assign('AWSTATS_FEATURE', '');
	}

	// Daily backup feature is available?
	if($cfg->BACKUP_DOMAINS != 'no' && $domainProperties['allowbackup'] != 'no'){
		$tpl->assign('TR_LMENU_DAILY_BACKUP', tr('Daily backup'));
	} else {
		$tpl->assign('BACKUP_FEATURE', '');
	}

	// Protected areas feature is available? (Always yes for now)
	// TODO add on|off option for protected areas
	$tpl->assign('TR_LMENU_HTACCESS', tr('Protected areas'));

	// Custom error pages feature is available? (Always yes for now)
	// TODO add on|off option for custom error pages feature
	$tpl->assign('TR_LMENU_CUSTOM_ERROR_PAGES', tr('Custom error pages'));

	// Application Software Package feature is available?
	if($domainProperties['domain_software_allowed'] != 'no') {
		$tpl->assign('TR_LMENU_APS', tr('Application installer'));
	} else {
		$tpl->assign('APS_FEATURE', '');
	}

	// Support system feature is available?
	// Todo: must be review for external support system
    $query = "SELECT `support_system` FROM `reseller_props` WHERE `reseller_id` = ?";
    $stmt = exec_query($query, $_SESSION['user_created_by']);

    if (!$cfg->IMSCP_SUPPORT_SYSTEM || $stmt->fields['support_system'] != 'no') {
		$tpl->assign(array(
						  'TR_LMENU_OPEN_TICKETS' => tr('Open tickets'),
						  'TR_LMENU_CLOSED_TICKETS' => tr('Closed tickets'),
						  'TR_LMENU_NEW_TICKET' => tr('New ticket'),
						  'SUPPORT_SYSTEM_PATH' => $cfg->IMSCP_SUPPORT_SYSTEM_PATH,
						  'SUPPORT_SYSTEM_TARGET' => $cfg->IMSCP_SUPPORT_SYSTEM_TARGET));
    } else {
		$tpl->assign('SUPPORT_SYSTEM_FEATURE', '');
	}

	// Update hosting plan is available?
	// Yes if hosting plan are managed by reseller and a least one hosting plan is
	// available for update
	if($cfg->HOSTING_PLANS_LEVEL != 'admin') {
		$query = "
			SELECT
				COUNT(`id`)`cnt`
			FROM
				`hosting_plans`
			WHERE
				`reseller_id` = ?
			AND
				`status` = '1'
		";
		$stmt = exec_query($query, $_SESSION['user_created_by']);

		if($stmt->fields['cnt'] > 0) {
			$tpl->assign('TR_LMENU_UPDATE_HOSTING_PLAN', tr('Update hosting plan'));
		} else {
			$tpl->assign('UPDATE_HOSTING_PLAN_FEATURE', '');
		}
	} else {
		$tpl->assign('UPDATE_HOSTING_PLAN_FEATURE', '');
	}

	// Per feature left menu -- End

	// Custom menus -- begin

	// Custom menus -- end

	$tpl->parse('MENU', 'menu');

	return;

/*
    $sub_cnt = get_domain_running_sub_cnt($dmn_id);

    if ($dmn_subd_limit != 0 && $sub_cnt >= $dmn_subd_limit) {
        $tpl->assign('ISACTIVE_SUBDOMAIN_MENU', '');
    }

    $als_cnt = get_domain_running_als_cnt($dmn_id);

    if ($dmn_als_limit != 0 && $als_cnt >= $dmn_als_limit) {
        $tpl->assign('ISACTIVE_ALIAS_MENU', '');
    }

    if ($cfg->AWSTATS_ACTIVE != 'yes') {
        $tpl->assign('ACTIVE_AWSTATS', '');
    } else {
        $tpl->assign(array(
                          'AWSTATS_PATH' => 'http://' . $_SESSION['user_logged'] . '/stats/',
                          'AWSTATS_TARGET' => $cfg->AWSTATS_TARGET
					 ));
    }

    // Hide 'Update Hosting Package'-Button, if there are none
    $query = "
		SELECT
			`id`
		FROM
			`hosting_plans`
		WHERE
			`reseller_id` = ?
		AND
			`status` = '1'
	";
    $stmt = exec_query($query, $_SESSION['user_created_by']);

    if (!$stmt->recordCount() ) {
        if ($cfg->HOSTING_PLANS_LEVEL != 'admin') {
            $tpl->assign('ISACTIVE_UPDATE_HP', '');
        }
    }

    $query = "
		SELECT
			`domain_software_allowed`, `domain_ftpacc_limit`
		FROM
			`domain`
		WHERE
			`domain_admin_id` = ?
	";
    $stmt = exec_query($query, $_SESSION['user_id']);

    if ($stmt->fields('domain_software_allowed') == 'yes'
        && $stmt->fields('domain_ftpacc_limit') != '-1'
    ) {
        $tpl->assign(array('SOFTWARE_MENU' => tr('yes')));
        $tpl->parse('T_SOFTWARE_MENU', '.t_software_menu');
    } else {
        $tpl->assign('T_SOFTWARE_MENU', '');
    }

    $tpl->parse('MENU', 'menu');
	*/
}
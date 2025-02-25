<?php
if( ! defined('IN_MANAGER_MODE') || IN_MANAGER_MODE !== true) {
    die("<b>INCLUDE_ORDERING_ERROR</b><br /><br />Please use the EVO Content Manager instead of accessing this file directly.");
}

if(!EvolutionCMS()->hasPermission('category_manager')) {
	EvolutionCMS()->webAlertAndQuit($_lang["error_no_privileges"]);
}

$_module_params = array(
	'module_version'   => '1.0.0',
	'module_params'    => '',
	'module_id'        => get_by_key($_GET, 'id', 0),
	'package_name'     => 'Module_Categories_Manager',
	'native_language'  => 'de',
	'name'             => $_lang['manage_categories'],
	'dirname'          => MODX_MANAGER_URL,
	'url'              => 'index.php?a=120&amp;id=' . get_by_key($_GET, 'id', 0),
	'path'             => realpath( __DIR__ ) . DIRECTORY_SEPARATOR . 'category_mgr' . DIRECTORY_SEPARATOR,
	'inc_dir'          => realpath( __DIR__ ) . DIRECTORY_SEPARATOR . 'category_mgr' . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR,
	'languages_dir'    => realpath( __DIR__ ) . DIRECTORY_SEPARATOR . 'category_mgr' . DIRECTORY_SEPARATOR . 'lang' . DIRECTORY_SEPARATOR,
	'views_dir'        => realpath( __DIR__ ) . DIRECTORY_SEPARATOR . 'category_mgr' . DIRECTORY_SEPARATOR . 'skin' . DIRECTORY_SEPARATOR,
	'request_key'      => 'module_categories_manager',
	'messages'         => array()
);

$cm = new EvolutionCMS\Legacy\ModuleCategoriesManager();

// assign module_params to internal params
foreach( $_module_params as $param => $value )
{
	$cm->set( $param, $value );
}

// catch the request actions
include_once $cm->get('inc_dir') . 'request_trigger.inc.php';

if( !$categories = $cm->getCategories() )
{
	setcookie('webfxtab_manage-categories-pane', 0 );
	$cm->addMessage( $cm->txt('Currently no categories available... JUST ADD A NEW ONE!'), 'global' );
}

$cm->renderView('main', $categories );
return;

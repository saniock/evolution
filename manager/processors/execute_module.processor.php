<?php

use EvolutionCMS\Models\SiteModule;

if (!defined('IN_MANAGER_MODE') || IN_MANAGER_MODE !== true) {
    die("<b>INCLUDE_ORDERING_ERROR</b><br /><br />Please use the EVO Content Manager instead of accessing this file directly.");
}
if (!EvolutionCMS()->hasPermission('exec_module')) {
    EvolutionCMS()->webAlertAndQuit($_lang["error_no_privileges"]);
}
if (isset($_GET['id'])) {
    if (is_numeric($_GET['id'])) {
        $id = (int)$_GET['id'];
    } else {
        $id = $_GET['id'];
    }
} else {
    EvolutionCMS()->webAlertAndQuit($_lang["error_no_id"]);
}
// check if user has access permission, except admins
if ($_SESSION['mgrRole'] != 1 && is_numeric($id)) {
    $moduleAccess = SiteModule::query()
        ->withoutProtected()
        ->where('site_modules.id', $id)
        ->first();

    if (empty($moduleAccess)) {
        EvolutionCMS()->webAlertAndQuit("You do not sufficient privileges to execute this module.", "index.php?a=106");
    }
}
if (is_numeric($id)) {
    // get module data
    $content = SiteModule::find($id);
    if (is_null($content)) {
        EvolutionCMS()->webAlertAndQuit("No record found for id {$id}.", "index.php?a=106");
    }
    $content = $content->toArray();
    if ($content['disabled']) {
        EvolutionCMS()->webAlertAndQuit("This module is disabled and cannot be executed.", "index.php?a=106");
    }
} else {
    $content = EvolutionCMS()->modulesFromFile[$id];
    $content['modulecode'] = file_get_contents($content['file']);
    $content["guid"] = '';
}
// Set the item name for logger
$_SESSION['itemname'] = $content['name'];

// load module configuration
$parameter = EvolutionCMS()->parseProperties($content["properties"], $content["guid"], 'module');

// Set the item name for logger
$_SESSION['itemname'] = $content['name'];

if (substr($content["modulecode"], 0, 5) === '<?php') {
    $content["modulecode"] = substr($content["modulecode"], 5);
}
echo evalModule($content["modulecode"], $parameter);
include MODX_MANAGER_PATH . "includes/sysalert.display.inc.php";

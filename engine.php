<?php

session_start();

error_reporting(E_ALL);

define("BUSINESS_MANAGER", false);

require_once('settings/settings.php');  # carga configuraciones
require_once('scripts/encryption.php'); # funciones de proteccion de datos

import('core.bm_engine');

date_default_timezone_set(TIMEZONE);

import('core.engine.database');
import('core.engine.template');
import('core.engine.page');
import('core.engine.frontController');
import('core.handler.http');
import('core.handler.controller');
import('core.handler.object');
import('core.handler.sessionHandler');
import('core.handler.MysqliHandler');
import('core.handler.debugHandler');
import('core.orm.helper');

import('scripts.loghandler');
import('scripts.pdf.fpdf');
import('scripts.pdf.report');
import('scripts.alias');
import('scripts.init_setup');

require_once "../UI-Core/plugins/sigma/demos/export_php/GridServerHandler.php";
require_once "../UI-Core/plugins/sigma/php/ConnectionManager.php";
require_once "../UI-Core/plugins/sigma/demos/export_php/JSON.php";
require_once "../UI-Core/plugins/sigma/demos/export_php/html2pdf/convert.php";
require_once "../UI-Core/plugins/sigma/demos/export_php/html2pdf/_class/exception.php";
require_once "../UI-Core/plugins/sigma/demos/export_php/html2pdf/_class/locale.php";
require_once "../UI-Core/plugins/sigma/demos/export_php/html2pdf/_class/myPdf.php";
require_once "../UI-Core/plugins/sigma/demos/export_php/html2pdf/_class/parsingHtml.php";
require_once "../UI-Core/plugins/sigma/demos/export_php/html2pdf/_class/parsingCss.php";

import('mdl.error');

#####	configuraciones iniciales	#####

$plugins = array(
    "metro-global",
    "metro-core",
    "metro-locale",
    "metro-initiator",
    "metro-accordion",
    "metro-button-set",
    "metro-carousel",
    "metro-countdown",
    "metro-date-format",
    "metro-dialog",
    "metro-drag-tile",
    "metro-dropdown",
    "metro-fluentmenu",
    "metro-hint",
    "metro-input-control",
    "metro-listview",
    "metro-live-tile",
    "metro-loader",
    "metro-notify",
    "metro-panel",
    "metro-plugin-template",
    "metro-popover",
    "metro-progressbar",
    "metro-pull",
    "metro-rating",
    "metro-scroll",
    "metro-slider",
    "metro-stepper",
    "metro-streamer",
    "metro-tab-control",
    "metro-table",
    "metro-tile-transform",
    "metro-times",
    "metro-touch-handler",
    "metro-treeview",
    "metro-wizard",
    "metro-calendar",
    "metro-datepicker"
);

BM::singleton()->storeObject('template', 'temp');
BM::singleton()->storeObject('database', 'db');
BM::singleton()->storeSetting('default', 'skin');

BM::singleton()->getObject('temp')->getPage()->setJs('../UI-Core/js/jquery.min.js');
BM::singleton()->getObject('temp')->getPage()->setJs('../UI-Core/js/jquery-ui.min.js');
BM::singleton()->getObject('temp')->getPage()->setJs('../UI-Core/js/angular.min.js');

BM::singleton()->getObject('temp')->getPage()->setJs('static/js/init.js');
BM::singleton()->getObject('temp')->getPage()->setJs('static/js/inventario.js');

BM::singleton()->getObject('temp')->getPage()->setCss('../UI-Core/plugins/sigma/grid/gt_grid.css');
BM::singleton()->getObject('temp')->getPage()->setCss('../UI-Core/plugins/sigma/grid/skin/mac/skinstyle.css');
BM::singleton()->getObject('temp')->getPage()->setCss('../UI-Core/plugins/sigma/grid/skin/vista/skinstyle.css');
BM::singleton()->getObject('temp')->getPage()->setCss('../UI-Core/css/metro-bootstrap-responsive.css');
BM::singleton()->getObject('temp')->getPage()->setCss('../UI-Core/css/metro-bootstrap.css');
BM::singleton()->getObject('temp')->getPage()->setCss('../UI-Core/css/iconFont.min.css');

BM::singleton()->getObject('temp')->getPage()->setJs('../UI-Core/js/business.manager.1.0.js');

foreach ($plugins as $plugin) {
    BM::singleton()->getObject('temp')->getPage()->setJs('../UI-Core/js/metro/' . $plugin . '.js');
}

BM::singleton()->getObject('temp')->getPage()->setJs('../UI-Core/plugins/sigma/grid/calendar/calendar.js');
BM::singleton()->getObject('temp')->getPage()->setJs('../UI-Core/plugins/sigma/grid/calendar/calendar-setup.js');
BM::singleton()->getObject('temp')->getPage()->setJs('../UI-Core/plugins/sigma/grid/gt_grid_all.js');
BM::singleton()->getObject('temp')->getPage()->setJs('../UI-Core/plugins/sigma/grid/gt_msg_en.js');
BM::singleton()->getObject('temp')->getPage()->setJs('../UI-Core/plugins/sigma/grid/flashchart/fusioncharts/FusionCharts.js');
BM::singleton()->getObject('temp')->getPage()->setJs('../UI-Core/js/hot.js');

BM::storeSetting(array("trace" => 1, "exception" => true, "soap_version"=>SOAP_1_1), "SOAP_OPTIONS");

$front = new frontController(array());  
$front->run();                         

exit();

?>
<?php 
/*
 * feindura - Flat File Content Management System
 * Copyright (C) Fabian Vogelsteller [frozeman.de]
 *
 * This program is free software;
 * you can redistribute it and/or modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not,see <http://www.gnu.org/licenses/>.
 */
/**
 * This file includes all necessary <var>classes</var> and configs for the use in the FRONTEND and the BACKEND
 *
 * @version 0.12
 */

define('DOCUMENTROOT',$_SERVER["DOCUMENT_ROOT"]);

$phpTags = file(dirname(__FILE__)."/process/phptags.txt"); 
define('PHPSTARTTAG',$phpTags[0]."\n");
define('PHPENDTAG',"\n".$phpTags[1]);

// get SETTINGS

/**
 * The administrator-settings config
 *
 * This config <var>array</var> is included from: <i>"feindura-CMS/config/admin.config.php"</i>
 *
 * @global array $adminConfig
 */
if(!$adminConfig =      @include_once(dirname(__FILE__)."/../config/admin.config.php"))
  $adminConfig =      array();

/**
 * The website-settings config
 *
 * This config <var>array</var> is included from: <i>"feindura-CMS/config/website.config.php"</i>
 *
 * @global array $websiteConfig
 */
if(!$websiteConfig =    @include_once(dirname(__FILE__)."/../config/website.config.php"))
  $websiteConfig =    array();

/**
 * The categories-settings config
 *
 * This config <var>array</var> is included from: <i>"feindura-CMS/config/category.config.php"</i>
 *
 * @global array $categories
 */
if(!$categories =       @include_once(dirname(__FILE__)."/../config/category.config.php"))
  $categories =       array();

/**
 * The statistic-settings config
 *
 * This config <var>array</var> is included from: <i>"feindura-CMS/config/statistic.config.php"</i>
 *
 * @global array $statisticConfig
 */
if(!$statisticConfig =  @include_once(dirname(__FILE__)."/../config/statistic.config.php"))
  $statisticConfig =  array();

/**
 * The website-statistics
 *
 * This statistics <var>array</var> is included from: <i>"feindura-CMS/config/website.statistic.php"</i>
 *
 * @global array $websiteStatistic
 */
if(!$websiteStatistic = @include_once(dirname(__FILE__)."/../statistic/website.statistic.php"))
  $websiteStatistic = array();


// INCLUDES
/**
 * Includes the {@link generalFunctions} <var>class</var>
 */
require_once(dirname(__FILE__)."/classes/generalFunctions.class.php");

/**
 * Includes the {@link statisticFunctions} <var>class</var>
 */
require_once(dirname(__FILE__)."/classes/statisticFunctions.class.php");

?>
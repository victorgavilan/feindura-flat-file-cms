<?php 
/*
    feindura - Flat File Content Management System
    Copyright (C) 2009 Fabian Vogelsteller [frozeman.de]

    This program is free software;
    you can redistribute it and/or modify it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 3 of the License, or (at your option) any later version.

    This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
    without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
    See the GNU General Public License for more details.

    You should have received a copy of the GNU General Public License along with this program;
    if not,see <http://www.gnu.org/licenses/>.

* frontend.include.php version 0.19
*/

require_once(dirname(__FILE__)."/general.include.php");

@include_once(dirname(__FILE__)."/../config/adminConfig.php");
@include_once(dirname(__FILE__)."/../config/websiteConfig.php");
@include_once(dirname(__FILE__)."/../config/categoryConfig.php");

@include_once(dirname(__FILE__)."/../statistic/websiteStatistic.php");

require_once(dirname(__FILE__)."/functions/general.functions.php");
require_once(dirname(__FILE__)."/functions/statistic.functions.php");
require_once(dirname(__FILE__)."/functions/mysql.functions.php");

require_once(dirname(__FILE__)."/functions/frontend.functions.php");

require_once(dirname(__FILE__)."/classes/frontend.classes.php");

?>
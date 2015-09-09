<?php
/*=================================================
	Project: Employee Scheduler
	File: pnindex.php
	Author: John Finlay
	Comments:
		postNuke module files.
		
	Copyright (C) 2002 to 2003  John Finlay and Others

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
===================================================*/

if (!eregi("modules.php", $PHP_SELF)) {
	die ("You can't access this file directly...");
} 

session_start();

global $config;
global $ES_BASEDIR;
global $ES_MODULENAME;
$ES_MODULENAME = $name;
$ES_BASEDIR = "modules/$ES_MODULENAME/";
if (!isset($config)) { include("config.php"); }
$config["module"] = "empscheduler";

$username = "";
$_SESSION['pgv_user'] = "";

if (pnUserLoggedIn()) 
{
	$username = pnUserGetVar('uname');

	list($userperms, $groupperms) = pnSecGetAuthInfo();
	if ((count($userperms) == 0) &&
            (count($groupperms) == 0)) 
	{
         print "no permissions<br>";
		// No permissions - is an error - how did they get here ?
         return;
	}
}

if (!empty($username)) { 
	$_SESSION['es_username'] = $username;
}
		
// go to scheduler
pnRedirect("$ES_BASEDIR/index.php");
exit;

?>

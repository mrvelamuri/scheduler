<?php
session_start();
ini_set('error_reporting' , false);
ini_set('error_reporting' , false);

/*********************************************************
	File: index.php
	Project: Employee Scheduler
	Author: John Finlay
	Comments:
		The home page for the site.  Asks a user to login
		and then redirects them to the appropriate section
		for employees or supervisors
		
	For site documentation and setup see the README.txt file
	included with the distrobution package.  If you did not
	receive this file, it can be found at 
	http://empscheduler.sourceforge.net
	
	Copyright (C) 2003  Brigham Young University

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
**********************************************************/

require "es_functions.php";

//-- authenticate the user
if($_SESSION['uname']){
 print_header("Welcome");
echo "I am abot to change";
 require("tablegrid.php");
} else {
 $user = auth_user();
 print_r($user);
}
print_footer();

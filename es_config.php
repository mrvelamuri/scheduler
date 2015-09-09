<?php
/*********************************************************
	File: es_config.php
	Project: Employee Scheduler
	Author: John Finlay
	Revision: $Revision: 1.7 $
	Date: $Date: 2004/05/12 22:39:35 $
	Comments:
		Contains common control variables for the site.
		
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

//--prevent direct access of this file
if (strstr($_SERVER["PHP_SELF"],"config.php")) {
	print "Why do you want to do that?";
	exit;
}

// ---- Define global variables
//- Database Connection variables 
#$DBHOST = "113.128.162.84";			//- MySQL Database Host
$DBHOST = "localhost";
$DBNAME = "scheduler";		//- MySQL Database Name
$DBUSER = "root";			//- MySQL DB Username
$DBPASS = "";			//- MySQL DB Userpassword

/*
//-- LDAP Authentication variables
$LDAP_HOST = "ldaps://ldap.yourdomain.com";	//-- LDAP Host URL
$LDAP_PORT = 636;							//-- LDAP Port
$LDAP_SEARCHBASE = "ou=people,o=yourdomain.com";	//-- LDAP search base
$LDAP_CONTEXT = "ou=people,o=yourdomain.com";		//-- LDAP Context
$LDAP_USER_ID_PROP = "uid";					//-- LDAP User identifying field
$LDAP_ATTRS_ARRAY = array("cn","mail");		//-- LDAP Attributes to return after the ldap search
*/
?>

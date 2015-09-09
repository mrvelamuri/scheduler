<?php
/*********************************************************
	File: es_sup_employees.php
	Project: Employee Scheduler
	Author: John Finlay
	Revision: $Revision: 1.12 $
	Date: $Date: 2004/07/20 15:23:26 $
	Comments:
		Shows the supervisor a list of their employees
		
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
dbconnect();

$user = auth_supervisor();

if ((!empty($su))&&(preg_match("/Admin/i", $_SESSION["u_type"])>0)) {
	$sql = "SELECT * FROM es_user WHERE u_id='".$su."'";
	$res = dbquery($sql);
	if (mysql_num_rows($res)>0) {
		$user = mysql_fetch_array($res);
		$user = db_cleanup($user);
		$_SESSION["es_username"] = $user["u_netid"];
	}
}

print_header($es_lang["supervisors"]);

print '<br><br><span class="pagetitle">'.$es_lang["supervisors"].' '.$es_lang["list"].'</span><br><img src="images/bar_1.gif" width="75%" height="2">';
if (preg_match("/Admin/i", $_SESSION["u_type"])>0) {
	if ($view!="print") {
		print "<br><a href=\"es_sup_edit_employee.php?u_type=Supervisor\">".$es_lang["new_supervisor"]."</a>";
		print " | <a href=\"es_sup_email_supervisors.php\">".$es_lang["email_all_sup"]."</a>\n";
	}
	if (!empty($delete_id)) {
		delete_user($delete_id);
	}
}

$employees = get_supervisors();
print "<br>";

foreach($employees as $employee) {
	print '<br><table width="85%" cellpadding="0" cellspacing="0"><tr><td><span class="sectitle">';
	if (!empty($employee["u_picture"])) print '<img align="bottom" src="photos/'.$employee["u_picture"].'" height="100"> ';
	print $employee["u_name"].'</span></td><td valign="bottom" align="right" class="text">';
	if ($view!="print") {
		if (preg_match("/Admin/i", $_SESSION["u_type"])>0) {
			print '<a href="es_sup_edit_employee.php?u_id='.$employee["u_id"].'">'.$es_lang["edit"].'</a> | ';
			print '<a href="es_sup_supervisors.php?su='.$employee["u_id"].'">Assume User</a> | ';
			if ($user["u_id"]!=$employee["u_id"]) print '<a href="es_sup_supervisors.php?delete_id='.$employee["u_id"].'" onclick="return confirm(\''.$es_lang["confirm_sup"].'\');">'.$es_lang["delete"].'</a>';
		}
	}
	print '</td></tr>';
	print '<tr><td colspan="2"><img src="images/bar_1.gif" width="100%" height="2"></td></tr>';
	print "<tr><td colspan=\"2\"><div class=\"text\" style=\"padding-left: 15px;\">";
	print "<table><tr><td valign=\"top\" class=\"text\">\n";
	if (preg_match("/Admin/i", $_SESSION["u_type"])>0) {
		if (!empty($employee["u_netid"]))print $es_lang["username"]." ".$employee["u_netid"]."<br>\n";
	}
	if (!empty($employee["u_major"])) print $es_lang["major"]." ".$employee["u_major"]."<br>\n";
	if (!empty($employee["u_workphone"]))print $es_lang["work_phone"]." ".$employee["u_workphone"]."<br>\n";
	if (!empty($employee["u_homephone"]))print $es_lang["home_phone"]." ".$employee["u_homephone"]."<br>\n";
	if (!empty($employee["u_email"]))print $es_lang["email"]." <a href=\"mailto:".$employee["u_email"]."\">".$employee["u_email"]."</a><br>\n";
	print "</td><td width=\"50\"><br></td><td valign=\"top\" class=\"text\">\n";
	$areas = get_supervisor_areas($employee);
	if (count($areas)>0) {
		print "<b>Area List:</b><br>";
		foreach($areas as $area) {
			print $area["a_name"]."<br>\n";
		}
	}
	print "</td></tr></table>\n";
	print "</div>\n</td></tr></table>";
}

print_footer();
?>

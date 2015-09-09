<?php
/*********************************************************
	File: es_sup_employees.php
	Project: Employee Scheduler
	Author: John Finlay
	Revision: $Revision: 1.8 $
	Date: $Date: 2004/06/21 15:34:29 $
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

$user = auth_supervisor();
print_header($es_lang["employees"]);

print '<br><br><span class="pagetitle">'.$es_lang["employee"].' '.$es_lang["list"].'</span><br><img src="images/bar_1.gif" width="75%" height="2">';

if ((!empty($delete_id))&&($delete_id!=$user["u_id"])) {
	delete_user($delete_id);
}	
$employees = get_supervisor_employees($user);

if (count($employees)==0) {
	print "<br><br><br>".$es_lang["no_emp"]."  <a href=\"es_sup_edit_employee.php\">".$es_lang["click_new_emp"]."</a>\n";
	print_footer();
	exit;
}

if ($view!="print") {
	print "<br><a href=\"es_sup_edit_employee.php\">".$es_lang["add_new_emp"]."</a> | ";
	print "<a href=\"es_sup_email_employees.php\">".$es_lang["email_emp"]."</a>\n";
}
print "<br>";

foreach($employees as $employee) {
	print '<br><table width="85%" cellpadding="0" cellspacing="0"><tr><td width="50%"><span class="sectitle">';
	if (!empty($employee["u_picture"])) print '<img align="bottom" src="photos/'.$employee["u_picture"].'" height="100" style="border: solid '.$employee["u_color"].' 5px;" />';
	print $employee["u_name"].'</span></td><td width="50%" valign="bottom" align="right" class="text">';
	if ($view!="print") {
		print '<a href="es_sup_edit_employee.php?u_id='.$employee["u_id"].'">'.$es_lang["edit"].'</a> | ';
		if ($user["u_id"]!=$employee["u_id"]) print '<a href="es_sup_employees.php?delete_id='.$employee["u_id"].'" onclick="return confirm(\''.$es_lang["emp_confirm"].'\');">'.$es_lang["delete"].'</a> | ';
		print '<a href="es_sup_employee_past_schedule.php?u_id='.$employee["u_id"].'">'.$es_lang["past_schedules"].'</a> | ';
		print '<a href="es_sup_employee_schedule.php?u_id='.$employee["u_id"].'">'.$es_lang["view_schedule"].'</a>';
	}
	print '</td></tr>';
	print '<tr><td colspan="2"><img src="images/bar_1.gif" width="100%" height="2"></td></tr>';
	print "<tr><td><div class=\"text\" style=\"padding-left: 15px;\">";
	if (!empty($employee["u_netid"])) print $es_lang["username"]." ".$employee["u_netid"]."<br>\n";
	if (!empty($employee["u_major"])) print $es_lang["major"]." ".$employee["u_major"]."<br>\n";
	if (!empty($employee["u_workphone"])) print $es_lang["work_phone"]." ".$employee["u_workphone"]."<br>\n";
	if (!empty($employee["u_homephone"])) print $es_lang["home_phone"]." ".$employee["u_homephone"]."<br>\n";
	if (!empty($employee["u_email"])) print $es_lang["email"]." <a href=\"mailto:".$employee["u_email"]."\">".$employee["u_email"]."</a><br>\n";
	print "</div>\n</td>\n";
	print "<td class=\"text\">";
	$positions = get_user_positions($employee);
	$total_hours = 0;
	if (count($positions)>0) {
		print "<b>".$es_lang["position_assignments"]."</b><br>";
		foreach($positions as $key=>$value) {
			$total_hours += $value;
			$position = get_position($key);
			print $position["p_name"].": ".$value." ".$es_lang["hours"]."<br>\n";
		}
		print "<b>".$es_lang["total_scheduled"]." $total_hours ".$es_lang["hours"]."</b><br>\n";
	}
	print "</td>\n";
	print "</tr></table>";
}

print_footer();
?>

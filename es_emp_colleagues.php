<?php
/*********************************************************
	File: es_emp_colleagues.php
	Project: Employee Scheduler
	Author: John Finlay
	Comments:
		Shows the user a list of their colleagues
		
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

$user = auth_user();
print_header($es_lang["my_colleagues"]);

print '<br><br><span class="pagetitle">My Colleagues</span><br><img src="images/bar_1.gif" width="75%" height="2">';

$e_ids = array();
$positions = get_user_positions($user);
foreach($positions as $key=>$value) {
	$position = get_position($key);
	$pschedules = get_position_schedules($position);
	foreach($pschedules as $schedule) {
		$assignments = $schedule["s_assignments"];
		foreach($assignments as $assignment_hour) {
			foreach($assignment_hour as $assignment) {
				if (!in_array($assignment["pa_u_id"], $e_ids)) $e_ids[] = $assignment["pa_u_id"];
			}
		}
	}
}

$employees = array();
foreach($e_ids as $id) {
	$employees[] = get_user($id);
}
usort($employees, "user_cmp");

if (count($employees)==0) {
	print "<br><br><br>".$es_lang["no_colleagues"];
	print_footer();
	exit;
}

print "<br>";

foreach($employees as $employee) {
	if ($user["u_id"]!=$employee["u_id"]) {
		print '<br><table width="85%" cellpadding="0" cellspacing="0"><tr><td width="50%"><span class="sectitle">';
		if (!empty($employee["u_picture"])) print '<img align="bottom" src="photos/'.$employee["u_picture"].'" height="100"> ';
		print $employee["u_name"].'</span></td><td width="50%" valign="bottom" align="right" class="text">';
		if ($view!="print") {
			print '<a href="es_emp_employee_schedule.php?u_id='.$employee["u_id"].'">'.$es_lang["view_schedule"].'</a>';
		}
		print '</td></tr>';
		print '<tr><td colspan="2"><img src="images/bar_1.gif" width="100%" height="2"></td></tr>';
		print "<tr><td><div class=\"text\" style=\"padding-left: 15px;\">";
		if (!empty($employee["u_major"])) print $es_lang["major"]." ".$employee["u_major"]."<br>\n";
		if (!empty($employee["u_workphone"]))print $es_lang["work_phone"]." ".$employee["u_workphone"]."<br>\n";
		if (!empty($employee["u_homephone"]))print $es_lang["home_phone"]." ".$employee["u_homephone"]."<br>\n";
		if (!empty($employee["u_email"]))print $es_lang["email"]." <a href=\"mailto:".$employee["u_email"]."\">".$employee["u_email"]."</a><br>\n";
		print "</div>\n</td>\n";
		print "<td class=\"text\">";
		$positions = get_user_positions($employee);
		if (count($positions)>0) {
			print "<b>".$es_lang["position_assignments"]."</b><br>";
			foreach($positions as $key=>$value) {
				$position = get_position($key);
				print $position["p_name"]."<br>\n";
			}
		}
		print "</td>\n";
		print "</tr></table>";
	}
}

print_footer();
?>
<?php
/*********************************************************
	File: es_sup_email_employees.php
	Project: Employee Scheduler
	Author: John Finlay
	Revision: $Revision: 1.1 $
	Date: $Date: 2004/05/17 17:02:14 $
	Comments:
		Allows a supervisor to spam their employees
		
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

print '<br><br><span class="pagetitle">'.$es_lang["email_emp"].'</span><br><img src="images/bar_1.gif" width="75%" height="2">';

$employees = get_supervisor_employees($user);

if (count($employees)==0) {
	print "<br><br><br>".$es_lang["no_emp"]."  <a href=\"es_sup_edit_employee.php\">".$es_lang["click_new_emp"]."</a>";
	print_footer();
	exit;
}

print "<br /><br />";

if (empty($action)) $action = "";

if ($action=="send") {
	$subject = $_POST["subject"];
	$message = $_POST["body"];
	foreach($employees as $employee) {
		if (!empty($user["u_email"])) {
			if ($ES_FULL_MAIL_TO) $headers = "From: ".$user["u_name"]." <".$user["u_email"].">\r\n";
			else $headers = "From: ".$user["u_email"]."\r\n";
		}
		if ($ES_FULL_MAIL_TO) $to = $employee["u_name"]." <".$employee["u_email"].">";
		else $to = $employee["u_email"];
		$g = mail($to, $subject, $message, $headers);
		if ($g) print $es_lang["send_successful"]."<b>".$to."</b><br />\n";
	}
}
//-- print the email compose form
else {
	print $es_lang["email_emp_inst"]."<br /><br />\n";
	print "<form method=\"post\" action=\"es_sup_email_employees.php\">\n";
	print "<input type=\"hidden\" name=\"action\" value=\"send\">\n";
	print "<table>";
	print "<tr><td align=\"right\" class=\"text\">".$es_lang["subject"]."</td><td><input type=\"text\" size=\"50\" name=\"subject\"></td></tr>\n";
	print "<tr><td align=\"right\" class=\"text\">".$es_lang["body"]."</td><td><textarea rows=\"10\" cols=\"50\" name=\"body\"></textarea></td></tr>\n";
	print "</table>\n";
	print "<input type=\"submit\" value=\"".$es_lang["send"]."\">\n";
	print "</form>\n";
}

print_footer();
?>

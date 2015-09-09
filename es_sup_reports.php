<?php
/*********************************************************
	File: es_sup_reports.php
	Project: Employee Scheduler
	Author: John Finlay
	Comments:
		Supervisor reporting features
		
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
print_header($es_lang["reports"]);

print '<br /><br /><span class="pagetitle">'.$es_lang["reports"].'</span><br /><img src="images/bar_1.gif" width="85%" height="2">';

print "<br /><br /><b>".$es_lang["select_report"]."</b><br /><br />";
print "<ul style=\"width: 70%;\">\n";
print "<li><a href=\"es_sup_report_employees.php\">".$es_lang["emp_report"]."</a> - ".$es_lang["emp_report_descr"]."<br /><br /></li>\n";
print "<li><a href=\"es_sup_report_positions.php\">".$es_lang["pos_report"]."</a> - ".$es_lang["pos_report_descr"]."<br /><br /></li>\n";
print "</ul><br />\n";

print_footer();
?>

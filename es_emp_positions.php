<?php
/*********************************************************
	File: es_emp_positions.php
	Project: Employee Scheduler
	Author: John Finlay
	Comments:
		Shows the user a list of their positions
		
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
print_header($es_lang["my_positions"]);

print '<br><br><span class="pagetitle">'.$es_lang["my_positions"].'</span><br><img src="images/bar_1.gif" width="75%" height="2">';

$positions = array();
$p_ids = get_user_positions($user);
foreach($p_ids as $key=>$value) {
	$positions[] = get_position($key);
}

if (count($positions)==0) {
	print "<br><br><br>".$es_lang["not_scheduled"];
	print_footer();
	exit;
}

print "<br>";

foreach($positions as $position) {
	print '<br><table width="85%" cellpadding="0" cellspacing="0"><tr><td width="50%"><span class="sectitle">';
	print $position["p_name"].'</span></td><td width="50%" valign="bottom" align="right" class="text">';
	if ($view!="print") {
		print '<a href="es_emp_position_schedule.php?p_id='.$position["p_id"].'">'.$es_lang["view_schedule"].'</a>';
	}
	print '</td></tr>';
	print '<tr><td colspan="2"><img src="images/bar_1.gif" width="100%" height="2"></td></tr>';
	print "<tr><td><div class=\"text\" style=\"padding-left: 15px;\">";
	print $position["p_description"];
	print "</div>\n</td>\n";
	print "<td class=\"text\">";
	print "</td>\n";
	print "</tr></table>";
}


print_footer();
?>
<?php
/*********************************************************
	File: es_sup_index.php
	Project: Employee Scheduler
	Author: John Finlay
	Comments:
		Default page for a supervisor.  Shows the supervisor's
		areas and positions.
		
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

//-- only allow supervisors to view page
$user = auth_supervisor();
print_header($es_lang["areas_and_positions"]);
print '<br><br><span class="pagetitle">'.$es_lang["areas_and_positions"].'</span><br><img src="images/bar_1.gif" width="85%" height="2">';

if (!isset($action)) $action="";

//-- check for delete area action
if (($action=="deletearea")&&(!empty($a_id))) {
	$area = get_area($a_id);
	delete_area($area);
}
//-- check and do delete position action
if (($action=="deletepos")&&(!empty($p_id))) {
	$position = get_position($p_id);
	delete_position($position);
}
//-- get supervisor areas as an array of areas
$areas = get_supervisor_areas($user);
//-- if the supervisor has no areas then provide them with a link to create an area or to the tutorial
if (count($areas)==0) {
	print "<br><br><br>".$es_lang["no_areas"]."  <a href=\"es_sup_edit_area.php\">".$es_lang["click_new_area"]."</a><br><br>";
	print $es_lang["sup_instructions"];
	print_footer();
	exit;
}
//-- if not in print-preview mode then show the add new area link
if ($view!="print") {
	print "<br><a href=\"es_sup_edit_area.php\">".$es_lang["add_area"]."</a>";
}
print "<br>";
//-- loop through the areas and print out a list
foreach($areas as $area) {
	print '<br><table width="85%" cellpadding="0" cellspacing="0"><tr><td><span class="sectitle">'.$area["a_name"].'</span></td><td align="right" class="text">';
	if ($view!="print") {
		print '<a href="es_sup_edit_area.php?a_id='.$area["a_id"].'">'.$es_lang["edit"].'</a> | ';
		print '<a href="es_sup_index.php?action=deletearea&a_id='.$area["a_id"].'" onclick="return confirm(\''.$es_lang["area_confirm"].'\');">delete</a> | ';
		print '<a href="es_sup_edit_position.php?a_id='.$area["a_id"].'">'.$es_lang["add_position"].'</a> | ';
		print '<a href="es_sup_area_schedule.php?a_id='.$area["a_id"].'">'.$es_lang["view_schedule"].'</a>';
	}
	print '</td></tr>';
	print '<tr><td colspan="2"><img src="images/bar_1.gif" width="100%" height="2"></td></tr>';
	print "<tr><td colspan=\"2\"><div class=\"text\" style=\"padding-left: 15px;\">";
	print $area["a_description"]."<br>";
	if (count($area["a_positions"])>0) {
		print "<b>Position List:</b><br>";
		print "<table cellspacing=3>\n";
		foreach($area["a_positions"] as $position) {
			print "<tr><td class=text>".$position["p_name"]."</td><td width=10><br></td>\n";
			if ($view != "print") {
				print "<td class=text><a href=\"es_sup_edit_position.php?p_id=".$position["p_id"]."\">".$es_lang["edit"]."</a> |</td>\n";
				print "<td class=text><a href=\"es_sup_index.php?action=deletepos&p_id=".$position["p_id"]."\" onclick=\"return confirm('".$es_lang["pos_confirm"]."');\">".$es_lang["delete"]."</a> |</td>\n";
				print "<td class=text><a href=\"es_sup_position_past.php?p_id=".$position["p_id"]."\">".$es_lang["past_schedules"]."</a> |</td>\n";
				print "<td class=text><a href=\"es_sup_position.php?p_id=".$position["p_id"]."\">".$es_lang["schedule"]."</a></td>\n";
			}
			print "</tr>\n";
		}
		print "</table>\n";
	}
	print "</div>\n</td></tr></table>";
}

print_footer();
?>

<?php
/*********************************************************
	File: es_emp_index.php
	Project: Employee Scheduler
	Author: John Finlay
	Revision: $Revision: 1.16 $
	Date: $Date: 2004/12/02 18:16:58 $
	Comments:
		Default page for an employee.  Shows the employee's
		schedule.
		
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

//-- set the default action view
if (empty($action)) $action="week";
if (!isset($gotoedit)) $gotoedit = false;
if (!isset($deleteschedule)) $deleteschedule = false;

if (isset($view_schedule)) {
	$temp = preg_split('/\|/', $view_schedule);
	if (count($temp)==2) {
		$s_group = $temp[0];
		$s_exptime = $temp[1];
	}
}
//-- only allow authenticated users to use this page
$user = auth_user();

if ($gotoedit) {
	if (!empty($s_group) && !empty($s_exptime)) {
		header("Location: es_emp_edit_schedule.php?s_group=$s_group&s_exptime=$s_exptime&".session_name()."=".session_id());
		exit;
	}
}

if ($deleteschedule) {
	delete_user_schedule_group($s_group, $s_exptime, $user);
	$s_group = "";
	$s_exptime = "";
}

print_header($es_lang["my_schedule"]);
print '<br><br><span class="pagetitle">'.$es_lang["my_schedule"].'</span><br><img src="images/bar_1.gif" width="85%" height="2">';
//-- get the schedules as an array of schedules for this user
$schedules = get_user_schedules($user);

//-- if the schedule hasn't been created yet then provide a link to create a new one and a link to the online help pages
if (count($schedules)==0) {
	print "<br><br><br>".$es_lang["have_note_created"]."  <a href=\"es_emp_edit_schedule.php\">".$es_lang["click_to_create"]."</a><br><br>\n";
	print $es_lang["to_help"]." <a href=\"es_help.php\" target=\"help\">".$es_lang["employee"]." ".$es_lang["tutorial"]."</a>.<br><br>\n";
	print_footer();
	exit;
}
$group = array();
if (!isset($s_group)) $s_group=$schedules[0]["s_group"];
if (!isset($s_exptime)) $s_exptime=$schedules[0]["s_exptime"];
foreach($schedules as $schedule) {
	if (($s_group==$schedule["s_group"])&&($s_exptime==$schedule["s_exptime"])) $group[] = $schedule;
}

$total_hours = 0;
$total_assigned = 0;
$j=0;
//-- add up the total hours and check if there are any changes in the hours before 6am and force the first hour to be midnight
foreach($group as $schedule) {
	$total_hours += (strlen($schedule["s_hours"])-substr_count($schedule["s_hours"], "0"))*.25;
	$total_assigned += count($schedule["s_assignments"])*.25;
}

//-- set the default to hide weekends
if (!isset($show_weekends)) $show_weekends = false;	
$firstday = 0;
$lastday = 7;
if (!$show_weekends) {
	$firstday = 1;
	$lastday = 6;
}

//-- show schedule as a week view
if ($action=="week") {
if ($view!="print") {
?>
	<script language="JavaScript">
	function goedit() {
		document.scheduleform.gotoedit.value='1';
		document.scheduleform.submit();
	}
	function godelete() {
		if (confirm('<?php print $es_lang["confirm_schedule"]; ?>')) {
			document.scheduleform.deleteschedule.value='1';
			document.scheduleform.submit();
		}
	}
	</script>
<?php

	print '<br>'.$es_lang["week"].' | <a href="es_emp_index.php?action=list&s_group='.$s_group.'&s_exptime='.$s_exptime.'">'.$es_lang["list"].'</a> ';
	print '<br><br>';
	if (count($schedules)>7) {
		print "<table><tr><td>";
		print "<form name=\"scheduleform\" method=\"get\" action=\"es_emp_index.php\">\n";
		print "<input type=\"hidden\" name=\"gotoedit\" value=\"0\">\n";
		print "<input type=\"hidden\" name=\"deleteschedule\" value=\"0\">\n";
		print $es_lang["select_schedule"]."<br>\n";
		print "<select name=\"view_schedule\">\n";
		$old_group = "";
		$old_time = "";
		foreach($schedules as $schedule) {
			if (($schedule["s_group"]!=$old_group)||($schedule["s_exptime"]!=$old_time)) {
				print "<option value=\"".$schedule["s_group"]."|".$schedule["s_exptime"]."\"";
				if (($schedule["s_group"]==$s_group)&&($schedule["s_exptime"]==$s_exptime)) print " selected";
				print ">".$schedule["s_group"]." from ".date("j M Y", $schedule["s_starttime"])." to ".date("j M Y", $schedule["s_exptime"])."</option>\n";
				$old_group = $schedule["s_group"];
				$old_time = $schedule["s_exptime"];
			}
		}
		print "</select><br>\n";
		print "<input type=\"submit\" value=\"".$es_lang["view"]."\"> <input type=\"button\" value=\"".$es_lang["edit"]."\" onclick=\"goedit();\"> ";
		print "<input type=\"button\" value=\"".$es_lang["delete"]."\" onclick=\"godelete();\"> ";
		print "<input type=\"button\" value=\"".$es_lang["new"]."\" onclick=\"window.location='es_emp_edit_schedule.php?create=create&".session_name()."=".session_id()."';\">\n";
		print "</form>\n";
		print "</td></tr></table>\n";
	}
?>
<table>
<tr>
<td valign="top">
	<table border=0 cellspacing=1 bgcolor="black">
		<tr bgcolor="<?php print $PRIORITY[0]?>"><td><?php print $es_lang["unavailable"]; ?></td></tr>
		<tr bgcolor="<?php print $PRIORITY[1]?>"><td><?php print $es_lang["pref1"]; ?></td></tr>
		<tr bgcolor="<?php print $PRIORITY[2]?>"><td><?php print $es_lang["pref2"]; ?></td></tr>
		<tr bgcolor="<?php print $PRIORITY[3]?>"><td><?php print $es_lang["pref3"]; ?></td></tr>
	</table>
</td>
<td width="20"><br></td>
<td>
	<table>
	<tr><td align="right"><?php print $es_lang["min_left"];?></td><td> <?php print $user["u_min"];?></td></tr>
	<tr><td align="right"><?php print $es_lang["max_left"];?></td><td> <?php print $user["u_max"];?></td></tr>
	<tr><td align="right"><b><?php print $es_lang["total_hours"];?></b></td><td><?php print $total_hours;?></td></tr>
	<tr><td align="right"><b><?php print $es_lang["hours_desired"];?></b></td><td><?php print $user["u_hours"];?></td></tr>
	<tr><td align="right"><b><?php print $es_lang["total_scheduled"];?></b></td><td><?php print $total_assigned;?></td></tr>
	</table>
</td>
<td width="10"><br></td>
<td valign="top">
	<?php
		print "Repeat ".$REPEAT[$group[0]["s_repeat"]]." <br>from ".date("j M Y", $group[0]["s_starttime"])." to ".date("j M Y", $group[0]["s_exptime"])."<br>\n";
	?>
</td>
</tr>
</table>

	<br>
	<form name="timeform" method="get">
	<input type="hidden" name="s_exptime" value="<?php print $s_exptime; ?>" />
	<input type="hidden" name="s_group" value="<?php print $s_group; ?>" />
	<?php print $es_lang["first_hour"]; ?> <select name="first_hour" onchange="document.timeform.submit();">
	<?php 
		$stime = mktime($START_HOUR, 0, 0, 1,1,2003);
		for($i=$START_HOUR; $i<$END_HOUR-2; $i++) {
			print "<option value=\"$i\"";
			if ($first_hour==$i) print " selected";
			print ">".date($TIME_FORMAT, $stime)."</option>\n";
			$stime+=(60*60);
		}
	?>
	</select>
	<?php print $es_lang["last_hour"]; ?> <select name="last_hour" onchange="document.timeform.submit();">
	<?php 
		$stime = mktime($START_HOUR, 0, 0, 1,1,2003);
		for($i=$START_HOUR; $i<=$END_HOUR; $i++) {
			print "<option value=\"$i\"";
			if ($last_hour==$i) print " selected";
			print ">".date($TIME_FORMAT, $stime)."</option>\n";
			$stime+=(60*60);
		}
	?>
	</select>
	<?php print $es_lang["resolution"]; ?> <select name="sections_in_day" onchange="document.timeform.submit();">
	<option value="24"<?php if ($sections_in_day==24) print " selected"; ?>>1 <?php print $es_lang["hour"]; ?></option>
	<option value="48"<?php if ($sections_in_day==48) print " selected"; ?>>1/2 <?php print $es_lang["hour"]; ?></option>
	<option value="96"<?php if ($sections_in_day==96) print " selected"; ?>>15 <?php print $es_lang["min"]; ?></option>
	</select>
	</form>
<?php 
}
?>
	<table border="1" cellspacing="0" bordercolor="black" style="border-collapse: collapse; empty-cells: show;">
	<tr bgcolor="#F0F0E2">
		<?php 
			print "<th class=\"daycell\">".$es_lang["hours"]."</th>\n";
			$j = $WEEKSTART;
			for($d=0; $d<7; $d++) {
				print "<th class=\"daycell\">".$DAYS[$j]["short"]."</th>\n";
				$j++;
				if ($j>6) $j=0;
			}
			print "</tr>\n";
		?>
	</tr>
<?php
	$firstday = 0;
	$lastday = 7;
	$htime = mktime($first_hour, 0, 0, 1, 1, 2003);
	//-- loop through 24 hours in a day
	for ($i=($first_hour*4); $i<($last_hour*4); $i+=(4/$sections_in_hour)) {
		print "<tr>\n";
		print "\t<td class=\"timecell\">". date($TIME_FORMAT, $htime). "</td>\n";
		//-- loop through the days a week
		$j = $WEEKSTART;
		for ($d=$firstday; $d<$lastday; $d++) {
			$hours = $group[$j]["s_hours"];
			$pry = 0;
			//-- get the background color based on the priority value
			for ($k=0; $k<4/$sections_in_hour; $k++) {
				if ($pry < $hours[$i+$k]) $pry = $hours[$i+$k];
			}
			for ($k=0; $k<4/$sections_in_hour; $k++) {
				if (isset($group[$j]["s_assignments"][$i+$k])) {
					$pry=7;
				}
			}
			$bgcolor = $PRIORITY[$pry];
			//-- set default unavailable background color
			if (empty($bgcolor)) $bgcolor = $PRIORITY[0];
			print "\t<td bgcolor=\"$bgcolor\" class=\"schedulecell\">";
			//-- check for schedule assignments and show them
			if (isset($group[$j]["s_assignments"][$i])) {
				$assignment = $group[$j]["s_assignments"][$i];
				$position = get_position($assignment["pa_p_id"]);
				print "<b>".$position["p_name"]."</b><br>";
				$pschedule = get_schedule($assignment["pa_s_id"]);
				if ($pschedule["s_repeat"]!=$REPEAT[1]) {
					print $pschedule["s_group"]."<br>\n";
					$nextdate = get_next_date($pschedule, $j);
					if ($nextdate > $pschedule["s_exptime"]) print $es_lang["finished"];
					else {
						print date("m/d/Y", $nextdate);
					}
				}
			}
			//-- if we are in print-preview mode then show the preference level by number
			if (($view=="print")&&($hours[$i]>0)) print "<b>P-".$hours[$i]."</b>";
			$coursedisp = "none";
			$commentdisp = "none";
			//-- check if the comment or course layers should be shown
			if (isset($group[$j]["s_comments"][$i])) {
				$comment = $group[$j]["s_comments"][$i];
				if (!empty($comment["sc_course"])) {
					$coursedisp = "block";
				}
				if (!empty($comment["sc_comment"])) {
					$commentdisp = "block";
				}
			}
			else {
				$comment = array();
			}
			//-- print out any courses for this hour
			if ($coursedisp!="none") {
				print "<div id=\"div-course-$i-$j\" style=\"display: $coursedisp;\">\n";
				print $es_lang["course"].": ".$comment["sc_course"]."<br>\n";
				print $es_lang["building"]." ".$comment["sc_building"]."<br>\n";
				print "</div>\n";
			}
			//-- print out any commnets for this hour
			if ($commentdisp!="none") {
				print "<div id=\"div-comment-$i-$j\" style=\"display: $commentdisp;\">\n";
				print $es_lang["comment"].": ".$comment["sc_comment"]."\n";
				print "</div>\n";
			}
			print "</td>\n";
			$j++;
			if ($j>6) $j=0;
		}
		print "</tr>\n";
		$htime += (60/$sections_in_hour)*60;
	}
?>	
	</table>
	<br>
<?php
}
//-- show the schedule in a list view
else if ($action=="list") {
	if ($view!="print") {
		?>
	<script language="JavaScript">
	function goedit() {
		document.scheduleform.gotoedit.value='1';
		document.scheduleform.submit();
	}
	function godelete() {
		if (confirm('<?php print $es_lang["confirm_schedule"]; ?>')) {
			document.scheduleform.deleteschedule.value='1';
			document.scheduleform.submit();
		}
	}
	</script>
<?php
		print '<br><a href="es_emp_index.php?action=week&s_group='.$s_group.'&s_exptime='.$s_exptime.'">'.$es_lang["week"].'</a> | '.$es_lang["list"].' ';
		print '<br>';
		if (count($schedules)>7) {
			print "<table><tr><td>";
			print "<form name=\"scheduleform\" method=\"get\" action=\"es_emp_index.php\">\n";
			print "<input type=\"hidden\" name=\"gotoedit\" value=\"0\">\n";
			print "<input type=\"hidden\" name=\"deleteschedule\" value=\"0\">\n";
			print $es_lang["select_schedule"]."<br>\n";
			print "<select name=\"view_schedule\">\n";
			$old_group = "";
			$old_time = "";
			foreach($schedules as $schedule) {
				if (($schedule["s_group"]!=$old_group)||($schedule["s_exptime"]!=$old_time)) {
					print "<option value=\"".$schedule["s_group"]."|".$schedule["s_exptime"]."\"";
					if (($schedule["s_group"]==$s_group)&&($schedule["s_exptime"]==$s_exptime)) print " selected";
					print ">".$schedule["s_group"]." from ".date("j M Y", $schedule["s_starttime"])." to ".date("j M Y", $schedule["s_exptime"])."</option>\n";
					$old_group = $schedule["s_group"];
					$old_time = $schedule["s_exptime"];
				}
			}
			print "</select><br>\n";
			print "<input type=\"submit\" value=\"".$es_lang["view"]."\"> <input type=\"button\" value=\"".$es_lang["edit"]."\" onclick=\"goedit();\"> ";
			print "<input type=\"button\" value=\"".$es_lang["delete"]."\" onclick=\"godelete();\"> ";
			print "<input type=\"button\" value=\"".$es_lang["new"]."\" onclick=\"window.location='es_emp_edit_schedule.php?".session_name()."=".session_id()."';\">\n";
			print "</form>\n";
			print "</td></tr></table>\n";
		}
	}
	$curj = -1;
	//-- loop through the days
	for ($j=0; $j<7; $j++) {
		$htime = mktime(0, 0, 0, 1, 1, 2003);
		//-- loop through the hours in a day
		for ($i=0; $i<96; $i++) {
			$hours = $group[$j]["s_hours"];
			//-- if the schedule has something set at this hour the show it
			if (($hours[$i]>0)||(isset($group[$j]["s_comments"][$i]))) {
				$i1 = $i;
				//-- if we are at a new day then close the table for the old day and start a new day
				if ($curj!=$j) {
					if ($curj!=-1) print "</table>\n";
					print '<br><span class="sectitle">'.$DAYS[$j]["long"].'</span><br><img src="images/bar_1.gif" width="75%" height="2"><br><table cellspacing=4 cellpadding=1>';
					$curj=$j;
				}
				print "<tr><td valign=\"top\" align=\"right\"><span class='navtitle'>".date($TIME_FORMAT, $htime)." - ";
				//-- get the positition schedule id for any assignments at this hour
				$sid=0;
				if (isset($group[$j]["s_assignments"][$i])) {
					$sid = $group[$j]["s_assignments"][$i]["pa_s_id"];
				}
				$sid2=$sid;
				//-- loop through the following hours until a change is detected, this will tell us how long the current assignment or level lasts
				while(($i<96)&&($hours[$i]==$hours[$i1])&&($sid2==$sid)) {
					$i++;
					$htime += 60*15;
					if (isset($group[$j]["s_assignments"][$i])) {
						$sid2 = $group[$j]["s_assignments"][$i]["pa_s_id"];
					}
				}
				$i2 = $i;
				//-- decrement $i so that we will be on the correct hour on the next iteration of the for loop
				$i--;
				print date($TIME_FORMAT, $htime)."</span></td><td class=\"text\" valign=\"top\" bgcolor=\"".$PRIORITY[$hours[$i1]]."\">&nbsp;</td><td class=\"text\" valign=\"top\">";
				$htime -= 60*15;
				//-- if there is an assignment out this hour then print out the position name
				if (isset($group[$j]["s_assignments"][$i1])) {
					$assignment = $group[$j]["s_assignments"][$i1];
					$position = get_position($assignment["pa_p_id"]);
					print "<b>".$position["p_name"]."</b><br>";
					//-- check the position schedul to see if the assignment is on a rotating schedule
					$pschedule = get_schedule($assignment["pa_s_id"]);
					if ($pschedule["s_repeat"]!=$REPEAT[1]) {
						print $pschedule["s_group"]."<br>\n";
						$nextdate = get_next_date($pschedule, $j);
						if ($nextdate > $pschedule["s_exptime"]) print $es_lang["finished"];
						else {
							print date("m/d/Y", $nextdate);
						}
					}
				}
				//-- there is no assignment so print out the preference level
				else {
					if ($hours[$i1] > 0) print $es_lang["pref_level"]." ".$hours[$i1]."<br>";
				}
				//-- loop through the hours for the current list item and print any courses or comments in them
				while ($i1<$i2) {
					if (isset($group[$j]["s_comments"][$i1])) {
						$comment = $group[$j]["s_comments"][$i1];
						if (!empty($comment["sc_course"])) print $es_lang["course"].": ".$comment["sc_course"]."<br>\n";
						if (!empty($comment["sc_building"])) print $es_lang["building"]." ".$comment["sc_building"]."<br>\n";
						if (!empty($comment["sc_comment"])) print $es_lang["comment"].": ".$comment["sc_comment"]."\n";
					}
					$i1++;
				}
				print "</td></tr>";
			}
			//-- create a timestamp and print out the start time
			$htime += 60*15;
		}
	}
	if ($curj!=-1) print "</table>";
	print "<br><br>";
}
if ($view!="print") {
	print '<input type="button" name="edit" value="'.$es_lang["edit_schedule"].'" onclick="window.location = \'es_emp_edit_schedule.php?s_group='.$s_group.'&s_exptime='.$s_exptime.'&'.session_name()."=".session_id().'\';">';
	print "<input type=\"button\" value=\"".$es_lang["new"]."\" onclick=\"window.location='es_emp_edit_schedule.php?create=create&".session_name()."=".session_id()."';\">\n";
}

print_footer();
?>

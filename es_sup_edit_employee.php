<?php
/*********************************************************
	File: es_sup_edit_employee.php
	Project: Employee Scheduler
	Author: John Finlay
	Revision: $Revision: 1.15 $
	Date: $Date: 2004/12/02 17:07:08 $
	Comments:
		Edit an employee's information.
		
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
print_header($es_lang["edit_employee"]);
print '<br><br><span class="pagetitle">'.$es_lang["edit_employee"].' '.$es_lang["information"].'</span><br><img src="images/bar_1.gif" width="75%" height="2">';

if (!isset($action)) $action="";
if (!isset($deletepic)) $deletepic="";
if (!isset($u_id)) $u_id = "";

//-- check for the form being submitted
if ($action=="update") {
	//-- make sure there is at least one supervisor in the list unless they are creating a supervisor
	if (!isset($u_supervisors)) {
		$u_supervisors = array();
		if ($u_newtype!="Supervisor") $u_supervisors[]=$user["u_id"];
	}
	$picture="";
	//-- check is the passwords are the same
	if (!isset($pass1)) $pass1="";
	if (!isset($pass2)) $pass2="";
	if ($pass1!=$pass2) {
		print "\n<br><font class=\"error\">".$es_lang["password_mismatch"]."</font><br>\n";
	}
	else {
		//-- check for a unique username
		$sql = "SELECT * FROM es_user WHERE u_netid='".addslashes($u_netid)."'";
		$tuser = get_db_items($sql);
		if ((count($tuser)>0)&&($u_id!=$tuser[0]["u_id"])) {
			$tuser = $tuser[0];
			$tsups = get_employee_supervisors($tuser);
			print "<br><font class=\"error\">".$es_lang["username_exists"]."  ".$es_lang["contact_users_sup"].$tsups[0]["u_name"].$es_lang["have_assigned"]."<br></font>\n";
		}
		//-- add or update the database
		else {
			if (preg_match("/[0-9a-fA-F]{6}/", $u_color)==0) $u_color = "#".dechex(rand(0,15)).dechex(rand(0,15)).dechex(rand(0,15)).dechex(rand(0,15)).dechex(rand(0,15)).dechex(rand(0,15));
			//-- if the desired hours is empty then set it to the maximum
			if (empty($u_hours)) $u_hours = $u_max;
			//-- if the u_id is empty then we are adding a new user
			if (empty($u_id)) {
				//-- create the sql statement
				$sql = "INSERT INTO es_user (u_netid, u_type, u_name, u_major, u_workphone, u_homephone, u_location, u_email, u_min, u_max, u_hours, u_supnotes, u_notes, u_color, u_language";
				if ((!empty($picture))||($deletepic)) $sql .= ", u_picture";
				if (!empty($pass1)) $sql .= ", u_password";
				$sql .= ") VALUES ('".addslashes($u_netid)."', 
					'".addslashes($u_newtype)."', 
					'".addslashes($u_name)."', 
					'".addslashes($u_major)."',
					'".addslashes($u_workphone)."',
					'".addslashes($u_homephone)."',
					'".addslashes($u_location)."',
					'".addslashes($u_email)."',
					'".addslashes($u_min)."',
					'".addslashes($u_max)."',
					'".addslashes($u_hours)."',
					'".addslashes($u_supnotes)."',
					'".addslashes($u_notes)."',
					'".addslashes($u_color)."',
					'".addslashes($u_language)."'";
				if ((!empty($picture))||($deletepic)) $sql .= ", '".addslashes($picture)."'";
				if (!empty($pass1)) $sql .= ", '".addslashes(crypt($pass1))."'";
				$sql .= ")";
				$res = dbquery($sql);
				$u_id = mysql_insert_id();
				//-- handle the file upload if a picture was sent
				if (($_FILES["u_picture"]["error"]==0)&&(!empty($_FILES["u_picture"]["tmp_name"]))) {
					move_uploaded_file($_FILES["u_picture"]["tmp_name"], "./photos/".$u_id."-".$_FILES["u_picture"]["name"]);
					$picture = $u_id."-".$_FILES["u_picture"]["name"];
					$sql = "UPDATE es_user SET u_picture='".addslashes($picture)."' WHERE u_id=$u_id";
					$res = dbquery($sql);
				}
				//-- update the users supervisors in the list
				foreach($u_supervisors as $sup_id) {	
					$sql = "INSERT INTO es_user_sups VALUES (NULL, ".$sup_id.", $u_id)";
					$res = dbquery($sql);
				}
			}
			//-- update the user in the database
			else {
				//-- get the users information
				$employee = get_user($u_id);
				//-- don't allow changing the type if the employee is an admin and the supervisor is not an admin
				if (strstr($user["u_type"], "Admin")===false) {
					if (strstr($employee["u_type"], "Admin")!==false) $u_newtype = $employee["u_type"];
				}
				//-- if we are deleting a picture then we need to delete it from the photos directory
				if ($deletepic) {
					if ((!empty($employee["u_picture"]))&&(file_exists("./photos/".$employee["u_picture"]))) unlink("./photos/".$employee["u_picture"]);
				}
				//-- check if a valid file was uploaded without errors and delete any old pictures before updating with the new one
				if (is_array($_FILES["u_picture"])) {
					if ((empty($_FILES["u_picture"]["error"]))&&(!empty($_FILES["u_picture"]["tmp_name"]))) {
						if ((!empty($employee["u_picture"]))&&(file_exists("./photos/".$employee["u_picture"]))) unlink("./photos/".$employee["u_picture"]);
						move_uploaded_file($_FILES["u_picture"]["tmp_name"], "./photos/".$employee["u_id"]."-".$_FILES["u_picture"]["name"]);
						$picture = $employee["u_id"]."-".$_FILES["u_picture"]["name"];
					}
				}
				//-- build the sql statement
				$sql = "UPDATE es_user SET 
					u_netid='".addslashes($u_netid)."', 
					u_type='".addslashes($u_newtype)."', 
					u_name='".addslashes($u_name)."', 
					u_major='".addslashes($u_major)."',
					u_workphone='".addslashes($u_workphone)."',
					u_homephone='".addslashes($u_homephone)."',
					u_location='".addslashes($u_location)."',
					u_email='".addslashes($u_email)."',
					u_min='".addslashes($u_min)."',
					u_max='".addslashes($u_max)."',
					u_hours='".addslashes($u_hours)."',
					u_supnotes='".addslashes($u_supnotes)."',
					u_color='".addslashes($u_color)."',
					u_notes='".addslashes($u_notes)."',
					u_language='".addslashes($u_language)."'";
				if ((!empty($picture))||($deletepic)) $sql .= ", u_picture='".addslashes($picture)."'";
				if (!empty($pass1)) $sql .= ", u_password='".addslashes(crypt($pass1))."'";
				$sql .= " WHERE u_id=$u_id";
				$res = dbquery($sql);
				//-- delete any old supervisor links and then create the new ones
				$sql = "DELETE FROM es_user_sups WHERE us_emp_id=$u_id";
				$res = dbquery($sql);
				foreach($u_supervisors as $sup_id) {	
					$sql = "INSERT INTO es_user_sups VALUES (NULL, ".$sup_id.", $u_id)";
					$res = dbquery($sql);
				}
			}
		}
	}
}	//-- end update action

//-- if the u_id is empty we are creating a new user, if not then get the information for the old user we are editing
if (!empty($u_id)) {
	$employee = get_user($u_id, true);
	if (empty($employee["u_color"])) $employee["u_color"] = "#".dechex(rand(0,15)).dechex(rand(0,15)).dechex(rand(0,15)).dechex(rand(0,15)).dechex(rand(0,15)).dechex(rand(0,15));
	print "<br />When editing a user leave the password fields blank to keep the old password.<br />\n";
}
else {
	if (empty($u_newtype)) $u_newtype="Employee";
	$employee = array();
	$employee["u_min"]=10;
	$employee["u_max"]=20;
	$employee["u_type"]=$u_newtype;
	$employee["u_netid"]="";
	$employee["u_name"]="";
	$employee["u_major"]="";
	$employee["u_workphone"]="";
	$employee["u_homephone"]="";
	$employee["u_location"] = "";
	$employee["u_email"] = "";
	$employee["u_hours"] = "";
	$employee["u_notes"] = "";
	$employee["u_supnotes"] = "";
	$employee["u_language"] = "";
	$employee["u_color"] = "#".dechex(rand(0,15)).dechex(rand(0,15)).dechex(rand(0,15)).dechex(rand(0,15)).dechex(rand(0,15)).dechex(rand(0,15));
}
?>
<SCRIPT LANGUAGE="JavaScript" SRC="ColorSelector.js"></SCRIPT>
<script language="JavaScript">
	//-- the following function will display the fields that allows adminsitrators to enter passwords
	function show_password(type) {
		row1 = document.getElementById("passrow1");
		row2 = document.getElementById("passrow2");
		if (type.selectedIndex==0 || type.selectedIndex==2 || type.selectedIndex==4) {
			row1.style.display="block";
			row2.style.display="block";
		}
		else {
			row1.style.display="none";
			row2.style.display="none";
		}
	}
	
	function checkform(frm) {
		if (frm.u_netid.value=="") {
			alert('<?php print $es_lang["username_required"];?>');
			frm.u_netid.focus();
			return false;
		}
		if (frm.u_name.value=="") {
			alert('<?php print $es_lang["name_required"];?>');
			frm.u_name.focus();
			return false;
		}
		return true;
	}
	
	function change_background(colorbox) {
		colorbox.style.backgroundColor = colorbox.value;
	}
</script>
<form method="post" name="userform" enctype="multipart/form-data" onsubmit="return checkform(this);">
<input type="hidden" name="u_id" value="<?php print $u_id?>">
<input type="hidden" name="action" value="update">
<table>
<tr><td align="right" class="text"><?php print $es_lang["username"];?></td><td><input type="text" name="u_netid" value="<?php print $employee["u_netid"]?>" maxlength="30"></td>
<td rowspan="9">
<?php if (!empty($employee["u_picture"])) print '<img align="right" src="photos/'.$employee["u_picture"].'" height="200">'; ?>
</td></tr>
<tr><td align="right" class="text"><?php print $es_lang["employee_type"];?></td><td><select name="u_newtype" onchange="show_password(this);">
	<option value="Employee" <?php if ($employee["u_type"]=="Employee") print "selected";?>><?php print $es_lang["employee"];?></option>
	<option value="LDAP Employee" <?php if ($employee["u_type"]=="LDAP Employee") print "selected";?>><?php print $es_lang["ldap_employee"];?></option>
	<option value="Supervisor" <?php if ($employee["u_type"]=="Supervisor") print "selected";?>><?php print $es_lang["supervisor"];?></option>
	<option value="LDAP Supervisor" <?php if ($employee["u_type"]=="LDAP Supervisor") print "selected";?>><?php print $es_lang["ldap_supervisor"];?></option>
<?php if (strstr($user["u_type"],"Admin")!==false) { ?>
	<option value="Admin" <?php if ($employee["u_type"]=="Admin") print "selected";?>><?php print $es_lang["admin"];?></option>
	<option value="LDAP Admin" <?php if ($employee["u_type"]=="LDAP Admin") print "selected";?>><?php print $es_lang["ldap_admin"];?></option>
<?php } ?>
</select>
</td></tr>
<?php
$disp="none";
if (preg_match("/LDAP/", $employee["u_type"])==0) {
	$disp="block";
}
print '<tr id="passrow1" style="display: '.$disp.'"><td align="right" class="text">'.$es_lang["password"].'</td><td><input type="password" name="pass1"></td></tr>';
print '<tr id="passrow2" style="display: '.$disp.'"><td align="right" class="text">'.$es_lang["confirm_password"].'</td><td><input type="password" name="pass2"></td></tr>';
?>
<tr><td align="right" class="text"><?php print $es_lang["full_name"]; ?></td><td><input type="text" name="u_name" value="<?php print $employee["u_name"]?>"></td></tr>
<tr><td align="right" class="text"><?php print $es_lang["major"]; ?></td><td><input type="text" name="u_major" value="<?php print $employee["u_major"]?>"></td></tr>
<tr><td align="right" class="text"><?php print $es_lang["work_phone"]; ?></td><td><input type="text" name="u_workphone" value="<?php print $employee["u_workphone"]?>"></td></tr>
<tr><td align="right" class="text"><?php print $es_lang["home_phone"]; ?></td><td><input type="text" name="u_homephone" value="<?php print $employee["u_homephone"]?>"></td></tr>
<tr><td align="right" class="text"><?php print $es_lang["location"]; ?></td><td><input type="text" name="u_location" value="<?php print $employee["u_location"]?>"></td></tr>
<tr><td align="right" class="text"><?php print $es_lang["email"]; ?></td><td><input type="text" name="u_email" value="<?php print $employee["u_email"]?>"></td></tr>
<tr><td align="right" class="text"><?php print $es_lang["minimum_hours"]; ?></td><td><input type="text" name="u_min" value="<?php print $employee["u_min"]?>" size="5"></td></tr>
<tr><td align="right" class="text"><?php print $es_lang["maximum_hours"]; ?></td><td><input type="text" name="u_max" value="<?php print $employee["u_max"]?>" size="5"></td></tr>
<tr><td align="right" class="text"><?php print $es_lang["desired_hours"]; ?></td><td><input type="text" name="u_hours" value="<?php print $employee["u_hours"]?>" size="5"></td></tr>
<tr><td align="right" class="text"><?php print $es_lang["user_color"]; ?></td><td><input type="text" name="u_color" value="<?php print $employee["u_color"]?>" size="10" maxlength="7" style="background-color: <?php print $employee["u_color"]?>;" onchange="change_background(this);">
	<script language="JavaScript" type="text/javascript">selector = new ColorSelector(document.userform.u_color, true); selector.writeSelector();</script>
	<a href="#" onclick="selector.show(); return false;"><img src="images/es_color.png" alt="Choose Color" border="0" width="30" height="30"></a></td></tr>
<tr><td align="right" class="text"><?php print $es_lang["preferred_language"]; ?></td><td><select name="u_language">
<?php
foreach($es_language as $language=>$langfile) {
	print "<option value=\"".$language."\"";
	if ($employee["u_language"]==$language) print " selected=\"selected\"";
	print ">".$es_lang[$language]."</option>\n";
}
?>
</select>
</td></tr>
<tr><td align="right" class="text" valign="top"><?php print $es_lang["notes"]; ?></td><td colspan="2"><textarea name="u_notes" rows=5 cols=50><?php print $employee["u_notes"]?></textarea></td></tr>
<tr><td align="right" class="text" valign="top"><?php print $es_lang["sup_notes"]; ?></td><td colspan="2"><textarea name="u_supnotes" rows=5 cols=50><?php print $employee["u_supnotes"]?></textarea></td></tr>
<tr><td colspan="3"><br></td></tr>
<tr><td align="right" class="text" valign="top"><?php print $es_lang["upload_picture"]; ?></td><td colspan="2" class="text"><input type="hidden" name="MAX_FILE_SIZE" value="150000"><input type="file" name="u_picture"><br>
<?php if (!empty($employee["u_picture"])) print '<input type="checkbox" name="deletepic" value="1"> Delete Picture'; ?>
</td></tr>
<tr><td colspan="3"><br></td></tr>
<tr><td align="right" class="text" valign="top"><?php print $es_lang["supervisors"]; ?></td><td colspan="2"><select name="u_supervisors[]" size="10" multiple>
<?php
	//-- get the supervisors for the supervisor selection list
	$sups = get_employee_supervisors($employee);
	$supervisors = get_supervisors();
	foreach($supervisors as $supervisor) {
		print "<option value=\"".$supervisor["u_id"]."\"";
		//-- compare each supervisor with the user's supervisor list to see if their is a match
		foreach($sups as $sup) {
			if ($sup["u_id"]==$supervisor["u_id"]) {
				print " selected";
				break;
			}
		}
		print ">".$supervisor["u_name"]."</option>\n";
	}
?>
</select>
</td></tr>
<tr><td colspan="3"><br></td></tr>
<tr><td></td><td><input type="submit" value="<?php print $es_lang["update"]; ?>"></td></tr>
</table>
</form>
<?php
	
print_footer();
?>

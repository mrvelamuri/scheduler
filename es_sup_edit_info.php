<?php
/*********************************************************
	File: es_sup_edit_info.php
	Project: Employee Scheduler
	Author: John Finlay
	Comments:
		Edit a supervisor's information.
		
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
if (!empty($u_language)) {
	if ($user["u_language"]!=$u_language) {
		$_SESSION["CLANGUAGE"] = $u_language;
		$LANGUAGE = $u_language;
		if (isset($es_language[$LANGUAGE])) require($es_language[$LANGUAGE]);
	}
}

if (!isset($action)) $action="";
if (!isset($deletepic)) $deletepic="";

if ($action=="update") {
	$picture="";
	if ($deletepic) if ((!empty($user["u_picture"]))&&(file_exists("./photos/".$user["u_picture"]))) unlink("./photos/".$user["u_picture"]);
	if (empty($_FILES["u_picture"]["error"])) {
		if ((!empty($user["u_picture"]))&&(file_exists("./photos/".$user["u_picture"]))) unlink("./photos/".$user["u_picture"]);
		move_uploaded_file($_FILES["u_picture"]["tmp_name"], "./photos/".$user["u_id"]."-".$_FILES["u_picture"]["name"]);
		$picture = $user["u_id"]."-".$_FILES["u_picture"]["name"];
	}
	$sql = "UPDATE es_user SET 
		u_name='".addslashes($u_name)."', 
		u_major='".addslashes($u_major)."',
		u_workphone='".addslashes($u_workphone)."',
		u_homephone='".addslashes($u_homephone)."',
		u_location='".addslashes($u_location)."',
		u_email='".addslashes($u_email)."',
		u_notes='".addslashes($u_notes)."',
		u_language='".addslashes($u_language)."'";
	if ((!empty($picture))||($deletepic)) $sql .= ", u_picture='".addslashes($picture)."'";
	if (!empty($pass1)) {
		if ($pass1==$pass2) $sql .= ", u_password='".addslashes(crypt($pass1))."'";
	}
	$sql .= " WHERE u_id=".$user["u_id"];
	$res = dbquery($sql);
	$sql = "DELETE FROM es_user_sups WHERE us_emp_id=".$user["u_id"];
	$res = dbquery($sql);
	if (empty($u_supervisors)) $u_supervisors = array();
	foreach($u_supervisors as $sup_id) {
		$sql = "INSERT INTO es_user_sups VALUES (NULL, ".$sup_id.", ".$user["u_id"].")";
		$res = dbquery($sql);
	}
	$user = get_user($user["u_id"]);
}
print_header($es_lang["my_info"]);

print '<br><br><span class="pagetitle">'.$es_lang["my_info"].'</span><br><img src="images/bar_1.gif" width="75%" height="2">';
?>
<form method="post" enctype="multipart/form-data">
<input type="hidden" name="action" value="update">
<table>
<tr><td align="right" class="text"><?php print $es_lang["username"]; ?></td><td class="text"><?php print $user["u_netid"]?></td>
<td rowspan="7">
<?php if (!empty($user["u_picture"])) print '<img align="right" src="photos/'.$user["u_picture"].'" height="200">'; ?>
</td></tr>
<?php
if (preg_match("/LDAP/", $user["u_type"])>0) {
	print '<tr><td align="right" class="text">'.$es_lang["password"].'</td><td><input type="password" name="pass1"></td></tr>';
	print '<tr><td align="right" class="text">'.$es_lang["confirm_password"].':</td><td><input type="password" name="pass1"></td></tr>';
}
?>
<tr><td align="right" class="text"><?php print $es_lang["full_name"]; ?></td><td><input type="text" name="u_name" value="<?php print $user["u_name"]?>"></td></tr>
<tr><td align="right" class="text"><?php print $es_lang["major"]; ?></td><td><input type="text" name="u_major" value="<?php print $user["u_major"]?>"></td></tr>
<tr><td align="right" class="text"><?php print $es_lang["work_phone"]; ?></td><td><input type="text" name="u_workphone" value="<?php print $user["u_workphone"]?>"></td></tr>
<tr><td align="right" class="text"><?php print $es_lang["home_phone"]; ?></td><td><input type="text" name="u_homephone" value="<?php print $user["u_homephone"]?>"></td></tr>
<tr><td align="right" class="text"><?php print $es_lang["location"]; ?></td><td><input type="text" name="u_location" value="<?php print $user["u_location"]?>"></td></tr>
<tr><td align="right" class="text"><?php print $es_lang["email"]; ?></td><td><input type="text" name="u_email" value="<?php print $user["u_email"]?>"></td></tr>
<tr><td align="right" class="text"><?php print $es_lang["preferred_language"]; ?></td><td><select name="u_language">
<?php
foreach($es_language as $language=>$langfile) {
	print "<option value=\"".$language."\"";
	if ($user["u_language"]==$language) print " selected=\"selected\"";
	print ">".$es_lang[$language]."</option>\n";
}
?>
</select>
</td></tr>
<tr><td align="right" class="text" valign="top"><?php print $es_lang["notes"]; ?></td><td colspan="2"><textarea name="u_notes" rows=5 cols=50><?php print $user["u_notes"]?></textarea></td></tr>
<tr><td colspan="3"><br></td></tr>
<tr><td align="right" class="text" valign="top"><?php print $es_lang["upload_picture"]; ?></td><td colspan="2" class="text"><input type="hidden" name="MAX_FILE_SIZE" value="150000"><input type="file" name="u_picture"><br>
<?php if (!empty($user["u_picture"])) print '<input type="checkbox" name="deletepic" value="1"> '.$es_lang["delete_pic"]; ?>
</td></tr>
<tr><td colspan="3"><br></td></tr>
<tr><td align="right" class="text" valign="top"><?php print $es_lang["supervisors"]; ?></td><td colspan="2"><select name="u_supervisors[]" size="5" multiple>
<?php
	$sups = get_employee_supervisors($user);
	$supervisors = get_supervisors();
	foreach($supervisors as $supervisor) {
		print "<option value=\"".$supervisor["u_id"]."\"";
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

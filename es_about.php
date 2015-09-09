<?php
/*********************************************************
	File: es_sup_employee_schedule.php
	Project: Employee Scheduler
	Author: John Finlay
	Revision: $Revision: 1.3 $
	Date: $Date: 2004/05/17 16:16:09 $
	Comments:
		Shows the employee's schedule to supervisors.
	
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

print_header("Employee Scheduler Login");
?>
	<center>
	<br><br><span class="pagetitle">About Employee Scheduler</span><br>
	<img src="images/bar_1.gif" width="80%" height="2">
	<br><br>
	<table width="60%"><tr><td>
	<span class="text">
	The Employee Scheduler was created for the <a href="http://www.byu.edu/">Brigham Young University</a> <a href="http://www.lib.byu.edu/">Harold B. Lee Library</a> to help supervisors schedule their student employees. Supervisors may setup areas, positions, and employees.  Employees then login to the system and enter in their available schedule.  From their employees' available schedules, supervisors can then schedule them to work certain positions.<br><br>
	To learn more about the Employee Scheduler visit <a href="http://empscheduler.sourceforge.net/" target="_blank">http://empscheduler.sourceforge.net/</a>
	</center>
<?php
print_footer();
?>
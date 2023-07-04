<?php

namespace Settings;

class AppSettings{


	public static function sqlcon(){
		$conn1 = mysqli_connect(AppSettings::LOAD_INI('SQLCON','server'), AppSettings::LOAD_INI('SQLCON','username'), AppSettings::LOAD_INI('SQLCON','password'));
	
		if(!$conn1){
			die("Connection Failed:" . mysqli_connect_error($conn1));
		}

		return $conn1;
	}

	public static function oracon(){
		$prodconn = oci_connect(AppSettings::LOAD_INI('ORACON','username'),AppSettings::LOAD_INI('ORACON','password'), AppSettings::LOAD_INI('ORACON','server'));

		if(!$prodconn){
			die("Connecting to Oracle Database Failed");
		}

		return $prodconn;
	}

	public static function LOAD_INI($VAR_GROUP,$VAR_NAME){

		$ini_array = parse_ini_file(getcwd()."/app.ini", true /* will scope sectionally */);
		return $ini_array[$VAR_GROUP][$VAR_NAME];
	
	}
	
}
?>
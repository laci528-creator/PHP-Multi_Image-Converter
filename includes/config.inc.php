<?php 
define("TESTBETRIEB",true);



if(TESTBETRIEB) {
	error_reporting(E_ALL);
	ini_set("display_error",1);
}
else {
	error_reporting(E_ALL);
	ini_set("display_errors",0);
}
?>
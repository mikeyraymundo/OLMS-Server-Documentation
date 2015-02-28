<?php

/**
 *	Allows user to logout of the current session
 *
 *	session_destroy() destroys all of the data associated with the current session. It does not 
 *	unset any of the global variables associated with the session, or unset the session cookie.
 *	to use the session variables again, session_start() has to be called. 
 *
 *	@var resource
 */

session_start();
$_SESSION=array();
session_destroy();

header("Location:login.php");

?>
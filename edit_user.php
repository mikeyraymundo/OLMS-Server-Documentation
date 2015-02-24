
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
	<title>OLMS</title>
	<!-- start:bootstrap v3.2.0 -->
	<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <!-- start:font awesome v4.1.0 
    <link rel="stylesheet" type="text/css" href="css/font-awesome.min.css"> -->
    <link href="http://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
    <!-- start:bootstrap reset -->
    <link rel="stylesheet" type="text/css" href="css/bootstrap-reset.css">
	<!-- start:style arjuna -->
	<link rel="stylesheet" type="text/css" href="css/arjuna.css">
	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->


</head>
<?php
	//CREATE DB CONNECTION
	include_once("db_query.php");
	db_connection();
 mysql_select_db("wts_olms", $db);
	session_start();

	
		
	
	
$update_id=$_SESSION['update_id'];
if(isset($_POST['intent']))
	{
		extract($_POST);
		if($intent == "edit")
		{
			edit_account($update_id, $fullname, $username, $password,$password2, $account_type, $original_name);
		}
	}
	$searchquery="SELECT * FROM account WHERE user_id='$update_id'";
	$result=mysql_query($searchquery);
	$row=mysql_fetch_row($result);
	$account_type=$row[4];

	
?>

<body>
    <!-- start:wrapper -->
    <?php include "navbar.php"; ?>
    <div id="wrapper">
   		<div id="page-wrapper">
    		<div class="row">
    			<div class="col-lg-12">
    				<h1 class="page-header">Update User Account</h1>
    			</div>
    		</div>
    		<div class="row">
    			<div class="col-lg-12">
    				<div class="panel panel-primary">
    					<div class="panel-heading"></div>
    					<div class="panel-body">
    						<div class="row">
    							<div class="col-lg-12">
							    	<form role="form" action="edit_user.php" method="POST">
							    		<input type="hidden" name="intent" value="edit">  
							    		<input type="hidden" name="original_name" value="<?php echo $row[1]; ?>">  

									  	<div class="form-group">
									  		<label>Full Name</label>
									    		<input type="text" class="form-control" id="fullName" name="fullname" value="<?php echo $row[3]; ?>" required="required">
									 
		  								</div>
									  	<div class="form-group">
							                <label>Type</label>
						                        <select class="form-control" name="account_type" value="<?php echo $row[4]; ?>">
						                            <option value="Agent" >Agent</option>
						                            <option value="Administator" <?php if($row[4]!="Agent"){echo 'selected="selected"';}else{}?>>Administrator</option>     
						                        </select>
            							</div>
							            <div class="form-group">
									  		<label>Username</label>
									    		<input type="text" class="form-control" id="username" name="username" value="<?php echo $row[1]; ?>" required="required">
								   		 	
									  	</div>		  	
							            <div class="form-group">
									  		<label>Password</label>
									    		<input type="password" class="form-control" id="password" name="password" value="<?php echo $row[2]; ?>" required="required">
								   		 	
									  	</div>
								  	 	<div class="form-group">
									  		<label>Confirm Password</label>
									    		<input type="password" class="form-control" id="password2" name="password2" value="<?php echo $row[2]; ?>" required="required">
								   		 	
									  	</div>  
									  	<button type="submit" class="btn btn-primary">
									  		Update
									  	</button>
									</form>
    							</div>
    						</div>
    					</div>
    				</div>
    			</div>
    		</div>
    	</div>
    </div>
    <!-- end:wrapper -->	    
	
	<!-- start:javascript for all pages -->
		<!-- jQuery -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="js/plugins/metisMenu/metisMenu.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="js/sb-admin-2.js"></script>
	<!-- end:javascript for all pages-->

</body>
</html>
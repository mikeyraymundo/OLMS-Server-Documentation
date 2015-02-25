
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

/**
*	This module allows the user of the system to choose a campaign from the search query 
*	according to campaign id. The update_campaign function is called.
*	
*	$searchquery gets the value searched in campaign table according to campaign id
*
*	@var resource
*	
*/

	//CREATE DB CONNECTION
	include_once("db_query.php");
	db_connection();

	session_start();

	if (isset($_POST['update_campaign_id']))
	{
		$update_campaign_id=$_POST['update_campaign_id'];
	}
	if (isset($_GET['update_campaign_id']))
	{
		$update_campaign_id=$_GET['update_campaign_id'];
	}
	$searchquery="Select*From campaign where campaign_id='$update_campaign_id'";
	$result=mysql_query($searchquery);
	$row=mysql_fetch_row($result);

	if(isset($_POST['intent']))
	{
		extract($_POST);
		if($intent == "update_campaign")
		{
			update_campaign($update_campaign_id, $campaign_name, $company, $creation_date, $date_ended);
		}
	}
?>

<body>
    <!-- start:wrapper -->
    <?php include "navbar.php"; ?>
    <div id="wrapper">
   		<div id="page-wrapper">
    		<div class="row">
    			<div class="col-lg-12">
    				<h1 class="page-header">Update Campaign</h1>
    			</div>
    		</div>
    		<div class="row">
    			<div class="col-lg-12">
    				<div class="panel panel-primary">
    					<div class="panel-heading"></div>
    					<div class="panel-body">
    						<div class="row">
    							<div class="col-lg-12">
							    	<form role="form" action="update_campaign.php" method="POST" onsubmit="return confirm('Are you sure about updating this campaign?');">
							    		<input type="hidden" name="intent" value="update_campaign">  
							    		<input type="hidden" name="update_campaign_id" value="<?php echo $update_campaign_id;?>">  	
									  	<div class="form-group">
									  		<label>Campaign Name</label>
								    		<input type="text" class="form-control" id="campaign_name" name="campaign_name" value="<?php echo $row[1]; ?>" required="required">
		  								</div>
									  	<div class="form-group">
							                <label>Company</label>
					                       	<input type="text" class="form-control" id="company" name="company" value="<?php echo $row[2]; ?>" required="required">
            							</div>
							            <div class="form-group">
									  		<label>Date Created</label>
									    	<input type='date' class="form-control" id="creation_date" name="creation_date" value="<?php echo $row[3]; ?>" required="required">		
									  	</div>		  	
							            <div class="form-group">
									  		<label>Date Ended</label>
									    	<input type='date' class="form-control" id="date_ended" name="date_ended" value="<?php echo $row[4]; ?>">
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
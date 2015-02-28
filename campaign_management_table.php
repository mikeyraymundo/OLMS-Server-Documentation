<?php

/**
 *	This function allows the system to display the table for campaign_management.php that
 *	can be refreshed according to the results of the queries in campaign_management.php.
 *  This table uses the function db_connection() from the file db_query.php.
 *  It uses the special MySQL function mysql_select_db that selects the database to be used.
 * 
 *  $selected_campaign is a variable that contains the value of selected_campaign
 *  $radio_selection is a variable that contains the valueof radio_selection.
 *  $query contains the query to be executed in the MySQL database.
 *  $result contains the special MySQL function mysql_query that runs $query.
 *  $row contains the special MySQL function mysql_fetch_row that stores $result in an aray.
 *  $name stores all of the data in the first column of the array(Agents)
 *  $user_id stores all of the data in the second column of the array(Agents)
 *  $company_name stores all of the data in the first column of the array(Leads)
 *  $primary_industry stores all of the data in the second column of the array(Leads)
 *  $lead_id stores all of the data in the third column of the array(Leads)
 *  $status_id stores all of the data in the second column of the array(Status)
 * 
 *  @var resource
 */
include_once("db_query.php");
db_connection();
        //SELECT DB
        mysql_select_db("wts_olms", $db);
    $selected_campaign=$_POST['selected_campaign'];
    $radio_selection=$_POST['radio_selection'];
$table="";
if($radio_selection=="Agents")
{
$table .= <<<EOT
    <form role="form" action="campaign_management.php" method="POST" onsubmit="return confirm('Are you sure you want to remove these agents from the campaign?');">
        <table class="table table-striped table-bordered table-hover" id="agent-table">
        <div style="display:none" id="hiddencontainer"></div>
            <input type="hidden" id="intent" name="intent" value="unassign_agents">
            <input type="hidden" id="campaign_id" name="campaign_id" value="$selected_campaign">
            <thead>
                <tr>
                    <th class="hidden-phone"><input type="checkbox" id="allcb" />ALL</th>
                    <th class="hidden-phone">Agent Name</th>
                </tr>
            </thead>
            <tbody>
EOT;
$query = "SELECT name, a.user_id  FROM account a LEFT JOIN agent_list l ON a.user_id=l.user_id WHERE l.campaign_id='$selected_campaign' ; ";
$result = mysql_query($query);
    while($row = mysql_fetch_row($result))
    {      
        $table .= "<tr>";
        $name = $row[0];
        $user_id=$row[1];                
        $table .= <<< EOT
                    <td><input type='checkbox' name='checkbox[]' value="$user_id"></td>
                    <td>$name</td>     
EOT;

    }
$table .= <<< EOT
            </tbody>
        </table>
    <button type="submit" class="btn btn-outline btn-warning">
        Unassign Agents
    </button">
    </form>

<script src="js/plugins/dataTables/jquery.dataTables.js"></script>
<script src="js/plugins/dataTables/dataTables.bootstrap.js"></script> 
<script  src="js/dataTables.fnGetHiddenNodes.js"></script>
<script  src="js/dataTables.fnGetFilteredNodes.js"></script>
<script>

    $(document).ready(function() {
        $('table').dataTable( {
            paginate: true,
            scrolly: 300,
            iDisplayStart: 0,
            iDisplayLength: 100,
        });
        
    });
$(document).ready(function() {
   oTable = $('table').dataTable();
    
   $('form').submit(function(){

      $(oTable.fnGetHiddenNodes()).find('input:checked').appendTo('#hiddencontainer');
   });
 
});
$('#allcb').click( function() { //this is the function that will mark all your checkboxes when the input with the .checkall class is clicked
   oTable = $('table').dataTable();
   
    $('input', oTable.fnGetFilteredNodes()).prop('checked',this.checked); //note it's calling fnGetFilteredNodes() - this is so it will mark all nodes whether they are filtered or not
} );
</script>
EOT;
echo $table;
}
else if ($radio_selection=="Leads") 
{
    $table.=<<<EOT
        <form role="form" action="campaign_management.php" method="POST" onsubmit="return confirm('Are you sure you want to remove these leads?');">
        <table class="table table-striped table-bordered table-hover" id="lead-table">
        <div style="display:none" id="hiddencontainer"></div>
        <input type="hidden" id="intent" name="intent" value="unassign_leads">
        <input type="hidden" id="campaign_id" name="campaign_id" value="$selected_campaign">
            <thead>
                <tr>
                    <th class="hidden-phone" style="width: 15%"><input type="checkbox" id="allcb" />ALL</th>
                    <th class="hidden-phone" style="width: 15%">Company Name</th>
                    <th class="hidden-phone" style="width: 15%"> Industry</th> 
                </tr>
            </thead>
            <tbody>
EOT;
$query = "SELECT company_name, primary_industry, l.lead_id  FROM lead l LEFT JOIN campaign_lead c ON l.lead_id=c.lead_id WHERE c.campaign_id='$selected_campaign' ; ";
$result = mysql_query($query);
    while($row = mysql_fetch_row($result))
    {      
        $table .= "<tr>";
        $company_name = $row[0];
        $primary_industry = $row[1];
        $lead_id=$row[2];     
        $table .= <<< EOT
                <td><input type='checkbox' name='checkbox[]' value="$lead_id"></td>
                <td>$company_name </td>
                <td>$primary_industry </td>
EOT;
    }
$table .= <<< EOT
            </tbody>
        </table>
    <button type="submit" class="btn btn-outline btn-warning">
        Unassign Leads
    </button>
    </form>
<script src="js/plugins/dataTables/jquery.dataTables.js"></script>
<script src="js/plugins/dataTables/dataTables.bootstrap.js"></script> 
<script  src="js/dataTables.fnGetHiddenNodes.js"></script>
<script  src="js/dataTables.fnGetFilteredNodes.js"></script>
<script>

    $(document).ready(function() {
        $('table').dataTable( {
            paginate: true,
            scrolly: 300,
            iDisplayStart: 0,
            iDisplayLength: 100,
        });
        
    });
$(document).ready(function() {
   oTable = $('table').dataTable();
    
   $('form').submit(function(){

      $(oTable.fnGetHiddenNodes()).find('input:checked').appendTo('#hiddencontainer');
   });
 
});
$('#allcb').click( function() { //this is the function that will mark all your checkboxes when the input with the .checkall class is clicked
   oTable = $('table').dataTable();
   
    $('input', oTable.fnGetFilteredNodes()).prop('checked',this.checked); //note it's calling fnGetFilteredNodes() - this is so it will mark all nodes whether they are filtered or not
} );
</script>
EOT;

echo $table;
}
else if($radio_selection=="Assign_Agents")
{
    $table .= <<<EOT
<form role="form" action="campaign_management.php" method="POST" onsubmit="return confirm('Are you sure you want to assign these agents to the campaign?');">
    <table class="table table-striped table-bordered table-hover" id="agent-table">
    <div style="display:none" id="hiddencontainer"></div>
    <input type="hidden" id="intent" name="intent" value="assign_agents">
    <input type="hidden" id="campaign_id" name="campaign_id" value="$selected_campaign">
        <thead>
            <tr>
                <th class="hidden-phone" style="width: 15%"><input type="checkbox" id="allcb" />ALL</th>
                <th class="hidden-phone" style="width: 15%">Agent Name</th>
            </tr>
        </thead>
        <tbody>
EOT;
$query = "SELECT name, a.user_id  FROM account a LEFT JOIN agent_list l ON a.user_id=l.user_id AND l.campaign_id='$selected_campaign' WHERE l.user_id IS NULL AND account_type='Agent' ; ";
$result = mysql_query($query);
    while($row = mysql_fetch_row($result))
    {      
        $table .= "<tr>";
        $name = $row[0];
        $user_id=$row[1];       
        $table .= <<< EOT
        <td><input type='checkbox' name='checkbox[]' value="$user_id"></td>
        <td>$name </td>
EOT;

    }

$table .= <<< EOT
        </tbody>
    </table>
    <button type="submit" class="btn btn-outline btn-warning">
       Assign Agents
    </button>
</form>
<script src="js/plugins/dataTables/jquery.dataTables.js"></script>
<script src="js/plugins/dataTables/dataTables.bootstrap.js"></script> 
<script  src="js/dataTables.fnGetHiddenNodes.js"></script>
<script  src="js/dataTables.fnGetFilteredNodes.js"></script>
<script>

    $(document).ready(function() {
        $('table').dataTable( {
            paginate: true,
            scrolly: 300,
            iDisplayStart: 0,
            iDisplayLength: 100,
        });
        
    });
$(document).ready(function() {
   oTable = $('table').dataTable();
    
   $('form').submit(function(){

      $(oTable.fnGetHiddenNodes()).find('input:checked').appendTo('#hiddencontainer');
   });
 
});
$('#allcb').click( function() { //this is the function that will mark all your checkboxes when the input with the .checkall class is clicked
   oTable = $('table').dataTable();
   
    $('input', oTable.fnGetFilteredNodes()).prop('checked',this.checked); //note it's calling fnGetFilteredNodes() - this is so it will mark all nodes whether they are filtered or not
} );
</script>
                   
EOT;
echo $table;
}
else if($radio_selection=="Status")
{
    $table .= <<<EOT
<form role="form" action="campaign_management.php" method="POST" onsubmit="return confirm('Are you sure you want to assign these statuses to the campaign?');">
    <table class="table table-striped table-bordered table-hover" id="status-table">
    <div style="display:none" id="hiddencontainer"></div>
    <input type="hidden" id="intent" name="intent" value="assign_status">
    <input type="hidden" id="campaign_id" name="campaign_id" value="$selected_campaign">
        <thead>
            <tr>
                <th class="hidden-phone" style="width: 15%"><input type="checkbox" id="allcb" />ALL</th>
                <th class="hidden-phone" style="width: 15%">Status Name</th>
            </tr>
        </thead>
        <tbody>
EOT;
$query = "SELECT status_name, s.status_id  FROM status s LEFT JOIN campaign_status c ON s.status_id=c.status_id AND c.campaign_id='$selected_campaign' WHERE c.status_id IS NULL ; ";
$result = mysql_query($query);
    while($row = mysql_fetch_row($result))
    {
        $table .= "<tr>";
        $name = $row[0];
        $status_id=$row[1];
        $table .= <<< EOT
        <td><input type='checkbox' name='checkbox[]' value="$status_id"></td>
        <td>$name </td>
EOT;
    }
$table .= <<< EOT
        </tbody>
    </table>
    <button type="submit" class="btn btn-outline btn-warning">
       Assign Status
    </button>
</form>
<script src="js/plugins/dataTables/jquery.dataTables.js"></script>
<script src="js/plugins/dataTables/dataTables.bootstrap.js"></script> 
<script  src="js/dataTables.fnGetHiddenNodes.js"></script>
<script  src="js/dataTables.fnGetFilteredNodes.js"></script>
<script>

    $(document).ready(function() {
        $('table').dataTable( {
            paginate: true,
            scrolly: 300,
            iDisplayStart: 0,
            iDisplayLength: 100,
        });
        
    });
$(document).ready(function() {
   oTable = $('table').dataTable();
    
   $('form').submit(function(){

      $(oTable.fnGetHiddenNodes()).find('input:checked').appendTo('#hiddencontainer');
   });
 
});
$('#allcb').click( function() { //this is the function that will mark all your checkboxes when the input with the .checkall class is clicked
   oTable = $('table').dataTable();
   
    $('input', oTable.fnGetFilteredNodes()).prop('checked',this.checked); //note it's calling fnGetFilteredNodes() - this is so it will mark all nodes whether they are filtered or not
} );
</script>

EOT;
echo $table;
}
?>
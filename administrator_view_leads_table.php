<?php

/**
*   This function allows the user to select a certain filter for leads. A possible filters would be "flagged leads".
*
*  It uses the db_connection function from db_query.php to connect to the database.
*  $filter_connection contains the special function $_POST that retrieves the choice of the user from the dropdown list.
*  $query is a resource and references PHP's special built-in mysql_query function. The mysql_query contains the MySQL query to be used in the database.
* 
*  If the user selects "all" as it's filter, the system will display a table that contains all lead details including the campaign name, contact's full name, agent assigned and when it was last updated.
*  If the user selects "flagged leads"  as it's filter, the system will display a table of all the flagged leads in the database.
*  The user can also select a different filter depending on his desire. 
*   
*   @var resource
*/
include_once("db_query.php");
    db_connection();

$filter_selection=$_POST['filter_selection'];

if($filter_selection=="All")
{
	$query=mysql_query("SELECT campaign_name,contact_first_name,  contact_middle_name, contact_last_name, a.name, date_last_updated, status_name, company_name, l.lead_id FROM campaign_lead cl , campaign c , account a, lead  l, status s WHERE cl.campaign_id=c.campaign_id AND 
        a.user_id=cl.user_id AND l.lead_id=cl.lead_id AND cl.status_id=s.status_id;");
}
else if($filter_selection=="flagged_leads")
{
	$query=mysql_query("SELECT contact_first_name,   contact_middle_name, contact_last_name, a.name, date_modified, company_name, l.lead_id FROM lead l, flagged_leads f, account a WHERE a.user_id=f.user_id AND f.lead_id=l.lead_id;");
}
else
{
    $query=mysql_query("SELECT campaign_name, contact_first_name,  contact_middle_name,contact_last_name, a.name, date_last_updated, company_name, l.lead_id FROM campaign_lead cl , campaign c , account a, lead  l WHERE cl.campaign_id=c.campaign_id AND 
        a.user_id=cl.user_id AND l.lead_id=cl.lead_id AND cl.status_id='$filter_selection';");   
}

if($filter_selection=="flagged_leads")
{
$table="";
$table .= <<<EOT
 
                    
                        <div class="col-md-6">
                        <!-- start:advanced table -->
                        <div class="box" >
                            <h4></h4>
                            <hr>                            
                            <table class="table table-striped table-advance table-hover" id="admin-table">
                                <thead>
                                    <tr>                                     
                                        <th class="hidden-phone" style="width: 15%"> Lead Name</th> 
                                        <th class="hidden-phone" style="width: 15%"> Company Name</th>     
                                        <th class="hidden-phone" style="width: 15%"> Agent Assigned</th>
                                        <th class="hidden-phone" style="width: 15%"> Date Modified</th>                                      
                                        <th class="hidden-phone" style="width: 15%"> Delete Lead</th>
                                    </tr>
                                </thead>
                                <tbody>

EOT;



    while($row = mysql_fetch_row($query))
    {
      
        $table .= "<tr>";
        $lead="";
        $lead = $row[0];
        $lead.= $row[1];
        $lead.= $row[2];
        $agent_assigned=$row[3];
       $date_modified=$row[4];
        $company_name=$row[5];
        $table .= <<< EOT
        <td>$lead </td>
        <td>$company_name </td>
        <td>$agent_assigned </td>
       <td>$date_modified </td>
    <td><a data-href="view_leads.php?lead_id=$lead_id&intent=delete" data-toggle="modal" data-target="#delete_lead" href="#">
                    <button type="button" class="btn btn-danger btn-xs"> 
                        Delete
                    </button>
                </a></td>

EOT;

    }

$table .= <<< EOT

</tbody>


                            </table>
                           
                            
                        </div>

                        </div>
<script src="js/plugins/dataTables/jquery.dataTables.js"></script>
<script src="js/plugins/dataTables/dataTables.bootstrap.js"></script>
<script>
$('#admin-table').dataTable();
</script>     
              
EOT;
echo $table;
}
else if($filter_selection=="All")
{
$table="";
$table .= <<<EOT
 
                                 
                            <table class="table table-striped table-advance table-hover" id="admin-table">
                                <thead>
                                    <tr>                                     
                                        <th class="hidden-phone" style="width: 15%">Campaign</th>
                                        <th class="hidden-phone" style="width: 15%"> Lead Name</th>   
                                        <th class="hidden-phone" style="width: 15%"> Company Name</th>   
                                        <th class="hidden-phone" style="width: 15%"> Agent Assigned</th>
                                        <th class="hidden-phone" style="width: 15%"> Last Updated</th>
                                        <th class="hidden-phone" style="width: 15%"> Status</th>    
                                        <th class="hidden-phone" style="width: 15%"> Delete Lead</th>                                  
                                         
                                    </tr>
                                </thead>
                                <tbody>

EOT;



    while($row = mysql_fetch_row($query))
    {
      
        $table .= "<tr>";
        $campaign_name = $row[0];
        $lead="";
        $lead .= $row[1];
        $lead.=" ";
        $lead.=$row[2];
        $lead.=" ";
        $lead.=$row[3];
        $agent_assigned=$row[4];
        $last_updated=$row[5];
        $status=$row[6];
        $company_name=$row[7];
        $table .= <<< EOT
        <td>$campaign_name </td>
        <td>$lead </td>
        <td>$company_name </td>
        <td>$agent_assigned </td>
        <td>$last_updated </td>
        <td>$status </td>
        <td><a data-href="view_leads.php?lead_id=$lead_id&intent=delete" data-toggle="modal" data-target="#delete_lead" href="#">
                    <button type="button" class="btn btn-danger btn-xs"> 
                        Delete
                    </button>
        </a></td>
EOT;

    }

$table .= <<< EOT

</tbody>


                            </table>
                           
                      
<script src="js/plugins/dataTables/jquery.dataTables.js"></script>
<script src="js/plugins/dataTables/dataTables.bootstrap.js"></script>
<script>
$('#admin-table').dataTable();
</script>        
              
EOT;
echo $table;
}
else 
{
$table="";
$table .= <<<EOT
 

                            <table class="table table-striped table-advance table-hover" id="admin-table">
                                <thead>
                                    <tr>                                     
                                        <th class="hidden-phone" style="width: 15%">Campaign</th>
                                        <th class="hidden-phone" style="width: 15%"> Lead Name</th> 
                                        <th class="hidden-phone" style="width: 15%"> Company Name</th> 
                                        <th class="hidden-phone" style="width: 15%"> Agent Assigned</th>
                                        <th class="hidden-phone" style="width: 15%"> Last Updated</th>
                                        <th class="hidden-phone" style="width: 15%"> Delete Lead</th>
                                         
                                    </tr>
                                </thead>
                                <tbody>

EOT;



    while($row = mysql_fetch_row($query))
    {
      
        $table .= "<tr>";
        $campaign_name = $row[0];
        $lead="";
        $lead .= $row[1];
        $lead.=" ";
        $lead.=$row[2];
        $lead.=" ";
        $lead.=$row[3];
        $agent_assigned=$row[4];
        $last_updated=$row[5];
        $company_name=$row[6];
        
        $table .= <<< EOT
        <td>$campaign_name </td>
        <td>$lead </td>
        <td>$company_name </td>
        <td>$agent_assigned </td>
        <td>$last_updated </td>
        <td><a data-href="view_leads.php?lead_id=$lead_id&intent=delete" data-toggle="modal" data-target="#delete_lead" href="#">
                    <button type="button" class="btn btn-danger btn-xs"> 
                        Delete
                    </button>
        </a></td>

EOT;

    }

$table .= <<< EOT

</tbody>


                            </table>
                         
<script src="js/plugins/dataTables/jquery.dataTables.js"></script>
<script src="js/plugins/dataTables/dataTables.bootstrap.js"></script>
<script>

$('#admin-table').dataTable();
</script>
        
EOT;
echo $table;
}
?>




    
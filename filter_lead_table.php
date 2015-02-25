<?php

	/**
*	Allows user to filter the leads located in the table according to a specific filter in
*	the dropdown list. The filter consists of statuses assigned to a campaign. Examples of 
*	possible criteria in the filter include the statuses, "Call back in x days", "Hard no",
*	"Wrong number", etc.
*
*	$filter_selection stores the criterion that the user chooses for filtering
*	$selected_campaign stores whichever campaign the user chose for filtering
*	
*	@var resource
*/

    include_once("db_query.php");
db_connection();

    $filter_selection=$_POST['filter_selection'];
    $user_id=$_POST['user_id'];
    $selected_campaign=$_POST['selected_campaign'];
    $row_index="0";
    $total_rows="0";
    $navigation="current";
    
    if($filter_selection=="12")
    {
    	$date=date("Y-m-d ");
    	$query=mysql_query("SELECT company_name, primary_industry, c.campaign_id, DATE_FORMAT(callback_date, '%h:%i %p' ) AS callback_date, l.lead_id FROM campaign_lead c, lead l WHERE status_id='3' AND user_id='$user_id' AND DATE_FORMAT( callback_date, '%Y-%m-%d')='$date' 
    		AND c.lead_id=l.lead_id AND c.campaign_id='$selected_campaign' ORDER BY callback_date ASC;");
    }
    else 
    {
    	$query=mysql_query("SELECT company_name, primary_industry, c.campaign_id, l.lead_id FROM campaign_lead c, lead l WHERE status_id='$filter_selection' 
    		AND user_id='$user_id' AND c.lead_id=l.lead_id AND c.campaign_id='$selected_campaign' ORDER BY company_name ASC;");
    }

    if($filter_selection=="12")
    {
        $table="";
        $table .= <<<EOT
            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>                                     
                        <th class="hidden-phone" style="width: 15%">Company Name</th>
                        <th class="hidden-phone" style="width: 15%"> Industry</th>   
                        <th class="hidden-phone" style="width: 15%"> CallBack Time</th>                                   
                        <th class="hidden-phone" style="width: 15%"> Lead ID</th>  
                    </tr>
                </thead>
                <tbody>
EOT;
        while($row = mysql_fetch_row($query))
        {
          
            $table .= "<tr>";
            $company_name = $row[0];
            $primary_industry = $row[1];
            $campaign_id=$row[2];
            $callback_date=$row[3];
            $lead_id=$row[4];
            $table .= <<< EOT
                    <td>$company_name </td>
                    <td>$primary_industry </td>
                    <td>$callback_date </td>
                    <td id='$row_index'  onclick="getID(this);">$lead_id </td>
EOT;
           $row_index = $row_index+1;
           $total_rows=$total_rows+1;
        }

        $table .= <<< EOT
                </tbody>
            </table>
        <script src="js/plugins/dataTables/jquery.dataTables.js"></script>
        <script src="js/plugins/dataTables/dataTables.bootstrap.js"></script>
        <script>
            $('table').dataTable({
                paginate: true,
                scrollY: 300,
                iDisplayStart: 0,  
            });

            function getID(oObject)
            {
                var row_index=oObject.id;
               
                var selected_campaign=$selected_campaign;
                var filter_selection=$filter_selection;
                var navigation="current";
                var total_rows=$total_rows;
                var leads_id=oObject.innerHTML;
                document.location.href = 'lead.php?selected_campaign='+ selected_campaign+'&row_index='+row_index
                +'&filter_selection=' +filter_selection+'&navigation=' + navigation +'&total_rows='+total_rows + '&lead_id=' +leads_id;
            }
        </script>      
EOT;
        echo $table;
    }
    else
    {
        $table="";
        $table .= <<<EOT
                                
            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>     
                                                       
                        <th class="hidden-phone" style="width: 15%">Company Name</th>
                        <th class="hidden-phone" style="width: 15%"> Industry</th>
                        <th class="hidden-phone" style="width: 15%"> Lead ID</th>  
                    </tr>
                </thead>
                <tbody>
EOT;
        while($row = mysql_fetch_row($query))
        {
            $table .= "<tr>";
            $company_name = $row[0];
            $primary_industry = $row[1];
            $campaign_id=$row[2];
            $lead_id=$row[3];
            $table .= <<< EOT
            
            <td>$company_name </td> 
            <td>$primary_industry </td>   
            <td id='$row_index'  onclick="getID(this);">$lead_id </td>
EOT;
           $row_index = $row_index+1;
           $total_rows=$total_rows+1;
        }
        $table .= <<< EOT
                </tbody>
            </table> 
        <script src="js/plugins/dataTables/jquery.dataTables.js"></script>
        <script src="js/plugins/dataTables/dataTables.bootstrap.js"></script>
        <script>
            $('table').dataTable({
                paginate: true,
                scrollY: 300,
                iDisplayStart: 0,  
            });

            function getID(oObject)
            {
                var row_index=oObject.id;
               
                var selected_campaign=$selected_campaign;
                var filter_selection=$filter_selection;
                var navigation="current";
                var total_rows=$total_rows;
                var leads_id=oObject.innerHTML;
                document.location.href = 'lead.php?selected_campaign='+ selected_campaign+'&row_index='+row_index
                +'&filter_selection=' +filter_selection+'&navigation=' + navigation +'&total_rows='+total_rows + '&lead_id=' +leads_id;
            }
        </script> 
EOT;
        echo $table;
    }
?>
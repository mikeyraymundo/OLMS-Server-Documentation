<?php
/**
 *	This module allows the user to export leads into a .csv file. The function sets the 
 *	first row of the csv file as containing detail headers such as contact_prefix, company_name,
 *	phone numbers etc. 
 *
 *	"ini_set" sets the value of a configuration point. he configuration option will keep 
 *	this new value during the script's execution, and will be restored at the script's ending. 
 *	"mysql_set_charset" sets the client character set. It sets the default character set 
 *	for the current connection. UTF8 is used. UTF8 is once again selected in 
 *	mb_internal_encoding. A valid character set must be used.
 *
 *	@var resource
 *
 */
    $output = "contact_prefix,contact_first_name,contact_middle_name,contact_last_name,contact_suffix,contact_title,company_name,primary_add_1,primary_add_2,primary_city, primary_county,primary_state,primary_zip,primary_zip_extension,primary_country,phone_number,toll_free,fax_number,web_address,latitude,longtitude,line_of_business,is_importer,is_exporter,total_employees,year_founded,primary_industry,email \n";
    include_once("db_query.php");
    db_connection();
    mysql_set_charset("utf8");

    session_start();
    if (!isset($_SESSION))
    {
        header("location:login.php");
    }

    ini_set("auto_detect_line_endings", true);
    ini_set('default_charset', 'utf-8');
    ini_set('max_input_vars', 3000);
    set_time_limit(0);
    mb_internal_encoding('UTF-8');
    

    $lead_total=mysql_query("SELECT COUNT(1) FROM lead");
    $array=mysql_fetch_array($lead_total);
    $total_leads=$array[0];

    $switch = NULL;
    $content = NULL;
    $index = 0;
    $fcIndex = 0;
    $nullSwitch = "NO ACTION";
    $whiteSpaceSwitch = "NO ACTION";
    $cannotBeNull = array();
    $canBeNull = array();
    $leadCount = 0;
    $leadContent = array();
    $export_name= date("M-d-Y", time());
    $export_name.="-Leads.csv";
    $fileExists = "NO";
    $currentCount = 0;


    if(isset($_POST['intent']))
    {
      extract($_POST);
      if($intent == "export")
      {
        if(isset($_POST['checkbox']))
        {
            mysql_set_charset("utf8");

            foreach($_POST['checkbox'] as $selected)
            {
                $selected = mysql_real_escape_string($selected);
                $sql = mysql_query("select * from lead where primary_industry='$selected'");

                $columns_total = mysql_num_fields($sql);

                while ($row = mysql_fetch_array($sql)) 
                {
                for ($i = 1; $i < $columns_total - 1; $i++) 
                    {
                        $output .= utf8_decode('"'.$row["$i"].'",');
                    }
                    $output .="\n";
                }
            }
        }
         header("Content-Type: text/csv; charset=UTF-8");
         header("Content-Disposition: attachment; filename=$export_name");
         header("Cache-Control: no-cache, no-store, must-revalidate"); 
         header("Pragma: no-cache"); 
         header("Expires: 0"); 
         echo $output;
         exit;

      }
    }


if(isset($_FILES['myfile']))
{
    if(!file_exists($_FILES['myfile']['tmp_name']) || !is_uploaded_file($_FILES['myfile']['tmp_name'])) 
    {
       echo "<script type='text/javascript'>alert('Select a File'); location.href='export_leads.php'</script>";
    }
    else
    {
        $fileExists = "YES";

    }
}
else
{
    $fileExists = "NO";
}

if($fileExists == "YES")
{

    $fileTempName = $_FILES['myfile']['tmp_name'];
    $fileType = $_FILES['myfile']['type'];
    $fileName = $_FILES['myfile']['name'];
    $info = pathinfo($fileName);

    if($info['extension'] == 'csv')
    {
        $fileContents = file($fileTempName);

        //sizeOf = size of array
        $file = fopen($fileTempName,"r");
        $count = count(file($fileTempName));
        while(!feof($file))
        {   
            if($leadCount < $count)
            {
                $leadContent = array();
                $leadContent = fgetcsv($file);

                if($leadCount == 0)
                {
                    
                }
                elseif($leadCount > 0)
                {
                        $contact_prefix = $leadContent[0];
                        $contact_first_name = $leadContent[1];
                        $contact_middle_name = $leadContent[2];
                        $contact_last_name = $leadContent[3];
                        $contact_suffix = $leadContent[4];
                        $contact_title = $leadContent[5];
                        $company_name = $leadContent[6];
                        $primary_add_1 = $leadContent[7];
                        $primary_add_2 = $leadContent[8];
                        $primary_city = $leadContent[9];
                        $primary_county = $leadContent[10];
                        $primary_state = $leadContent[11];
                        $primary_zip = $leadContent[12];
                        $primary_zip_extension = $leadContent[13];
                        $primary_country = $leadContent[14];
                        $phone_number = $leadContent[15];
                        $toll_free = $leadContent[16];
                        $fax_number = $leadContent[17];
                        $web_address = $leadContent[18];
                        $latitude = $leadContent[19];
                        $longtitude = $leadContent[20];
                        $line_of_business = $leadContent[21];
                        $is_importer = $leadContent[22];
                        $is_exporter = $leadContent[23];
                        $total_employees = $leadContent[24];
                        $year_founded = $leadContent[25];
                        $primary_industry = $leadContent[26];
                        $Email = trim($leadContent[27]);

                        

                        $contact_prefix = str_replace(
                         array("\xe2\x80\x98", "\xe2\x80\x99", "\xe2\x80\x9c", "\xe2\x80\x9d", "\xe2\x80\x93", "\xe2\x80\x94", "\xe2\x80\xa6"),
                         array("'", "'", '"', '"', '-', '--', '...'),
                         $contact_prefix);
                        $contact_prefix = str_replace(
                         array(chr(145), chr(146), chr(147), chr(148), chr(150), chr(151), chr(133)),
                         array("'", "'", '"', '"', '-', '--', '...'),
                         $contact_prefix);

                        $contact_first_name = str_replace(
                         array("\xe2\x80\x98", "\xe2\x80\x99", "\xe2\x80\x9c", "\xe2\x80\x9d", "\xe2\x80\x93", "\xe2\x80\x94", "\xe2\x80\xa6"),
                         array("'", "'", '"', '"', '-', '--', '...'),
                         $contact_first_name);
                        $contact_first_name = str_replace(
                         array(chr(145), chr(146), chr(147), chr(148), chr(150), chr(151), chr(133)),
                         array("'", "'", '"', '"', '-', '--', '...'),
                         $contact_first_name);

                        $contact_middle_name = str_replace(
                         array("\xe2\x80\x98", "\xe2\x80\x99", "\xe2\x80\x9c", "\xe2\x80\x9d", "\xe2\x80\x93", "\xe2\x80\x94", "\xe2\x80\xa6"),
                         array("'", "'", '"', '"', '-', '--', '...'),
                         $contact_middle_name);
                        $contact_middle_name = str_replace(
                         array(chr(145), chr(146), chr(147), chr(148), chr(150), chr(151), chr(133)),
                         array("'", "'", '"', '"', '-', '--', '...'),
                         $contact_middle_name);

                        $contact_last_name = str_replace(
                         array("\xe2\x80\x98", "\xe2\x80\x99", "\xe2\x80\x9c", "\xe2\x80\x9d", "\xe2\x80\x93", "\xe2\x80\x94", "\xe2\x80\xa6"),
                         array("'", "'", '"', '"', '-', '--', '...'),
                         $contact_last_name);
                        $contact_last_name = str_replace(
                         array(chr(145), chr(146), chr(147), chr(148), chr(150), chr(151), chr(133)),
                         array("'", "'", '"', '"', '-', '--', '...'),
                         $contact_last_name);

                        $contact_suffix = str_replace(
                         array("\xe2\x80\x98", "\xe2\x80\x99", "\xe2\x80\x9c", "\xe2\x80\x9d", "\xe2\x80\x93", "\xe2\x80\x94", "\xe2\x80\xa6"),
                         array("'", "'", '"', '"', '-', '--', '...'),
                         $contact_suffix);
                        $contact_suffix = str_replace(
                         array(chr(145), chr(146), chr(147), chr(148), chr(150), chr(151), chr(133)),
                         array("'", "'", '"', '"', '-', '--', '...'),
                         $contact_suffix);

                        $contact_title = str_replace(
                         array("\xe2\x80\x98", "\xe2\x80\x99", "\xe2\x80\x9c", "\xe2\x80\x9d", "\xe2\x80\x93", "\xe2\x80\x94", "\xe2\x80\xa6"),
                         array("'", "'", '"', '"', '-', '--', '...'),
                         $contact_title);
                        $contact_title = str_replace(
                         array(chr(145), chr(146), chr(147), chr(148), chr(150), chr(151), chr(133)),
                         array("'", "'", '"', '"', '-', '--', '...'),
                         $contact_title);

                        $company_name = str_replace(
                         array("\xe2\x80\x98", "\xe2\x80\x99", "\xe2\x80\x9c", "\xe2\x80\x9d", "\xe2\x80\x93", "\xe2\x80\x94", "\xe2\x80\xa6"),
                         array("'", "'", '"', '"', '-', '--', '...'),
                         $company_name);
                         $company_name = str_replace(
                         array(chr(145), chr(146), chr(147), chr(148), chr(150), chr(151), chr(133)),
                         array("'", "'", '"', '"', '-', '--', '...'),
                         $company_name);

                         $primary_add_1 = str_replace(
                         array("\xe2\x80\x98", "\xe2\x80\x99", "\xe2\x80\x9c", "\xe2\x80\x9d", "\xe2\x80\x93", "\xe2\x80\x94", "\xe2\x80\xa6"),
                         array("'", "'", '"', '"', '-', '--', '...'),
                         $primary_add_1);
                         $primary_add_1 = str_replace(
                         array(chr(145), chr(146), chr(147), chr(148), chr(150), chr(151), chr(133)),
                         array("'", "'", '"', '"', '-', '--', '...'),
                         $primary_add_1);

                         $primary_add_2 = str_replace(
                         array("\xe2\x80\x98", "\xe2\x80\x99", "\xe2\x80\x9c", "\xe2\x80\x9d", "\xe2\x80\x93", "\xe2\x80\x94", "\xe2\x80\xa6"),
                         array("'", "'", '"', '"', '-', '--', '...'),
                         $primary_add_2);
                         $primary_add_2 = str_replace(
                         array(chr(145), chr(146), chr(147), chr(148), chr(150), chr(151), chr(133)),
                         array("'", "'", '"', '"', '-', '--', '...'),
                         $primary_add_2);

                         $primary_city = str_replace(
                         array("\xe2\x80\x98", "\xe2\x80\x99", "\xe2\x80\x9c", "\xe2\x80\x9d", "\xe2\x80\x93", "\xe2\x80\x94", "\xe2\x80\xa6"),
                         array("'", "'", '"', '"', '-', '--', '...'),
                         $primary_city);
                         $primary_city = str_replace(
                         array(chr(145), chr(146), chr(147), chr(148), chr(150), chr(151), chr(133)),
                         array("'", "'", '"', '"', '-', '--', '...'),
                         $primary_city);

                         $primary_county = str_replace(
                         array("\xe2\x80\x98", "\xe2\x80\x99", "\xe2\x80\x9c", "\xe2\x80\x9d", "\xe2\x80\x93", "\xe2\x80\x94", "\xe2\x80\xa6"),
                         array("'", "'", '"', '"', '-', '--', '...'),
                         $primary_county);
                         $primary_county = str_replace(
                         array(chr(145), chr(146), chr(147), chr(148), chr(150), chr(151), chr(133)),
                         array("'", "'", '"', '"', '-', '--', '...'),
                         $primary_county);

                         $primary_state = str_replace(
                         array("\xe2\x80\x98", "\xe2\x80\x99", "\xe2\x80\x9c", "\xe2\x80\x9d", "\xe2\x80\x93", "\xe2\x80\x94", "\xe2\x80\xa6"),
                         array("'", "'", '"', '"', '-', '--', '...'),
                         $primary_state);
                         $primary_state = str_replace(
                         array(chr(145), chr(146), chr(147), chr(148), chr(150), chr(151), chr(133)),
                         array("'", "'", '"', '"', '-', '--', '...'),
                         $primary_state);

                         $primary_zip = str_replace(
                         array("\xe2\x80\x98", "\xe2\x80\x99", "\xe2\x80\x9c", "\xe2\x80\x9d", "\xe2\x80\x93", "\xe2\x80\x94", "\xe2\x80\xa6"),
                         array("'", "'", '"', '"', '-', '--', '...'),
                         $primary_zip);
                         $primary_zip = str_replace(
                         array(chr(145), chr(146), chr(147), chr(148), chr(150), chr(151), chr(133)),
                         array("'", "'", '"', '"', '-', '--', '...'),
                         $primary_zip);

                         $primary_zip_extension = str_replace(
                         array("\xe2\x80\x98", "\xe2\x80\x99", "\xe2\x80\x9c", "\xe2\x80\x9d", "\xe2\x80\x93", "\xe2\x80\x94", "\xe2\x80\xa6"),
                         array("'", "'", '"', '"', '-', '--', '...'),
                         $primary_zip_extension);
                         $primary_zip_extension = str_replace(
                         array(chr(145), chr(146), chr(147), chr(148), chr(150), chr(151), chr(133)),
                         array("'", "'", '"', '"', '-', '--', '...'),
                         $primary_zip_extension);

                         $primary_country = str_replace(
                         array("\xe2\x80\x98", "\xe2\x80\x99", "\xe2\x80\x9c", "\xe2\x80\x9d", "\xe2\x80\x93", "\xe2\x80\x94", "\xe2\x80\xa6"),
                         array("'", "'", '"', '"', '-', '--', '...'),
                         $primary_country);
                         $primary_country = str_replace(
                         array(chr(145), chr(146), chr(147), chr(148), chr(150), chr(151), chr(133)),
                         array("'", "'", '"', '"', '-', '--', '...'),
                         $primary_country);

                         $phone_number = str_replace(
                         array("\xe2\x80\x98", "\xe2\x80\x99", "\xe2\x80\x9c", "\xe2\x80\x9d", "\xe2\x80\x93", "\xe2\x80\x94", "\xe2\x80\xa6"),
                         array("'", "'", '"', '"', '-', '--', '...'),
                         $phone_number);
                         $phone_number = str_replace(
                         array(chr(145), chr(146), chr(147), chr(148), chr(150), chr(151), chr(133)),
                         array("'", "'", '"', '"', '-', '--', '...'),
                         $phone_number);

                         $toll_free = str_replace(
                         array("\xe2\x80\x98", "\xe2\x80\x99", "\xe2\x80\x9c", "\xe2\x80\x9d", "\xe2\x80\x93", "\xe2\x80\x94", "\xe2\x80\xa6"),
                         array("'", "'", '"', '"', '-', '--', '...'),
                         $toll_free);
                         $toll_free = str_replace(
                         array(chr(145), chr(146), chr(147), chr(148), chr(150), chr(151), chr(133)),
                         array("'", "'", '"', '"', '-', '--', '...'),
                         $toll_free);

                         $fax_number = str_replace(
                         array("\xe2\x80\x98", "\xe2\x80\x99", "\xe2\x80\x9c", "\xe2\x80\x9d", "\xe2\x80\x93", "\xe2\x80\x94", "\xe2\x80\xa6"),
                         array("'", "'", '"', '"', '-', '--', '...'),
                         $fax_number);
                         $fax_number = str_replace(
                         array(chr(145), chr(146), chr(147), chr(148), chr(150), chr(151), chr(133)),
                         array("'", "'", '"', '"', '-', '--', '...'),
                         $fax_number);

                         $web_address = str_replace(
                         array("\xe2\x80\x98", "\xe2\x80\x99", "\xe2\x80\x9c", "\xe2\x80\x9d", "\xe2\x80\x93", "\xe2\x80\x94", "\xe2\x80\xa6"),
                         array("'", "'", '"', '"', '-', '--', '...'),
                         $web_address);
                         $web_address = str_replace(
                         array(chr(145), chr(146), chr(147), chr(148), chr(150), chr(151), chr(133)),
                         array("'", "'", '"', '"', '-', '--', '...'),
                         $web_address);

                         $latitude = str_replace(
                         array("\xe2\x80\x98", "\xe2\x80\x99", "\xe2\x80\x9c", "\xe2\x80\x9d", "\xe2\x80\x93", "\xe2\x80\x94", "\xe2\x80\xa6"),
                         array("'", "'", '"', '"', '-', '--', '...'),
                         $latitude);
                         $latitude = str_replace(
                         array(chr(145), chr(146), chr(147), chr(148), chr(150), chr(151), chr(133)),
                         array("'", "'", '"', '"', '-', '--', '...'),
                         $latitude);

                         $longtitude = str_replace(
                         array("\xe2\x80\x98", "\xe2\x80\x99", "\xe2\x80\x9c", "\xe2\x80\x9d", "\xe2\x80\x93", "\xe2\x80\x94", "\xe2\x80\xa6"),
                         array("'", "'", '"', '"', '-', '--', '...'),
                         $longtitude);
                         $longtitude = str_replace(
                         array(chr(145), chr(146), chr(147), chr(148), chr(150), chr(151), chr(133)),
                         array("'", "'", '"', '"', '-', '--', '...'),
                         $longtitude);

                         $line_of_business = str_replace(
                         array("\xe2\x80\x98", "\xe2\x80\x99", "\xe2\x80\x9c", "\xe2\x80\x9d", "\xe2\x80\x93", "\xe2\x80\x94", "\xe2\x80\xa6"),
                         array("'", "'", '"', '"', '-', '--', '...'),
                         $line_of_business);
                         $line_of_business = str_replace(
                         array(chr(145), chr(146), chr(147), chr(148), chr(150), chr(151), chr(133)),
                         array("'", "'", '"', '"', '-', '--', '...'),
                         $line_of_business);

                         $is_importer = str_replace(
                         array("\xe2\x80\x98", "\xe2\x80\x99", "\xe2\x80\x9c", "\xe2\x80\x9d", "\xe2\x80\x93", "\xe2\x80\x94", "\xe2\x80\xa6"),
                         array("'", "'", '"', '"', '-', '--', '...'),
                         $is_importer);
                         $is_importer = str_replace(
                         array(chr(145), chr(146), chr(147), chr(148), chr(150), chr(151), chr(133)),
                         array("'", "'", '"', '"', '-', '--', '...'),
                         $is_importer);

                         $is_exporter = str_replace(
                         array("\xe2\x80\x98", "\xe2\x80\x99", "\xe2\x80\x9c", "\xe2\x80\x9d", "\xe2\x80\x93", "\xe2\x80\x94", "\xe2\x80\xa6"),
                         array("'", "'", '"', '"', '-', '--', '...'),
                         $is_exporter);
                         $is_exporter = str_replace(
                         array(chr(145), chr(146), chr(147), chr(148), chr(150), chr(151), chr(133)),
                         array("'", "'", '"', '"', '-', '--', '...'),
                         $is_exporter);

                         $total_employees = str_replace(
                         array("\xe2\x80\x98", "\xe2\x80\x99", "\xe2\x80\x9c", "\xe2\x80\x9d", "\xe2\x80\x93", "\xe2\x80\x94", "\xe2\x80\xa6"),
                         array("'", "'", '"', '"', '-', '--', '...'),
                         $total_employees);
                         $total_employees = str_replace(
                         array(chr(145), chr(146), chr(147), chr(148), chr(150), chr(151), chr(133)),
                         array("'", "'", '"', '"', '-', '--', '...'),
                         $total_employees);

                         $year_founded = str_replace(
                         array("\xe2\x80\x98", "\xe2\x80\x99", "\xe2\x80\x9c", "\xe2\x80\x9d", "\xe2\x80\x93", "\xe2\x80\x94", "\xe2\x80\xa6"),
                         array("'", "'", '"', '"', '-', '--', '...'),
                         $year_founded);
                         $year_founded = str_replace(
                         array(chr(145), chr(146), chr(147), chr(148), chr(150), chr(151), chr(133)),
                         array("'", "'", '"', '"', '-', '--', '...'),
                         $year_founded);

                         $primary_industry = str_replace(
                         array("\xe2\x80\x98", "\xe2\x80\x99", "\xe2\x80\x9c", "\xe2\x80\x9d", "\xe2\x80\x93", "\xe2\x80\x94", "\xe2\x80\xa6"),
                         array("'", "'", '"', '"', '-', '--', '...'),
                         $primary_industry);
                         $primary_industry = str_replace(
                         array(chr(145), chr(146), chr(147), chr(148), chr(150), chr(151), chr(133)),
                         array("'", "'", '"', '"', '-', '--', '...'),
                         $primary_industry);

                         $Email = str_replace(
                         array("\xe2\x80\x98", "\xe2\x80\x99", "\xe2\x80\x9c", "\xe2\x80\x9d", "\xe2\x80\x93", "\xe2\x80\x94", "\xe2\x80\xa6"),
                         array("'", "'", '"', '"', '-', '--', '...'),
                         $Email);
                         $Email = str_replace(
                         array(chr(145), chr(146), chr(147), chr(148), chr(150), chr(151), chr(133)),
                         array("'", "'", '"', '"', '-', '--', '...'),
                         $Email);




                         $contact_prefix = iconv('ISO-8859-1', 'UTF-8', $contact_prefix);
                         $contact_first_name = iconv('ISO-8859-1', 'UTF-8', $contact_first_name);
                         $contact_middle_name = iconv('ISO-8859-1', 'UTF-8', $contact_middle_name);
                         $contact_last_name = iconv('ISO-8859-1', 'UTF-8', $contact_last_name);
                         $contact_suffix = iconv('ISO-8859-1', 'UTF-8', $contact_suffix);
                         $contact_title = iconv('ISO-8859-1', 'UTF-8', $contact_title);
                         $company_name = iconv('ISO-8859-1', 'utf-8', $company_name);
                         $primary_add_1 = iconv('ISO-8859-1', 'UTF-8', $primary_add_1);
                         $primary_add_2 = iconv('ISO-8859-1', 'UTF-8', $primary_add_2);
                         $primary_city = iconv('ISO-8859-1', 'UTF-8', $primary_city);
                         $primary_county = iconv('ISO-8859-1', 'UTF-8', $primary_county);
                         $primary_state = iconv('ISO-8859-1', 'UTF-8', $primary_state);
                         $primary_zip = iconv('ISO-8859-1', 'UTF-8', $primary_zip);
                         $primary_zip_extension = iconv('ISO-8859-1', 'UTF-8', $primary_zip_extension);
                         $primary_country = iconv('ISO-8859-1', 'UTF-8', $primary_country);
                         $phone_number = iconv('ISO-8859-1', 'UTF-8', $phone_number);
                         $toll_free = iconv('ISO-8859-1', 'UTF-8', $toll_free);
                         $fax_number = iconv('ISO-8859-1', 'UTF-8', $fax_number);
                         $web_address = iconv('ISO-8859-1', 'UTF-8', $web_address);
                         $latitude = iconv('ISO-8859-1', 'UTF-8', $latitude);
                         $longtitude = iconv('ISO-8859-1', 'UTF-8', $longtitude);
                         $line_of_business = iconv('ISO-8859-1', 'UTF-8', $line_of_business);
                         $is_importer = iconv('ISO-8859-1', 'UTF-8', $is_importer);
                         $is_exporter = iconv('ISO-8859-1', 'UTF-8', $is_exporter);
                         $total_employees = iconv('ISO-8859-1', 'UTF-8', $total_employees);
                         $year_founded = iconv('ISO-8859-1', 'UTF-8', $year_founded);
                         $primary_industry = iconv('ISO-8859-1', 'UTF-8', $primary_industry);
                         $Email = iconv('ISO-8859-1', 'UTF-8', $Email);

                        $contact_prefix = mysql_real_escape_string($contact_prefix);
                        $contact_first_name = mysql_real_escape_string($contact_first_name);
                        $contact_middle_name = mysql_real_escape_string($contact_middle_name);
                        $contact_last_name = mysql_real_escape_string($contact_last_name);
                        $contact_suffix = mysql_real_escape_string($contact_suffix);
                        $contact_title = mysql_real_escape_string($contact_title);
                        $company_name = mysql_real_escape_string($company_name);
                        $primary_add_1 = mysql_real_escape_string($primary_add_1);
                        $primary_add_2 = mysql_real_escape_string($primary_add_2);
                        $primary_city = mysql_real_escape_string($primary_city);
                        $primary_county = mysql_real_escape_string($primary_county);
                        $primary_state = mysql_real_escape_string($primary_state);
                        $primary_zip = mysql_real_escape_string($primary_zip);
                        $primary_zip_extension = mysql_real_escape_string($primary_zip_extension);
                        $primary_country = mysql_real_escape_string($primary_country);
                        $phone_number = mysql_real_escape_string($phone_number);
                        $toll_free = mysql_real_escape_string($toll_free);
                        $fax_number = mysql_real_escape_string($fax_number);
                        $web_address = mysql_real_escape_string($web_address);
                        $latitude = mysql_real_escape_string($latitude);
                        $longtitude = mysql_real_escape_string($longtitude);
                        $line_of_business = mysql_real_escape_string($line_of_business);
                        $is_importer = mysql_real_escape_string($is_importer);
                        $is_exporter = mysql_real_escape_string($is_exporter);
                        $total_employees = mysql_real_escape_string($total_employees);
                        $year_founded = mysql_real_escape_string($year_founded);
                        $primary_industry = mysql_real_escape_string($primary_industry);
                        $Email = mysql_real_escape_string($Email);


                        $query = "SELECT contact_first_name, contact_last_name, company_name, primary_add_1, primary_city, primary_county, primary_state, primary_zip, primary_country, phone_number,
                         primary_industry FROM lead WHERE company_name = '$company_name' AND primary_city = '$primary_city' AND primary_state = '$primary_state' AND primary_zip = '$primary_zip' AND primary_country = '$primary_country'
                         AND phone_number = '$phone_number' AND primary_industry = '$primary_industry';";
                        $result = mysql_query($query);
                        $num_rows = mysql_num_rows($result);

                        if(  strlen($company_name) > 0 && strlen($primary_city) > 0 &&
                            strlen($primary_state) > 0 && strlen($primary_zip) > 0 && strlen($primary_country) > 0 && strlen($phone_number) > 0 && strlen($primary_industry) > 0 &&
                            ctype_space($company_name) == FALSE && ctype_space($primary_city) == FALSE && 
                            ctype_space($primary_state) == FALSE && ctype_space($primary_zip) == FALSE && ctype_space($primary_country) == FALSE && ctype_space($phone_number) == FALSE && 
                            ctype_space($primary_industry) == FALSE && $num_rows == 0)
                        {
                            $nullSwitch = "OPEN";
                            $whiteSpaceSwitch = "OPEN";
                            $contact_prefix = strlen($contact_prefix) > 0 ? "'$contact_prefix'" : "NULL";
                            $contact_first_name = strlen($contact_first_name) > 0 ? "'$contact_first_name'" : "NULL";
                            $contact_middle_name = strlen($contact_middle_name) > 0 ? "'$contact_middle_name'" : "NULL";
                            $contact_last_name = strlen($contact_last_name) > 0 ? "'$contact_last_name'" : "NULL";
                            $contact_suffix = strlen($contact_suffix) > 0 ? "'$contact_suffix'" : "NULL";
                            $contact_title = strlen($contact_title) > 0 ? "'$contact_title'" : "NULL";
                            $primary_add_1 = strlen($primary_add_1) > 0 ? "'$primary_add_1'" : "NULL";
                            $primary_add_2 = strlen($primary_add_2) > 0 ? "'$primary_add_2'" : "NULL";
                            $primary_county = strlen($primary_county) > 0 ? "'$primary_county'" : "NULL";
                            $primary_zip_extension = strlen($primary_zip_extension) > 0 ? "'$primary_zip_extension'" : "NULL";
                            $toll_free = strlen($toll_free) > 0 ? "'$toll_free'" : "NULL";
                            $fax_number = strlen($fax_number) > 0 ? "'$fax_number'" : "NULL";
                            $web_address = strlen($web_address) > 0 ? "'$web_address'" : "NULL";
                            $latitude = strlen($latitude) > 0 ? "'$latitude'" : "NULL";
                            $longtitude = strlen($longtitude) > 0 ? "'$longtitude'" : "NULL";
                            $line_of_business = strlen($line_of_business) > 0 ? "'$line_of_business'" : "NULL";
                            $is_importer = strlen($is_importer) > 0 ? "'$is_importer'" : "NULL";
                            $is_exporter = strlen($is_exporter) > 0 ? "'$is_exporter'" : "NULL";
                            $total_employees = strlen($total_employees) > 0 ? "'$total_employees'" : "NULL";
                            $year_founded = strlen($year_founded) > 0 ? "'$year_founded'" : "NULL";
                            $Email = strlen($Email) > 0 ? "'$Email'" : "NULL";
                            $cannotBeNull[] = array($company_name, $primary_city, $primary_state, $primary_zip, $primary_country, $phone_number,
                                $primary_industry);
                            $canBeNull[] = array($contact_prefix, $contact_first_name, $contact_middle_name, $contact_last_name, $contact_suffix, $contact_title, $primary_add_1, $primary_add_2, $primary_county, $primary_zip_extension, $toll_free, $fax_number, $web_address, $latitude, $longtitude, 
                                $line_of_business, $is_importer, $is_exporter, $total_employees, $year_founded, $Email);

                        }
                        else
                        {
                            echo "<script type='text/javascript'>alert('Error at Lead Number: $leadCount\\nEither the lead already exists in the database or there is an error in the format of the lead'); location.href='export_leads.php'</script>";
                            $nullSwitch = "CLOSED";
                            $whiteSpaceSwitch = "CLOSED";
                            break;
                        }
                    
                }
            }
            else
            {
                break;
            }   
                

            $leadCount++;

            
        }
        fclose($file);

        
    }
    else
    {
        echo "<script type='text/javascript'>alert('File Not Allowed'); location.href='export_leads.php'</script>";
    }

    
}


if ($nullSwitch == "OPEN" && $whiteSpaceSwitch == "OPEN")
{
    $insertCount = "0";
    $count3 = sizeOf($cannotBeNull);
    for($x = 0; $x < $count3; $x++)
    {

        $insert1 = implode("','", $cannotBeNull[$x]);
        $contact_prefix = $canBeNull[$x][0];
        $contact_first_name = $canBeNull[$x][1];
        $contact_middle_name = $canBeNull[$x][2];
        $contact_last_name = $canBeNull[$x][3]; 
        $contact_suffix = $canBeNull[$x][4];
        $contact_title = $canBeNull[$x][5];
        $primary_add_1 = $canBeNull[$x][6];
        $primary_add_2 = $canBeNull[$x][7];
        $primary_county = $canBeNull[$x][8];
        $primary_zip_extension = $canBeNull[$x][9];
        $toll_free = $canBeNull[$x][10];
        $fax_number = $canBeNull[$x][11];
        $web_address = $canBeNull[$x][12];
        $latitude = $canBeNull[$x][13];
        $longtitude = $canBeNull[$x][14];
        $line_of_business = $canBeNull[$x][15];
        $is_importer = $canBeNull[$x][16];
        $is_exporter = $canBeNull[$x][17];
        $total_employees = $canBeNull[$x][18];
        $year_founded = $canBeNull[$x][19];
        $Email = trim($canBeNull[$x][20]);

        
        $query = "INSERT INTO lead(company_name, primary_city, primary_state, primary_zip, primary_country, phone_number,
                 primary_industry, contact_prefix, contact_first_name, contact_middle_name, contact_last_name, contact_suffix, contact_title, primary_add_1, primary_add_2, primary_county, primary_zip_extension, toll_free, fax_number, web_address, latitude, longtitude, 
                 line_of_business, is_importer, is_exporter, total_employees, year_founded, Email, extra_field) VALUES ('$insert1', $contact_prefix, $contact_first_name, $contact_middle_name, $contact_last_name, $contact_suffix, $contact_title, $primary_add_1,
                 $primary_add_2, $primary_county, $primary_zip_extension, $toll_free, $fax_number, $web_address, $latitude, $longtitude, $line_of_business, $is_importer, $is_exporter, $total_employees, $year_founded, $Email, 0);";
        $result = mysql_query($query);
        $insertCount++;
        if($insertCount == $count3)
        {
            echo "<script type='text/javascript'>alert('$insertCount leads successfully imported!'); location.href='export_leads.php'</script>";
        }
    

    }
    
}



?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Import & Export Leads </title>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="css/plugins/metisMenu/metisMenu.min.css" rel="stylesheet">

    <!-- Timeline CSS -->
    <link href="css/plugins/timeline.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/sb-admin-2.css" rel="stylesheet">

    <!-- Morris Charts CSS -->
    <link href="css/plugins/morris.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="font-awesome-4.1.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>
    <?php include "navbar.php"; ?>
    <div id="wrapper">
        
        <div id="page-wrapper">
            <div class="row">
                <div class="col-md-12">
                    <h1 class="page-header">Import/Export Leads</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                             Total Number of Leads : <?php echo $total_leads;?> 
                        </div>
                        <?php
                        $table="";
                        $table .= <<<EOT
                        <div class="panel-body">
                            <div class="table-responsive">
                            <form role="form" action="export_leads.php" method="POST" Are you sure you want to export these leads?>
                            <input type="hidden" id="intent" name="intent" value="export">
                                <table class="table table-striped table-bordered table-hover" id="table">
                                <div style="display:none" id="hiddencontainer"></div>
                                    <thead>
                                        <tr>
                                            <th class="hidden-phone"><input type="checkbox" id="allcb">ALL</th>
                                            <th class="hidden-phone">Primary Industry</th>
                                            <th class="hidden-phone">Number of Leads</th>
                                        </tr>
                                    </thead>
                                <tbody>
EOT;
                        $query = "SELECT  primary_industry, count(1) FROM lead group by primary_industry;";
                        $result = mysql_query($query);
                        while($row = mysql_fetch_row($result))
                        {
                            $table .= "<tr>";
                            $industry = $row[0];
                            $leadIndustryCounter = $row[1];
                            
                            $table .= <<< EOT
                            <td><input type='checkbox' name='checkbox[]' value="$industry"></td>
                            <td>$industry </td>
                            <td>$leadIndustryCounter </td>
EOT;
                        }
                        $table .= <<<EOT
                            </tbody>
                        </table>
                    <button type="submit" class="col-lg-4 btn btn-primary">
                        Export as CSV
                    </button>

                    </form>

                    <form action="export_leads.php" method="POST" enctype="multipart/form-data">        
                    <div style="display:none" id="hiddencontainer "> </div>                
                        <span class="col-lg-4 btn btn-info btn-file">
                            <input type="file" name="myfile" id="myfile"> 
                        </span>
                        <button class=" col-lg-4 btn btn-success" type="submit" value="submit file">Import Leads (Maximum File Size: 5MB)</button>
                    </form>
                    <script>
                    var myfile = document.getElementById('myfile');
                    myfile.addEventListener('change', function() {
                    if(this.files[0].size > 5242880)
                    {
                        alert('The file that you are trying to upload is more than 5MB in size');
                        location.href='export_leads.php';

                    }

                    });
                    </script>
                    </div>
                </div>
EOT;
                echo $table;
                ?>
                    </div>
                </div>
                <!-- /.col-lg-12 -->
            </div>
        </div>
        <!-- /#page-wrapper -->
    </div>
    <!-- /#wrapper -->
    



    <!-- jQuery -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="js/plugins/metisMenu/metisMenu.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="js/sb-admin-2.js"></script>

    <!-- DataTables JavaScript -->
    <script src="js/plugins/dataTables/jquery.dataTables.js"></script>
   
    <script src="js/jquery-1.11.1.min.js"></script> 
    <script src="js/jquery.dataTables.min.js"></script> 
    <script  src="js/dataTables.fnGetHiddenNodes.js"></script>
<script  src="js/dataTables.fnGetFilteredNodes.js"></script>
    <!-- Page-Level Demo Scripts - Tables - Use for reference -->
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
   oTable = $('#table').dataTable();
   
    $('input', oTable.fnGetFilteredNodes()).prop('checked',this.checked); //note it's calling fnGetFilteredNodes() - this is so it will mark all nodes whether they are filtered or not
} );
    </script>


</body>

</html>
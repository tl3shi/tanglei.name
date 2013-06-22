<?php
$result=array();
$maxSize = 5;
if($_POST != null)
{
    $json = false;
    if(array_key_exists('json', $_POST))
    {
        $json = $_POST['json'];
        if($json =='on' or $json=='true')
            $json = true;
    }

    $paper_list_str = $_POST['paper_list'];
    $paper_list = explode("\n", $paper_list_str);
    //var_dump($paper_list);
    $paper_list = array_unique($paper_list);
    $exceed_max_size = count($paper_list) > $maxSize;
    $paper_list = array_slice($paper_list, 0, $maxSize);
    foreach($paper_list as $title)
    {
        //$title = $paper_list[0];
        $citeinfo="";
        $cmd = 'python siteTools.py "'. escapeshellcmd($title). '"';
        //var_dump($cmd);
        $citeinfo = shell_exec($cmd);
        
        $order   = array("\r\n", "\n", "\r");
        $replace = '<br />';
        $citeinfo = str_replace($order, $replace, $citeinfo);
        
        array_push($result, $citeinfo);
    }
    if($json === true)
    {
        if($exceed_max_size)
            echo '<font size="5" color="red">To reduce the pressure of my server, Maximum of '. $maxSize .' papers are allowed per time.</font><br/>';
        echo json_encode($result);
    }
    else
    { 
        if($exceed_max_size)
            echo '<br/> <font size="5" color="red">To reduce the pressure of my server, Maximum of '. $maxSize .' papers are allowed per time.</font><br/>';
        echo implode('<br/>', $result);
    }
    exit();
}
echo 'hello world! :)'
?>

<?php


function k_ds_fetch($ds) 
{
    $s = array(CURLOPT_POST => true);
    $page = k_http_request($ds['url_login'], $s, $ds['login_data']);
    $page = k_http_request($ds['url']);
    preg_match($ds['regexp'], $page, $matches);
    if(!empty($matches[1]))
    {
        return $matches[1];
    }
    else
    {
        return false;    
    }
}

?>
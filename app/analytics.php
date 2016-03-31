<?php
include('browser_detection.php');

//This is a wrapper for the (3rdparty) browser detection library
//It returns an array with the following stats:
//  browser, browser_version, os, os_version, is_mobile, js_enabled=False
function get_stats()
{
    $browser_info = browser_detection('full');
    $browser_info = browser_detection('full_assoc');

    $stats = array();
    $stats['is_mobile'] = ($browser_info[8] == 'mobile'); 

    //OS and mobile device detection
    if($stats['is_mobile'])
    {
        if($browser_info[13][0])
            $stats['mobile_type'] = ucwords( $browser_info[13][0] );
        if($browser_info[13][7])
            $stats['mobile_version'] = $browser_info[13][7];
        if ( $browser_info[13][3] )
        {
            if ( $browser_info[13][3] == 'cpu os' )
            {
                $browser_info[13][3] = 'ipad os';
            }
            $stats['os'] = ucwords( $browser_info[13][3] ) . ' ' .  $browser_info[13][4];
        }
        if ( $browser_info[13][1] )
        {
            $stats['browser'] = ucwords( $browser_info[13][1] ); 
            $stats['browser_version'] = $browser_info[13][2];
        }
        if ( $browser_info[13][5] )
        {
            $stats['mobile_server'] = 
                ucwords($browser_info[13][5].' ' .$browser_info[13][6]);
        }
    }

    $os = '';
    $os_version = '';
    if(!$browser_info[5])
    {
        $stats['os'] = "unknown";
    }
    else
    {

        switch ($browser_info[5])
        {
        case 'win':
            $os .= 'Windows ';
            break;
        case 'nt':
            $os .= 'Windows ';
            break;
        case 'lin':
            $os .= 'Linux ';
            break;
        case 'mac':
            $os .= 'Apple Mac ';
            break;
        case 'iphone':
            $os .= 'Apple iOS';
            break;
        case 'unix':
            $os .= 'Unix ';
            break;
        default:
            $os .= $browser_info[5];
        }
        if ( $browser_info[5] == 'nt' )
        {
            $os_version .= $browser_info[6];
            if ($browser_info[6] == 5)
            {
                $os .= ' 2000';
            }
            elseif ($browser_info[6] == 5.1)
            {
                $os .= ' XP';
            }
            elseif ($browser_info[6] == 5.2)
            {
                $os .= ' XP x64 Edition or Windows Server 2003';
            }
            elseif ($browser_info[6] == 6.0)
            {
                $os .= ' Vista';
            }
            elseif ($browser_info[6] == 6.1)
            {
                $os .= ' 7';
            }
            elseif ($browser_info[6] == 'ce')
            {
                $os .= ' CE';
            }
        }

        elseif ( $browser_info[5] == 'iphone' )
        {
            if($browser_info[6] != '')
                $os_version = $browser_info[6];
        }
        // note: browser detection now returns os x version number if available, 10 or 10.4.3 style
        elseif ( ( $browser_info[5] == 'mac' ))
        {
            if(strstr( $browser_info[6], '10')) 
                $os .=  'OS X';
            $os_version = $browser_info[6];
        }
        elseif ( $browser_info[5] == 'lin' )
        {
            if($browser_info[6] != '')
                $os_version = ucwords($browser_info[6]);
        }
        // default case for cases where version number exists
        elseif ( $browser_info[5] && $browser_info[6] )
        {
            $os = ucwords( $browser_info[5] );
            $os_version = ucwords( $browser_info[6] );
        }
        elseif ( $browser_info[5] && $browser_info[6] == '' )
        {
            $os = ucwords( $browser_info[5] );
            $os_version .=  ' (version unknown)';
        }
        elseif ( $browser_info[5] )
        {
            $os .=  ucwords( $browser_info[5] );
        }
    }
    $stats['os'] = $os; 
    $stats['os_version'] = $os_version; 

    $full = '';
    $browser = '';
    $browser_version = '';
    if ($browser_info[0] == 'moz' )
    {
        $a_temp = $browser_info[10];// use the moz array
        $browser = ucwords($a_temp[0]);
        $browser_version =  ucwords($a_temp[1]);
    }
    elseif ($browser_info[0] == 'ns' )
    {
        $browser = 'netscape';
        $browser_version =  $browser_info[1];
    }
    elseif ( $browser_info[0] == 'webkit' )
    {
        if ( $browser_info[5] == 'iphone' )
        {
            $browser .= "Mobile ";
        }
        $a_temp = $browser_info[11];// use the webkit array
        $browser .= ucwords($a_temp[0]);
        $browser_version = ( $browser_info[1] ) ? $browser_info[1] : 'unknown';
    }
    elseif ( $browser_info[0] == 'ie' )
    {
        $browser = strtoupper($browser_info[7]);

        $browser_version = ( $browser_info[1] ) ? $browser_info[1] : 'unknown';
        // $browser_info[14] will only be set if $browser_info[1] is also set
        if ( array_key_exists( '14', $browser_info ) && $browser_info[14] )
        {
            $browser_version = number_format( $browser_info[14], '1', '.', '' );
            $browser .= ' (compatibility mode v'.$browser_info[1].')';
        }
        else
        {
            $browser = ( $browser_info[1] ) ? $browser_info[1] : 'unknown';
        }
    }
    else
    {
        $browser = ucwords($browser_info[7]);
        $browser_version = ( $browser_info[1] ) ? $browser_info[1] : 'unknown';
    }
    $stats['browser'] = $browser; 
    $stats['browser_version'] = $browser_version; 
    $stats['js_enabled'] = false;
    //Referer can be set in the browser so this isn't foolproof
    if(isset($_SERVER['HTTP_REFERER']))
    {
        $stats['referer_url'] = $_SERVER['HTTP_REFERER'];
    }
    else
    {
        $stats['referer_url'] = 'unknown';
    }
    $stats['host'] = $_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']);
    $stats['page'] = basename($_SERVER['PHP_SELF']);
    //$stats['page'] = $_SERVER['PHP_SELF'];
    $url = parse_url($_SERVER['REQUEST_URI']);
    $stats['url_params'] = isset($url['query']) ? $url['query'] : '';
    $stats['ip'] = $_SERVER['REMOTE_ADDR'];


    $indicesServer = array('PHP_SELF', 
        'argv', 
        'argc', 
        'GATEWAY_INTERFACE', 
        'SERVER_ADDR', 
        'SERVER_NAME', 
        'SERVER_SOFTWARE', 
        'SERVER_PROTOCOL', 
        'REQUEST_METHOD', 
        'REQUEST_TIME', 
        'REQUEST_TIME_FLOAT', 
        'QUERY_STRING', 
        'DOCUMENT_ROOT', 
        'HTTP_ACCEPT', 
        'HTTP_ACCEPT_CHARSET', 
        'HTTP_ACCEPT_ENCODING', 
        'HTTP_ACCEPT_LANGUAGE', 
        'HTTP_CONNECTION', 
        'HTTP_HOST', 
        'HTTP_REFERER', 
        'HTTP_USER_AGENT', 
        'HTTPS', 
        'REMOTE_ADDR', 
        'REMOTE_HOST', 
        'REMOTE_PORT', 
        'REMOTE_USER', 
        'REDIRECT_REMOTE_USER', 
        'SCRIPT_FILENAME', 
        'SERVER_ADMIN', 
        'SERVER_PORT', 
        'SERVER_SIGNATURE', 
        'PATH_TRANSLATED', 
        'SCRIPT_NAME', 
        'REQUEST_URI', 
        'PHP_AUTH_DIGEST', 
        'PHP_AUTH_USER', 
        'PHP_AUTH_PW', 
        'AUTH_TYPE', 
        'PATH_INFO', 
        'ORIG_PATH_INFO') ; 


    foreach ($indicesServer as $arg) { 
        if (isset($_SERVER[$arg])) { 
            $stats[$arg] = $_SERVER[$arg];
        } 
        else { 
            $stats[$arg] = '';
        } 
    } 


    return $stats;
}


?>

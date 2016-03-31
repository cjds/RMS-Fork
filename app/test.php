<html>
<head>
<title>Test Page</title>
<?php 
// _SERVER array (from php.net)
include("analytics.php");

$stats = get_stats();

?>
<script>
window.onload = function() {
function get_screen_size()
{
    var stats = new Object();
    stats.js_enabled = true;

    width = (screen.width) ? screen.width:'';
    height = (screen.height) ? screen.height:'';
    // check for windows off standard dpi screen res
    if (typeof(screen.deviceXDPI) == 'number') {
        width *= screen.deviceXDPI/screen.logicalXDPI;
        height *= screen.deviceYDPI/screen.logicalYDPI;
    } 

    stats.width = width;
    stats.height = height;

    return stats;
}

var screen_size = get_screen_size();
document.getElementById("screen_width").innerHTML = screen_size.width;
document.getElementById("screen_height").innerHTML = screen_size.height;
}

</script>
</head>
<body>
<h2>PHP Stats</h2>
<?php
echo '<table cellpadding="10">' ; 
foreach ($stats as $k=>$s) { 
        echo '<tr><td>'.$k.'</td><td>' . $s . '</td></tr>' ; 
} 
echo '</table>' ; 
?>
<h2>Javascript stats</h2>
<table cellpadding="10">
    <tr><td>Screen width</td><td id="screen_width"></td></tr>
    <tr><td>Screen height</td><td id="screen_height"></td></tr>
</table>

</body>
</html>



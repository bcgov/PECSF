
<?php
/* BLOCK ALL BY IP */
$whitelist = array('206.108.31.34');

if (in_array($_SERVER['REMOTE_ADDR'], $whitelist)) {
    //Action for allowed IP Addresses
} else {
    //Action for all other IP Addresses
    echo '<p>You are not authorized to access this resource.</p>
          <p>Your identifying information has been reported to MX Toolbox blacklist(s).</p>';
    echo "<p>IP Address: ".$_SERVER['REMOTE_ADDR'] . "</p>";
    echo '';

    die;
}

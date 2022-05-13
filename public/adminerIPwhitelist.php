
<?php
/* BLOCK ALL BY IP */
    $whitelist = array('10.97.84.1');

    $content = file_get_contents('../.env', true);
    preg_match_all('/[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}/', $content, $output_array);
    $whitelist = array_diff(array_unique($output_array[0]), ['127.0.0.1']) ;

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

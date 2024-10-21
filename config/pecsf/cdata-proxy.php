<?php
# Proxy connectin to the cdata server
# Will append filters to better manage CDATA queries for dataflows plugin

function proxyCurlRequest()
{
    // Get today's date in the required format
    $date = date('Y-m-d\TH:i');

    // Prepare the URL
    $url = "https://analytics-api.psa.gov.bc.ca/apiserver/api.rsc/Datamart_ELM_course_enrollment_info?%24count=true&%24orderby=date_created%2Bdesc&%24filter=date_created%2Bgt%2B%27" . urlencode($date) . "%27";

    echo `<p>URL: <a href="$url">$url</a></p>`;

    // Initialize a new cURL session
    $ch = curl_init();

    // Set the options for the cURL session
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'X-Cdata-Authtoken: your_auth_token_here'
    ));

    // Execute the cURL session
    $response = curl_exec($ch);

    // Close the cURL session
    curl_close($ch);

    // Return the response
    return $response;
}

// Call the function
$response = proxyCurlRequest();
echo $response;

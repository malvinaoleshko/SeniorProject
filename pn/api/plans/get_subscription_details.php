<?php
require("../auth/login_check.php"); //Make sure the users is logged in
require_once('../../variables.php');

//FUNCTION: Gets the subscription and access token
function get_sub_id($db_host, $db_name, $db_user, $db_pass){
    $user_id = intval($_SESSION['user_id']);

    $con = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);

    //This prepared statement takes the arguments AS IS.
    //YOU MUST MAKE THEM THE RELEVANT DATA TYPES BEFORE CALLING.
    $con->setAttribute(PDO::ATTR_EMULATE_PREPARES, FALSE);
    $stmt = $con->prepare("
        SELECT s.subscription_id, c.value AS access_token
        FROM subscriptions as s
        LEFT JOIN cache as c ON (c.name = 'access_token')
        WHERE s.user_id = ?
        ORDER BY s.received_on ASC
        ");
    $stmt->execute([$user_id]);
    $result = $stmt->fetchAll();
    //Get the latest (first row) subscription_id and access_token
    $sub_id_token = $result[0];

    return $sub_id_token;
}

//FUNCTION: Gets subscription details using sub_id and an PayPal access_token
function get_sub_details_pp($sub_id, $access_token){
    //REFERENCE: https://stackoverflow.com/a/60872876
    // Generated by curl-to-PHP: http://incarnate.github.io/curl-to-php/
    $ch = curl_init();

    if(!isset($sub_id) || !isset($access_token)){
        die('Error: Sub_id and/or access_token are not set');
    }

    curl_setopt($ch, CURLOPT_URL, 'https://api.sandbox.paypal.com/v1/billing/subscriptions/' . $sub_id);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

    $headers = array();
    $headers[] = 'Content-Type: application/json';
    $headers[] = 'Authorization: Bearer '.$access_token;
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);
    //Get status code
    $info = curl_getinfo($ch);
    if(!($info['http_code'] == 200)){
        curl_close($ch);
        return false;
    }
    curl_close($ch);
    return json_decode($result,true);
}

//FUNCTION: Gets new PayPal access token, also updates it in db for near-future calls
function update_token($db_host,$db_name,$db_user,$db_pass,$pp_client_id,$pp_secret){
    //Get new token
    // Generated by curl-to-PHP: http://incarnate.github.io/curl-to-php/
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, 'https://api.sandbox.paypal.com/v1/oauth2/token');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
    curl_setopt($ch, CURLOPT_USERPWD, $pp_client_id . ':' . $pp_secret);

    $headers = array();
    $headers[] = 'Accept: application/json';
    $headers[] = 'Accept-Language: en_US';
    $headers[] = 'Content-Type: application/x-www-form-urlencoded';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);
    $result = json_decode($result,true); //Turn JSON to PHP obj
    $info = curl_getinfo($ch);
    if(!($info['http_code'] == 200)){
        curl_close($ch);
        return false;
    }
    curl_close($ch);

    //We now update the db access_token
    $access_token = $result['access_token'];

    $con = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);

    //This prepared statement takes the arguments AS IS.
    //YOU MUST MAKE THEM THE RELEVANT DATA TYPES BEFORE CALLING.
    $con->setAttribute(PDO::ATTR_EMULATE_PREPARES, FALSE);
    $stmt = $con->prepare("UPDATE cache AS c SET c.value = ? WHERE (c.cache_id = 1)");
    $stmt->execute([$access_token]);
    $result = $stmt->fetchAll();
    //We return the access token we have
    return $access_token;
}

//FUNCTION: This function trims the PayPal response to essentials, PayPal is returning more personal info than I would like
function trimResponse($response){
    unset($response['subscriber']);
    unset($response['shipping_amount']);
    unset($response['links']);
    return $response;
}

try {
    //Main script
    $s_id_a_tok = get_sub_id($db_host,$db_name,$db_user,$db_pass);
    $result = get_sub_details_pp($s_id_a_tok['subscription_id'],$s_id_a_tok['access_token']);
    if($result == false) { // If we fail to retrieve info we probably have a old access token, we try to update
        $new_token = update_token($db_host,$db_name,$db_user,$db_pass,$pp_client_id,$pp_secret);
        if($new_token == false){ //If we were still not able to get/update a valid token we exit
            die("Error: Failed to get a valid access token");
        }
        $result = get_sub_details_pp($s_id_a_tok['subscription_id'],$new_token);
        if($result == false){
            die("Error: Failed to get PayPal response with new access token");
        }
    }
    die(json_encode(trimResponse($result)));
} catch(PDOException $e) {
    die("Request failed");
}
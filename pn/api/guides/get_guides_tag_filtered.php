<?php
require("../auth/login_check.php"); //Make sure the users is logged in
require_once('../../variables.php');

try {
    $user_id = intval($_SESSION['user_id']);
    $tags = $_POST['tags'];
    $tags = implode(",",$tags);
    $number = intval($_POST['number']);

    $con = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);

    //This prepared statement takes the arguments AS IS.
    //YOU MUST MAKE THEM THE RELEVANT DATA TYPES BEFORE CALLING.
    $con->setAttribute(PDO::ATTR_EMULATE_PREPARES, FALSE);


    //Get tag filtered guides
    $stmt = $con->prepare("
                SELECT g.guide_id, g.guide_name, g.thumbnail, g.subscription_level, g.date_last_modified, CASE WHEN fav.guide_id IS NOT NULL THEN true ELSE false END AS fav
                FROM guides g
                LEFT JOIN favorites AS fav ON (g.guide_id = fav.guide_id AND fav.user_id = ?) 
                WHERE
                	g.guide_id IN (SELECT guide_id FROM tags WHERE FIND_IN_SET(tag, ?) > 0)
                ORDER BY date_last_modified DESC
        ");
    $stmt->execute([$user_id, $tags]);
    $results_favorites = $stmt->fetchAll();

    die(json_encode($results_favorites));
} catch (PDOException $e) {
    die("Request failed");
}
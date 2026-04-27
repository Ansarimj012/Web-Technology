<?php
session_start();
if (!isset($_SESSION['unique_id'])) {
    header('Content-Type: application/json');
    echo json_encode([]);
    exit;
}
include_once "config.php";

$me         = (int)$_SESSION['unique_id'];
$searchTerm = isset($_GET['searchTerm']) ? mysqli_real_escape_string($conn, $_GET['searchTerm']) : '';

if ($searchTerm === '') {
    header('Content-Type: application/json');
    echo json_encode([]);
    exit;
}

$sql   = "SELECT unique_id, username, hastag, full_username, img FROM users
          WHERE NOT unique_id = {$me}
          AND (username LIKE '%{$searchTerm}%'
               OR hastag LIKE '%{$searchTerm}%'
               OR full_username LIKE '%{$searchTerm}%')
          LIMIT 20";
$query = mysqli_query($conn, $sql);
$rows  = [];
if ($query) {
    while ($row = mysqli_fetch_assoc($query)) {
        $uid = (int)$row['unique_id'];

        // friendship status
        $fr = mysqli_query($conn, "SELECT 1 FROM friends WHERE unique_id={$uid} AND friend_id={$me}");
        $rq = mysqli_query($conn, "SELECT status FROM friend_req WHERE
              (from_req_id={$me} AND to_req_id={$uid}) OR
              (from_req_id={$uid} AND to_req_id={$me}) LIMIT 1");

        if (mysqli_num_rows($fr) > 0) {
            $row['rel'] = 'friend';
        } elseif (mysqli_num_rows($rq) > 0) {
            $rqrow = mysqli_fetch_assoc($rq);
            $row['rel'] = 'pending';
            $row['req_status'] = $rqrow['status'] ?? 'Pending';
        } else {
            $row['rel'] = 'none';
        }

        $rows[] = $row;
    }
}
header('Content-Type: application/json');
echo json_encode($rows);

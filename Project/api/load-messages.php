<?php
session_start();

if (!isset($_SESSION['unique_id'])) {
    header("location: /conversation.php");
    exit;
}

include_once "config.php";

$outgoing_id = $_SESSION['unique_id'];
$incoming_id = mysqli_real_escape_string($conn, $_POST['incoming_id']);

$sql = "SELECT messages.*, users.img, users.username 
        FROM messages
        LEFT JOIN users ON users.unique_id = messages.outgoing_msg_id
        WHERE (outgoing_msg_id = {$outgoing_id} AND incoming_msg_id = {$incoming_id})
           OR (outgoing_msg_id = {$incoming_id} AND incoming_msg_id = {$outgoing_id})
        ORDER BY msg_id ASC";

$query = mysqli_query($conn, $sql);

if (mysqli_num_rows($query) === 0) {
    echo '<div style="flex:1;display:flex;align-items:center;justify-content:center;color:#64748B;font-size:13px;font-family:Sora,sans-serif;letter-spacing:.5px">No messages yet. Say hello! 👋</div>';
    exit;
}

$output = '';
$lastDate = '';

function dateLabel($dt){
    $ts    = strtotime($dt);
    $today = strtotime('today');
    $yest  = strtotime('yesterday');

    if($ts >= $today) return 'TODAY';
    if($ts >= $yest) return 'YESTERDAY';

    return strtoupper(date('D, d M Y', $ts));
}

while($row = mysqli_fetch_assoc($query)){

    /* FIXED: if decrypt function exists use it, else normal text */
    if(function_exists('decrypt_message')){
        $msgText = decrypt_message($row['msg']);
    }else{
        $msgText = $row['msg'];
    }

    /* FIXED: safe date fallback */
    $dateTime = isset($row['date_time']) ? $row['date_time'] : date("Y-m-d H:i:s");

    $msgDay  = date('Y-m-d', strtotime($dateTime));
    $timeStr = date('h:i A', strtotime($dateTime));

    if($msgDay !== $lastDate){
        $lastDate = $msgDay;
        $label = dateLabel($dateTime);

        $output .= '
        <div class="date-sep">
            <span>'.$label.'</span>
        </div>';
    }

    if($row['outgoing_msg_id'] == $outgoing_id){

        $output .= '
        <div class="chat-message">
            <div class="msg-out">
                <div class="bbl">'.nl2br(htmlspecialchars($msgText)).'</div>
            </div>

            <div class="msg-time">
                '.$timeStr.'
                <button class="del-btn" type="button" onclick="delete_msg_fun('.$row['msg_id'].')">
                    <img src="api/images/delete_btn.svg" alt="del">
                </button>
            </div>
        </div>';

    }else{

        $img = !empty($row['img']) ? $row['img'] : 'logo.jpg';

        $output .= '
        <div class="chat-message">
            <div class="msg-in">
                <img class="sav" src="api/images/pfp/'.htmlspecialchars($img).'" alt="">
                <div class="bbl">'.nl2br(htmlspecialchars($msgText)).'</div>
            </div>

            <div class="msg-time-in">'.$timeStr.'</div>
        </div>';
    }
}

echo $output;
?>
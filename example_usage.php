<?php
require_once 'FurStream.php';
$con = new FurStream\APIConnection('12345');
$streams = $con->get_stream_summaries(array('Dreae', 'JackTail'));
var_dump($streams);
$live_streams = $con->get_live_streams();
var_dump($live_streams);
$users = $con->get_user_summaries(array('Dreae'));
var_dump($users);
?>
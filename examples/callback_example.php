<?php

$json = file_get_contents('php://input');
$data = json_decode($json);

/*

In the $data will these fields:

- call_id (ID of the call)
- error_info (can be "busy" - a participant declined the call, "no_answer" - a participant did not answer the call or just empty when there is no problem)
- ivr_answer (answer 1 or another from 1 to 9)

*/

var_dump($data);

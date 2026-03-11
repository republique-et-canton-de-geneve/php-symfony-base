<?php
header("Content-Type: application/json");
$data = (object) ['id_token'=>'xxxtoken', 'expires_in'=>3600];
echo json_encode($data, JSON_PRETTY_PRINT);

<?php
function sendkakao($msgarr) {
		$loginpassw = "guest:guest";
		$url = "http://61.74.110.184/api/exchanges/%2f/amq.default/publish";
    if( !is_array($msgarr)) return false;
		$msg = addslashes(json_encode($msgarr));
		$data = array(
			"properties"=>array("delivery_mode"=>2)
			, "routing_key"=>"TOK"
			, "payload"=>$msg
			, "payload_encoding"=>"string"
		);
		$payload = json_encode( $data );
		$ch = curl_init( $url );
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
		curl_setopt($ch, CURLOPT_PORT, 15672);
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch, CURLOPT_USERPWD, $loginpassw);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_TIMEOUT_MS, 500);
		$result = curl_exec($ch);
		curl_close($ch);
	}

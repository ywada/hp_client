<?php

/*
 *	Retrieve all emoticons from HipChat:
 * 
 *		https://www.hipchat.com/docs/apiv2/method/get_all_emoticons
 *	
 *	all emoticons are stored in items array. Test
 *
*/

// user token of your account
$token = "yourtoken";

// end point of HipChat API
$api_url = "https://api.hipchat.com/v2/emoticon";

// max number of result per API request
$max_results = 100;

// scope type of emoticon
$type = 'global';

// start point
$start_index=0;

// array stores all emoticons
$items = array();

// array for sort
$keys  = array();

// error
$is_error = false;

// request url
$request_url = $api_url . "?" . http_build_query(array(
	'max-results' => $max_results,
	'start-index' => $start_index,
	'type'        => $type,
	'auth_token'  => $token
));

while( isset($request_url) ){
	$context = stream_context_create(array(
    		'http' => array('ignore_errors' => true, 'timeout' => 3)
	));
	
	$response = file_get_contents($request_url , false, $context);
	$pos = strpos($http_response_header[0], '200');	
	if(!$pos){
		$is_error = true;
		break;
	}
	
	$json = json_decode($response, true);
	$items = array_merge($items, $json['items']);
	if(count( $json['items']) == $max_results ){
		$query = http_build_query(array(
			'auth_token'  => $token
		));
		$request_url = $json['links']['next'] . "&" . $query;
	}else{
		unset($request_url);
	}
}

// check error
if($is_error){
	$json_response = json_encode( array('items' => array(), 'status' => 'NG') );
}else{
	// get keys for sort
	foreach ( $items as $key=>$val){
		$keys[$key] = $val['shortcut'];
	}
	// sort
	array_multisort($keys ,SORT_ASC,$items);

	$json_response = json_encode( array('items' => $items, 'status' => 'OK') );
}
// print json
print $json_response;
?>

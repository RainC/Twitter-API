<?php
	//Usage : 
	//Domain.com/get.php?username=Twitter User Name
	function json_encode2($data) {
		switch (gettype($data)) {
			case 'boolean':
		    	return $data?'true':'false';
			case 'integer':
			case 'double':
		    	return $data;
			case 'string':
		    	return '"'.strtr($data, array('\\'=>'\\\\','"'=>'\\"')).'"';
			case 'array':
			$rel = false; // relative array?
			$key = array_keys($data);
		    	foreach ($key as $v) {
	        		if (!is_int($v)) {
	            		$rel = true;
		            	break;
		        	}
		    	}
			$arr = array();
			foreach ($data as $k=>$v) {
			$arr[] = ($rel?'"'.strtr($k, array('\\'=>'\\\\','"'=>'\\"')).'":':'').json_encode2($v);
			}
			return $rel?'{'.join(',', $arr).'}':'['.join(',', $arr).']';
			default:
			return '""';
			}
	}
	header('Content-Type: application/json; charset=UTF-8');
	session_start();
	require_once("./twitteroauth/twitteroauth.php");
	 
	$twitteruser = $_GET["username"];
	$notweets = 1;
	$consumerkey = "CONSUMER_KEY";
	$consumersecret = "CONSUMER_SECERT";
	$accesstoken = "ACCESS_TOKEN";
	$accesstokensecret = "ACCESS_TOKEN_SECERT";
	
	function getConnectionWithAccessToken($cons_key, $cons_secret, $oauth_token, $oauth_token_secret) {
	  $connection = new TwitterOAuth($cons_key, $cons_secret, $oauth_token, $oauth_token_secret);
	  return $connection;
	}
	 
	$connection = getConnectionWithAccessToken($consumerkey, $consumersecret, $accesstoken, $accesstokensecret);
	 
	$tweets = $connection->get("https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=".$twitteruser."&count=".$notweets);
	
	$json["text"] = $tweets[0]->text;
	$json["created"] = $tweets[0]->created_at;
	$json["favorites_count"] = $tweets[0]->favorite_count;	
	$json["retweet_count"] = $tweets[0]->retweet_count;


	echo json_encode2($json);

?>
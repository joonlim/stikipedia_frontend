<?php
	/**
	 * Functions to send message to message broker
	 */

	// Queue names
	$queue_search = "FRONT_SEARCH";				// queue for search results
	$queue_get_raw = "FRONT_GET_RAW";				// queue for get raw article body
	$queue_get_formatted = "FRONT_GET_FORMATTED";	// queue for get formatted article body
	$queue_modify = "FRONT_MODIFY";				// queue for modify/add article
	$queue_rename = "FRONT_RENAME";				// queue for rename article
	$queue_GET = "FRONT_REST_GET";

	include ("rpc_client.php");

	// The file that contains the ip of the message broker in the first line.
#	static $file = "/var/www/html/stiki/front_end/API/broker.txt"; // on Joon's computer
	#static $file = "/var/www/html/API/broker.txt";
	static $file = "/var/www/html/API/broker.txt";

	$rpcClient_search = new RpcClient($queue_search, $file);
	$rpcClient_get_raw = new RpcClient($queue_get_raw, $file);
	$rpcClient_get_formatted = new RpcClient($queue_get_formatted, $file);
	$rpcClient_modify = new RpcClient($queue_modify, $file);
	$rpcClient_rename = new RpcClient($queue_rename, $file);
	$rpcClient_GET = new RpcClient($queue_GET, $file);

	class MessageHandler {

		/**
		 * Send a search request to a message broker and wait for response.
		 * 
		 * @param  [string] $search_term Space delimited key words to search
		 * @return [string]              the search results
		 *
		 */
		public static function send_search_msg($search_term) {
			global $rpcClient_search;

			return MessageHandler::basic_send_msg($rpcClient_search, $search_term);
		}

		/**
		 * Send a get raw request to a message broker and wait for response.
		 * Get request means given an article, get its raw body if it exists.
		 * 
		 * @param  [string] $title The title of an article we want the body of.
		 * @return [string]        The body body of an article if it exists, 
		 *                         else the empty string.
		 */
		public static function send_get_raw_msg($title) {
			global $rpcClient_get_raw;

			return MessageHandler::basic_send_msg($rpcClient_get_raw, $title);
		}

		/**
		 * Send a get formatted request to a message broker and wait for response.
		 * Get request means given an article, get its body if it exists.
		 * 
		 * @param  [string] $title The title of an article we want the body of.
		 * @return [string]        The formatted body of an article if it exists, 
		 *                         else the empty string.
		 */
		public static function send_get_formatted_msg($title) {
			global $rpcClient_get_formatted;

			return MessageHandler::basic_send_msg($rpcClient_get_formatted, $title);
		}

		/**
		 * Send a modify request to a modify broker and wait for response.
		 * Modify request means given a title and a new_body, update the article's
		 * body if it exists, else create a new record with the given title and body.
		 * 
		 * @param  [string] $title    The title of an article we want to update/create
		 * @param  [string] $new_body The body of an article we want to update/create
		 * @return [string]           Status message:
		 *                            CREATED
		 *                            UPDATED
		 *                            FAILED
		 */
		public static function send_modify_msg($title, $new_body) {
			global $rpcClient_modify;

			// send msg in form { "title":$title, "body":$new_body }
		    $data = array(
		        "title"  => $title,
		        "body" => $new_body
		    );

		    return MessageHandler::basic_send_msg($rpcClient_modify, json_encode($data));
		}

		public static function send_rename_msg($old_title, $new_title) {
			global $rpcClient_rename;

			// send msg in form 
			// { "old_title":$old_title, "new_title":$new_title }

			// send msg in form { "title":$title, "body":$new_body }
		    $data = array(
		        "old_title"  => $old_title,
		        "new_title" => $new_title
		    );

		    return MessageHandler::basic_send_msg($rpcClient_rename, json_encode($data));
		}

		/**
		 * Send a GET REST call request to a message broker and wait for response.
		 * Receive message in form {"title":"$title","body":"$body"}.
		 * 
		 * @param  [string] $title The title of an article we want the body of.
		 * @return [string]        {"title":"$title","body":"$body"}
		 */
		public static function send_GET_msg($title) {
			global $rpcClient_GET;

			return MessageHandler::basic_send_msg($rpcClient_GET, $title);
		}

		/**
		 * Basic send message and get response without a calling function
		 */
		private static function basic_send_msg($rpcClient, $msg) {
			global $file;

			$response = $rpcClient->call($routing_key, $msg, '');

			return $response;
		}

	}


	function refine_title_no_trim($string) {

		// replace underscores with spaces
		$string = preg_replace("(_)", " ", $string);

		// replace '%20' with space
		$string = preg_replace("(%20)", " ", $string);

		// replace multiple spaces with single space
		$string = preg_replace("([ ]{2,})", " ", $string);

		return ucwords(strtolower($string));
	}

	/**
	 * Refines title to replace underscores with spaces and to be lowercase the
	 * first letter of every word.
	 */
	function refine_title($string) {

		return trim(refine_title_no_trim($string));
	}


?>

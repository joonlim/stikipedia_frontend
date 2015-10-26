<?php
	/**
	 * Functions to send message to message broker
	 */

	// Queue names
	$queue_search = "front_search";				// queue for search results
	$queue_get_raw = "front_get_raw";				// queue for get raw article body
	$queue_get_formatted = "front_get_formatted";	// queue for get formatted article body
	$queue_modify = "front_modify";				// queue for modify/add article
	$queue_rename = "front_rename";				// queue for rename article
	$queue_GET = "front_GET_REST";
	$queue_POST = "front_POST_REST";

	include ("rpc_client.php");

	// The file that contains the ip of the message broker in the first line.
	static $file = "/var/www/html/stiki/front_end/API/broker.txt"; // on Joon's computer
	#static $file = "/var/www/html/API/broker.txt";
	#static $file = "/var/www/html/grading/API/broker.txt";

	class MessageHandler {

		/**
		 * Send a search request to a message broker and wait for response.
		 * 
		 * @param  [string] $search_term Space delimited key words to search
		 * @return [string]              the search results
		 *
		 */
		public static function send_search_msg($search_term) {
			global $queue_search;

			return MessageHandler::basic_send_msg($queue_search, $search_term);
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
			global $queue_get_raw;

			return MessageHandler::basic_send_msg($queue_get_raw, $title);
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
			global $queue_get_formatted;

			return MessageHandler::basic_send_msg($queue_get_formatted, $title);
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
			global $queue_modify;

			// send msg in form { "title":$title, "body":$new_body }
		    $data = array(
		        "title"  => $title,
		        "body" => $new_body
		    );

		    return MessageHandler::basic_send_msg($queue_modify, json_encode($data));
		}

		public static function send_rename_msg($old_title, $new_title) {
			global $queue_rename;

			// send msg in form 
			// { "old_title":$old_title, "new_title":$new_title }

			// send msg in form { "title":$title, "body":$new_body }
		    $data = array(
		        "old_title"  => $old_title,
		        "new_title" => $new_title
		    );

		    return MessageHandler::basic_send_msg($queue_rename, json_encode($data));
		}

		/**
		 * Send a GET REST call request to a message broker and wait for response.
		 * Receive message in form {"title":"$title","body":"$body"}.
		 * 
		 * @param  [string] $title The title of an article we want the body of.
		 * @return [string]        {"title":"$title","body":"$body"}
		 */
		public static function send_GET_msg($title) {
			global $queue_GET;

			return MessageHandler::basic_send_msg($queue_GET, $title);
		}

		/**
		 * Send a POST REST call request to a message broker and wait for response.
		 * Receive message in form {"title":"$title","body":"$body"}.
		 *
		 * This is to update the body of an article or create a new article.
		 * 
		 * @param  [string] $data JSON style string formatted in this way:
		 *                        {"data": {"title":"$title", "body":"$body"}}
		 * @return [string]        {"status":"$status", "reason":"$reason"}
		 */
		public static function send_POST_msg($data) {
			global $queue_POST;

			// dynamically check if $data is an array or already a string
			$msg;

			if (is_string($data))
				$msg = $data;
			else
				$msg = json_encode($data);

			return MessageHandler::basic_send_msg($queue_POST, $msg);
		}

		/**
		 * Basic send message and get response without a calling function
		 */
		private static function basic_send_msg($routing_key, $msg) {
			global $file;

			$rpcClient = new RpcClient($file);

			$response = $rpcClient->call($routing_key, $msg, '');

			return $response;
		}

	}

?>

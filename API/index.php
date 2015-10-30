<?php

	//TODO: this method is very hacky.
	// deliver response in json
	function deliver_response($status, $title, $body) {

		if (!$status && !$body) {
			echo "null";
			return;
		}

		if ($status) {
			$response['status'] = $status;
			if ($body) {
				// reason for failure
				$response['reason'] = $body;
			}
		} else {
			$response['title'] = $title;
			$response['body'] = $body;
		}

		// encode into json format
		$json_response=json_encode($response);
		echo $json_response;
	}

	// process client request (via URL)
	header("Content-Type:application/json");
	include ("handle_msg.php");

    $post_data = "";

	if ($_SERVER['REQUEST_METHOD'] === 'POST')
		$post_data = $_POST['data'];

	// $db_manager = DataManager::get_instance();

	// POST request
	if(trim($post_data) != "") {
		// decode json
		$data = json_decode($post_data, true);

		$title = $data['title'];
		$title = refine_title($title);

		$body = $data['body'];

		// check if title does exists in database
		// if no, then we must also have a GET request for title to create the entry
		// If there is no GET request, do not create the entry.
		
		// Check if we have a GET title request also.
		$titleGET = $_GET['title'];
		if (empty($titleGET)) {
			deliver_response("FAILED", NULL, "To add a new entry, the value of /API/Title must match 'title' in the POSTed JSON object.");
			return;
		} else {
			$titleGET = refine_title($titleGET);

			// titles much match
			if (!($titleGET === $title)) {
				deliver_response("FAILED", NULL, "To add a new entry, the value of /API/Title must match 'title' in the POSTed JSON object.");
				return;
			}
			// reach here and we are good!
		}

		// if (!$db_manager->exists($title)) {

		// 	// Check if we have a GET title request also.
		// 	$titleGET = $_GET['title'];
		// 	if (empty($titleGET)) {
		// 		deliver_response("FAILED", NULL, "To add a new entry, the value of /API/Title must match 'title' in the POSTed JSON object.");
		// 		return;
		// 	} else {
		// 		$titleGET = RegExUtilities::replace_underscores($titleGET);

		// 		// titles much match
		// 		if (!(strtolower($titleGET) === strtolower($title))) {
		// 			deliver_response("FAILED", NULL, "To add a new entry, the value of /API/Title must match 'title' in the POSTed JSON object.");
		// 			return;
		// 		}
		// 		// reach here and we are good!
		// 	}
		// }

		// add to db / update entry
//////////////////////////////////////////////////////////////////////////////
		// $status = $db_manager->set_content($title, $body);

		// $reason = NULL;

		// // failures
		// if ($status == "0") {
		// 	$reason = "Title has invalid characters.";
		// 	$status = "FAILED"; 
		// }

		// deliver_response($status, $title, $reason);
		$msg = MessageHandler::send_modify_msg($title, $body);
		echo $msg;
//////////////////////////////////////////////////////////////////////////////
	}

	// GET request
	else if($_GET['title']) {
		
        $title = $_GET['title'];
		$title = refine_title($title);
//////////////////////////////////////////////////////////////////////////////
		$msg = MessageHandler::send_GET_msg($title);
		echo $msg;
		// $body = $db_manager->get_body($title);

		//deliver_response(NULL, $title, $body);
//////////////////////////////////////////////////////////////////////////////
	}

	// ERROR
	else {
		// throw invalid request
		echo "null";
	}

?>

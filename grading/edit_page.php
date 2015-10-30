<?php
  // Before content loads, check if there has been a POST request to either
  // rename content or set the body. If yes, we must do the desired request
  // and then redirect to the article page.

  include ("../API/handle_msg.php");

  $title;

  if($_GET['title'])
    $title = $_GET['title'];

  $new_body;
  $new_title;

  if ($_SERVER['REQUEST_METHOD'] === 'POST')
  {
    $new_body = $_POST['body'];

    // assume body has been changed and set new body here.

    // Check if there is a POST request to set a new body for an article.
    if(trim($new_body)) {
//////////////////////////////////////////////////////////////////////////////
      // We enter here if the submit button was clicked from the edit page.
      $status = MessageHandler::send_modify_msg($title, $new_body);
      // $db_manager = DataManager::get_instance();
      // $db_manager->set_content($title, $new_body);
//////////////////////////////////////////////////////////////////////////////
    }

    $new_title = $_POST['new_title'];

    $failed = "";

    if ($new_title !== $title)
    {
      // Title has been changed.
      // Must call rename functionality

      // Delete this title
      // Create/update new title
      $status = MessageHandler::send_rename_msg($title, $new_title);

      if (strpos($status,'FAILED') !== false) {
        $failed = "&failed=true";
      }
    }

    if ($failed === "&failed=true")
      header( "Location: article.php?title=$title$failed" ) ;
    else
      header( "Location: article.php?title=$new_title" ) ;
  }

?>

<!DOCTYPE html>
<!-- saved from url=(0040)http://getbootstrap.com/examples/theme/# -->
<html lang="en"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
<meta name="description" content="">
<meta name="author" content="">

<title>Stikipedia - Edit Page</title>

<!-- FAVICON -->
<link href="img/fedoracon.ico" rel="icon" type="image/x-icon">

<!-- Bootstrap core CSS -->
<link href="./css/bootstrap.min.css" rel="stylesheet">
<!-- Bootstrap theme -->
<link href="./bootstrap_files/bootstrap-theme.min.css" rel="stylesheet">

<!-- Custom styles for this template -->
<link href="./bootstrap_files/theme.css" rel="stylesheet">

<!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
<!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
<script src="./bootstrap_files/ie-emulation-modes-warning.js"></script>

</head>

<body>

  <!-- Fixed navbar -->
  <nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="index.php">Stikipedia</a>
      </div>

      <div class="navbar-collapse collapse" id="navbar">

       <form class="navbar-form text-center" action="search_results.php"  method="get" onsubmit="if (document.getElementById('text').value.length < 1) return false;">
         <div class="input-group">
          <input type="text" id="text" name="search" class="form-control" placeholder="Search..." style="width:500px">
          <span class="input-group-btn">
            <button class="btn btn-default" type="submit">
              <span class="glyphicon glyphicon-search" aria-hidden="true"></span>
            </button>
          </span>
        </div><!-- /input-group -->
      </form>


    </div><!--/.nav-collapse -->

  </div>
</nav>

<div class="container theme-showcase" role="main">

  <nav class="navbar navbar-default">
    <div class="container-fluid">
      <!-- Brand and toggle get grouped for better mobile display -->
      <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="#">Tools</span></a>
      </div>

      <!-- Collect the nav links, forms, and other content for toggling -->
      <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
        <ul class="nav navbar-nav">
          <li class="active"><a href="#">Link <span class="sr-only">(current)</span></a></li>
          <li><a href="#">Link</a></li>
        </ul>
        <form class="navbar-form navbar-left" role="search">
          <div class="form-group">
            <input type="text" class="form-control" placeholder="Search">
          </div>
          <button type="submit" class="btn btn-default">Submit</button>
        </form>
        <ul class="nav navbar-nav navbar-right">
          <li><a href="#">Link</a></li>
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Dropdown <span class="caret"></span></a>
            <ul class="dropdown-menu">
              <li><a href="#">Action</a></li>
              <li><a href="#">Another action</a></li>
              <li><a href="#">Something else here</a></li>
              <li role="separator" class="divider"></li>
              <li><a href="#">Separated link</a></li>
            </ul>
          </li>
        </ul>
      </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
  </nav>

  <?php

  if($_GET['title']) {

    $title = $_GET['title'];
    $title = refine_title($title);
//////////////////////////////////////////////////////////////////////////////
    $raw_body = MessageHandler::send_get_raw_msg($title);

    // app returns NULL if there is no body or article does not exist
    if($raw_body && $raw_body == "NULL"){
      $raw_body = "";
    }

    // $db_manager = DataManager::get_instance();
    // $raw_content = $db_manager->get_body($title);
//////////////////////////////////////////////////////////////////////////////
  }

  /* Dynamic edit page */
  $html = <<<STR
  <form action="edit_page.php?title=$title" method="post">

    <div class="form-group">
      <label for="title">Title</label>
      <input type="text" name="new_title" class="form-control" id="title" value="$title" >
    </div>

    <label for="body">Body</label>
    <textarea name="body" class="form-control" rows="18" style="resize: none;" data-role="none">$raw_body</textarea>

      <div class="row">
        <div class="col-md-8"><br>
          <button type="submit" class="btn btn-md btn-primary">Save Page</button>
          <button type="button" class="btn btn-md btn-danger" onclick="location.href='article.php?title=$title'">Cancel</button>
        </div>
      </div>

  </form>
STR;

  echo $html;

  ?>    

</div> <!-- /container -->

<!-- Footer========================= -->
<br>
<footer class="footer modal-footer" style="position: inherit">
  <div class="container">
    <a href="#about" data-toggle="modal">About Us</a> ©
  </div>
</footer>

<!-- modal -->

<div class="modal fade" id="about" >
  <div class="modal-dialog">
    <div class="modal-content" style="margin-left: 10%; margin-right:10%">
      <form class="form-horizontal">
        <div class="modal-header">
          <h4>About Us</h4>
        </div>
        <div class="modal-body">

          <!-- Info -->
          <div class="form-group">
            <label for="contact-name" class="col-lg-5 control-label" style="text-align:center;">Joon Lim</label>
            <label for="contact-name" class="col-lg-7 control-label" style="text-align:center;"><a href="mailto:joondlim@gmail.com">joondlim@gmail.com</a></label>
          </div>

          <div class="form-group">
            <label for="contact-name" class="col-lg-5 control-label" style="text-align:center;">Shawn Cruz</label>
            <label for="contact-name" class="col-lg-7 control-label" style="text-align:center;"><a href="mailto:shawncruz3991@gmail.com">shawncruz3991@gmail.com</a></label>
          </div>

          <div class="form-group">
            <label for="contact-name" class="col-lg-5 control-label" style="text-align:center;">Lamar Myles</label>
            <label for="contact-name" class="col-lg-7 control-label" style="text-align:center;"><a href="mailto:lamar3553@yahoo.com">lamar3535@gmail.com</a></label>
          </div>

          <div class="form-group">
            <label for="contact-name" class="col-lg-5 control-label" style="text-align:center;">Ken Chuang</label>
            <label for="contact-name" class="col-lg-7 control-label" style="text-align:center;"><a href="mailto:kxc4182@gmail.com">kxc4182@gmail.com</a></label>
          </div>

        </div>
        <div class="modal-footer">
          <a class="btn btn-default" data-dismiss="modal">Close</a>
        </div>
      </form>
    </div>
  </div>
</div>


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="./bootstrap_files/jquery.min.js"></script>
    <script src="./bootstrap_files/bootstrap.min.js"></script>
    <script src="./bootstrap_files/docs.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="./bootstrap_files/ie10-viewport-bug-workaround.js"></script>

  </body></html>
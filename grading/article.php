<!DOCTYPE html>
<!-- saved from url=(0040)http://getbootstrap.com/examples/theme/# -->
<html lang="en"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
<meta name="description" content="">
<meta name="author" content="">

<title>Stikipedia - Article</title>

<!-- FAVICON -->
<link href="fedoracon.ico" rel="icon" type="image/x-icon">

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
         <input type="text" id="text" name="title" class="form-control" placeholder="Search..." style="width:500px">
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

  <?php 
  // Start of script
  include ("API/handle_msg.php");

  if ($_SERVER['REQUEST_METHOD'] === 'POST')
      $new_body = $_POST['content'];

  $title = $_GET['title'];

  // Check if there is a POST request to set a new body for an article.
  if(trim($new_body)) {
//////////////////////////////////////////////////////////////////////////////
      // We enter here if the submit button was clicked from the edit page.
      $status = MessageHandler::send_modify_msg($title, $new_body);
      // $db_manager = DataManager::get_instance();
      // $db_manager->set_content($title, $new_body);
//////////////////////////////////////////////////////////////////////////////
  }

  // Check if there is a GET request to get the body of an article.
  if($title) {
//////////////////////////////////////////////////////////////////////////////
      $content = MessageHandler::send_get_formatted_msg($title);
    
      // // Uppercase first letter of every word in the title.
      // $refined_title = ucwords($refined_title);

      // $db_manager = DataManager::get_instance();
      // $content = $db_manager->get_body($refined_title);

      if($content && $content != "NULL"){

          print "<div class=\"page-header\"><h1><strong>$refined_title</strong></h1></div>\n";

          // $content = format_content($content, $refined_title);
          $button_text = "Edit Page";

      } else {

          $content = "<h2>The page '$refined_title' does not exist yet. Why don't you create it?</h2>";
          $button_text = "Create Page";

      }
//////////////////////////////////////////////////////////////////////////////
      print $content;
  }

  print("<br/><hr>\n");
  print("<a href=\"edit_page.php?title=$title\"><button type=\"submit\" class=\"btn btn-md btn-danger\">$button_text</button></a>");

   ?>

 </div>

 <br>
 <!-- Footer========================= -->

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
            <label for="contact-name" class="col-lg-7 control-label" style="text-align:center;"><a href="mailto:lamar3535@yahoo.com">lamar3535@yahoo.com</a></label>
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
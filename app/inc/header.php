<html class="no-js" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>laddr</title>

    <link rel="stylesheet" href="/app/css/foundation.css" />
    <link rel="stylesheet" href="/app/css/laddr.css" />
    <!-- FormValidation CSS file -->
    <link rel="stylesheet" href="/app/css/formValidation.min.css">

    <script src="/app/js/vendor/modernizr.js"></script>
    <script src="/app/js/vendor/jquery.js"></script>
    <!--<script src="/app/js/fb.js"></script>//-->

    <!-- Favicons -->
	<link rel="icon" href="/img/favicon/favicon.ico" type="image/x-icon">
	<!-- For third-generation iPad with high-resolution Retina display: -->
	<link rel="apple-touch-icon-precomposed" sizes="144x144" href="/img/favicon/apple-touch-icon-144x144-precomposed.png">
	<!-- For iPhone with high-resolution Retina display: -->
	<link rel="apple-touch-icon-precomposed" sizes="114x114" href="/img/favicon/apple-touch-icon-114x114-precomposed.png">
	<!-- For first- and second-generation iPad: -->
	<link rel="apple-touch-icon-precomposed" sizes="72x72" href="/img/favicon/apple-touch-icon-72x72-precomposed.png">
	<!-- For non-Retina iPhone, iPod Touch, and Android 2.1+ devices: -->
	<link rel="apple-touch-icon-precomposed" href="/img/favicon/apple-touch-icon-precomposed.png">
  </head>
  <body>
    <nav class="top-bar" data-topbar role="navigation">
      <ul class="title-area">
        <li class="name">
          <h1><a href="/app/home/"><img id="logo" src="/app/img/laddr_header_logo.png"></a></h1>
        </li>
         <!-- Remove the class "menu-icon" to get rid of menu icon. Take out "Menu" to just have icon alone -->
        <li class="toggle-topbar menu-icon"><a href="#"><span></span></a></li>
      </ul>
      <section class="top-bar-section">
        <!-- Right Nav Section -->
        <ul class="right">
          <li><a href="/app/home/">Home</a></li>
          <li><a href="/app/buy/">Buy</a></li>
          <li><a href="/app/sell/">Sell</a></li>
          <!-- <li><a href="#">Settings</a></li> -->
          <li class="has-dropdown">
            <a href="/app/services/">Services</a>
            <ul class="dropdown">
              <li><a href="/app/services/">Conveyancy</a></li>
              <li><a href="/app/services/">Agents</a></li>
              <li><a href="/app/services/">Photography</a></li>
              <li><a href="/app/services/">Marketing</a></li>
            </ul>
          </li>
          <li><a href="/app/logout.php">Log out</a></li>

        </ul>
      </section>
    </nav>
  	<div class="row">

      <div class="row">
        <div class="small-12" id="message">
          <?php
          if(isset($user->error)){
            // Error messages
            echo $user->error;
            unset($user->error);
          }
          ?>

        </div>
      </div>
      <div class="row">
  		  <div class="small-12" id="main">
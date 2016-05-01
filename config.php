<HTML>
<HEAD>
<body background="bg.jpg">
<?php
ini_set( "display_errors", true );
date_default_timezone_set( "Asia/Calcutta" );  // http://www.php.net/manual/en/timezones.php
define( "DB_DSN", "mysql:host=localhost;dbname=cms" );
define( "DB_USERNAME", "root" );
define( "DB_PASSWORD", "123" );
define( "CLASS_PATH", "classes" );
define( "TEMPLATE_PATH", "templates" );
define( "HOMEPAGE_NUM_ARTICLES", 5 );
require( CLASS_PATH . "/Article.php" );
require( CLASS_PATH . "/Category.php" );

function handleException( $exception ) {
  echo "Sorry, a problem occurred. Please try later.";
  error_log( $exception->getMessage() );
}

set_exception_handler( 'handleException' );
?>
</body>
</html>

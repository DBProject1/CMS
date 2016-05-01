<HTML>
<HEAD>
<body background="bg.jpg">
<?php

require( "config.php" );
session_start();

if(isset($_POST["register"]))
{
  register1();
}


function register1()  {

    $results = array();
    $results['pageTitle'] = "User Registration";


      $sql = "INSERT INTO users (name,password,rolecode) VALUES(:name,:password,:user)";
       $sql2 = "SELECT * from users WHERE name = :name";

      try
      {
          $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD);
          $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
          $stmt = $conn->prepare( $sql2 );
          $stmt->bindValue( ":name", $_POST["username"], PDO::PARAM_STR );
          $stmt->execute();
          $rows  = $stmt->fetchAll();
          if (count($rows) > 0)
          {
            //username already present
            $results['errorMessage'] = "Username Already Present.";
            require( TEMPLATE_PATH . "/admin/loginForm.php" );
          }
          else
          {
            // unique username
            $stmt = $conn->prepare( $sql );
            $stmt->bindValue( ":name", $_POST["username"], PDO::PARAM_STR );
            $stmt->bindValue( ":password", $_POST["password"], PDO::PARAM_STR );
            $stmt->bindValue( ":user", "user", PDO::PARAM_STR );
            $stmt->execute();
            header( "Location: login.php" );
          }
        }

        catch(Exception $e)
        {
          echo "Error".$e;
        }


}
?>
<?php include "templates/include/header.php" ?>

      <form action="register.php?action=register" method="post" style="width: 50%;">
        <input type="hidden" name="register" value="true" />

<?php if ( isset( $results['errorMessage'] ) ) { ?>
        <div class="errorMessage"><?php echo $results['errorMessage'] ?></div>
<?php } ?>

        <ul>

          <li>
            <label for="username">Username</label>
            <input type="text" name="username" id="username" placeholder="Your user name" required autofocus maxlength="20" />
          </li>

          <li>
            <label for="password">Password</label>
            <input type="password" name="password" id="password"  placeholder="Your user password" required maxlength="20" />
          </li>

        </ul>

        <div class="buttons">
          <input type="submit" name="regist" value="SignUp" />
        </div>

      </form>

<?php include "templates/include/footer.php" ?>
</body>
</html>

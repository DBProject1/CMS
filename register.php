<?php

require( "config.php" );


if(isset($_POST['action']))
{
  if($_POST['action']=="regis")
  register1();
}

function register1()  {
    //echo "hello";
    if(isset($_POST['regist']))
    {

    $results = array();
    $results['pageTitle'] = "User Registration";


      $username = $_POST['username1'];
      $password = $_POST['password1'];

      $sql = "INSERT INTO users (name,password,rolecode) VALUES(:name,:password,:user)";


          $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD);
          $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
          $stmt = $conn->prepare( $sql );
          $stmt->bindValue( ":name", $_POST['username1'], PDO::PARAM_STR );
          $stmt->bindValue( ":password", $_POST['password1'], PDO::PARAM_STR );
          $stmt->bindValue( ":user", "user", PDO::PARAM_STR );
          $stmt->execute();



          require( TEMPLATE_PATH . "/admin/user.php" );

        }
}
?>
<div>
<?php include "templates/include/header.php" ?>

      <form action="register.php?action=regis" method="post" style="width: 50%;">
        <input type="hidden" name="regist" value="true" />

<?php if ( isset( $results['errorMessage'] ) ) { ?>
        <div class="errorMessage"><?php echo $results['errorMessage'] ?></div>
<?php } ?>

        <ul>

          <li>
            <label for="username">Username</label>
            <input type="text" name="username" id="username1" placeholder="Your user name" required autofocus maxlength="20" />
          </li>

          <li>
            <label for="password">Password</label>
            <input type="password" name="password" id="password1" placeholder="Your user password" required maxlength="20" />
          </li>

        </ul>

        <div class="buttons">
          <input type="submit" name="register" value="SignUp" />
        </div>

      </form>

<?php include "templates/include/footer.php" ?>
</div>

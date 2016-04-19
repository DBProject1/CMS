<?php include "templates/include/header.php" ?>

      <form action="login.php?action=login" method="post" style="width: 50%;">
        <input type="hidden" name="login" value="true" />


<?php if ( isset( $results['errorMessage'] ) ) { ?>
        <div class="errorMessage"><?php echo $results['errorMessage'] ?></div>
<?php } ?>

        <ul>

          <li>
            <label for="username">Username</label>
            <input type="text" name="username" id="username" placeholder="Your admin username" required autofocus maxlength="20" />
          </li>

          <li>
            <label for="password">Password</label>
            <input type="password" name="password" id="password" placeholder="Your admin password" required maxlength="20" />
          </li>

        </ul>

        <div class="buttons">
          <input id = "login" type="submit" name="login" value="Login" />
        </div>

        <div class="buttons">
          <input id = "regist" type="submit"  name="regist" value="Register" />
          </div>
          <script type="text/javascript">
          document.getElementById("regist").onclick = function () {
            location.href = "/blog/register.php";
          };
          </script>
      </form>


<?php include "templates/include/footer.php" ?>

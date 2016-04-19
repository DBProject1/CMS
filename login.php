<?php

require( "config.php" );
session_start();


$action = isset( $_GET['action'] ) ? $_GET['action'] : "";
$username = isset( $_SESSION['username'] ) ? $_SESSION['username'] : "";
$userpriv = isset( $_SESSION['userpriv']) ? $_SESSION['userpriv'] : "";

if ( $action != "login" && $action != "regist" && $action != "logout" && !$username ) {
  login();
  exit;
}
switch ( $action ) {
  case 'login':
    login();
    break;
  case 'logout':
    logout();
    break;
  case 'newArticle':
    newArticle();
    break;
  case 'editArticle':
    editArticle();
    break;
  case 'deleteArticle':
    deleteArticle();
    break;
  default:
    listArticles();
}
/*
function register1()  {
    $results = array();
    $results['pageTitle'] = "User Registration";

    if(isset($_POST['reg'])){

      $username = $_POST['username'];
      $password = $_POST['password'];



        $sql = "INSERT INTO users (name,password,rolecode) VALUES(:name,:password,:user)";


          $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD);
          $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
          $stmt = $conn->prepare( $sql );
          $stmt->bindValue( ":name", $_POST['username'], PDO::PARAM_STR );
          $stmt->bindValue( ":password", $_POST['password'], PDO::PARAM_STR );
          $stmt->bindValue( ":user", "user", PDO::PARAM_STR );
          $stmt->execute();




        }

}
*/

function login() {

  $results = array();
  $results['pageTitle'] = "Admin Login | Widget News";

  if ( isset( $_POST['login'] ) )
  {

    // User has posted the login form: attempt to log the user in
    $sql = "SELECT * from users WHERE name = :name AND password = :password";
    try
    {
      $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
      $stmt = $conn->prepare( $sql );
      $stmt->bindValue( ":name", $_POST['username'], PDO::PARAM_STR );
      $stmt->bindValue( ":password", $_POST['password'], PDO::PARAM_STR );
      $stmt->execute();
      $rows  = $stmt->fetchAll();
      if (count($results) > 0)
      {
        $_SESSION['username'] = $rows[0]['name'];
        $_SESSION['userpriv'] = $rows[0]['rolecode'];
        header( "Location: login.php" );
      }
      else
      {
        // Login failed: display an error message to the user
        $results['errorMessage'] = "Incorrect username or password. Please try again.";
        require( TEMPLATE_PATH . "/admin/loginForm.php" );
      }
    }
    catch(Exception $e)
    {
      echo "Error".$e;
    }
  }
  else
	{

    // User has not posted the login form yet: display the form
    require( TEMPLATE_PATH . "/admin/loginForm.php" );
	}

}


function logout() {
  unset( $_SESSION['username'] );
  unset( $_SESSION['userpriv']);
  header( "Location: login.php" );
}


function newArticle() {

  $results = array();
  $results['pageTitle'] = "New Article";
  $results['formAction'] = "newArticle";

  if ( isset( $_POST['saveChanges'] ) ) {

    // User has posted the article edit form: save the new article
    $article = new Article;
    $data = $_POST;
    global $result;
    $data['author'] = $_SESSION['username'];
    $article->storeFormValues( $data );
    $article->insert();
    header( "Location: login.php?status=changesSaved" );

  } elseif ( isset( $_POST['cancel'] ) ) {

    // User has cancelled their edits: return to the article list
    header( "Location: login.php" );
  } else {

    // User has not posted the article edit form yet: display the form
    $results['article'] = new Article;
    require( TEMPLATE_PATH . "/admin/editArticle.php" );
  }

}


function editArticle() {

  $results = array();
  $results['pageTitle'] = "Edit Article";
  $results['formAction'] = "editArticle";

  if ( isset( $_POST['saveChanges'] ) ) {

    // User has posted the article edit form: save the article changes

    if ( !$article = Article::getById( (int)$_POST['articleId'] ) ) {
      header( "Location: login.php?error=articleNotFound" );
      return;
    }

    $article->storeFormValues( $_POST );
    $article->update();
    header( "Location: login.php?status=changesSaved" );

  } elseif ( isset( $_POST['cancel'] ) ) {

    // User has cancelled their edits: return to the article list
    header( "Location: login.php" );
  } else {

    // User has not posted the article edit form yet: display the form
    $results['article'] = Article::getById( (int)$_GET['articleId'] );
    require( TEMPLATE_PATH . "/admin/editArticle.php" );
  }

}


function deleteArticle() {

  if ( !$article = Article::getById( (int)$_GET['articleId'] ) ) {
    header( "Location: login.php?error=articleNotFound" );
    return;
  }

  $article->delete();
  header( "Location: login.php?status=articleDeleted" );
}


function listArticles() {
  $results = array();
  $data = Article::getList($_SESSION['userpriv'],$_SESSION['username']);
  $results['articles'] = $data['results'];
  $results['totalRows'] = $data['totalRows'];
  $results['pageTitle'] = "All Articles";

  if ( isset( $_GET['error'] ) ) {
    if ( $_GET['error'] == "articleNotFound" ) $results['errorMessage'] = "Error: Article not found.";
  }

  if ( isset( $_GET['status'] ) ) {
    if ( $_GET['status'] == "changesSaved" ) $results['statusMessage'] = "Your changes have been saved.";
    if ( $_GET['status'] == "articleDeleted" ) $results['statusMessage'] = "Article deleted.";
  }

  require( TEMPLATE_PATH . "/admin/listArticles.php" );
}

?>

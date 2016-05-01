<HTML>
<HEAD>
<body background="bg.jpg">
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
    case 'listCategories':
    listCategories();
    break;
  case 'newCategory':
    newCategory();
    break;
  case 'editCategory':
    editCategory();
    break;
  case 'deleteCategory':
    deleteCategory();
    break;
  default:
    listArticles();
}

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
      if (count($rows) > 0)
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
    $data = Category::getList();
    $results['categories'] = $data['results'];
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
    $data = Category::getList();
  $results['categories'] = $data['results'];
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
  $data = Category::getList();
 $results['categories'] = array();
 foreach ( $data['results'] as $category ) $results['categories'][$category->id] = $category;

  if ( isset( $_GET['error'] ) ) {
    if ( $_GET['error'] == "articleNotFound" ) $results['errorMessage'] = "Error: Article not found.";
  }

  if ( isset( $_GET['status'] ) ) {
    if ( $_GET['status'] == "changesSaved" ) $results['statusMessage'] = "Your changes have been saved.";
    if ( $_GET['status'] == "articleDeleted" ) $results['statusMessage'] = "Article deleted.";
  }

  require( TEMPLATE_PATH . "/admin/listArticles.php" );
}
function listCategories() {
  $results = array();
  $data = Category::getList();
  $results['categories'] = $data['results'];
  $results['totalRows'] = $data['totalRows'];
  $results['pageTitle'] = "Article Categories";

  if ( isset( $_GET['error'] ) ) {
    if ( $_GET['error'] == "categoryNotFound" ) $results['errorMessage'] = "Error: Category not found.";
    if ( $_GET['error'] == "categoryContainsArticles" ) $results['errorMessage'] = "Error: Category contains articles. Delete the articles, or assign them to another category, before deleting this category.";
  }

  if ( isset( $_GET['status'] ) ) {
    if ( $_GET['status'] == "changesSaved" ) $results['statusMessage'] = "Your changes have been saved.";
    if ( $_GET['status'] == "categoryDeleted" ) $results['statusMessage'] = "Category deleted.";
  }

  require( TEMPLATE_PATH . "/admin/listCategories.php" );
}


function newCategory() {

  $results = array();
  $results['pageTitle'] = "New Article Category";
  $results['formAction'] = "newCategory";

  if ( isset( $_POST['saveChanges'] ) ) {

    // User has posted the category edit form: save the new category
    $category = new Category;
    $category->storeFormValues( $_POST );
    $category->insert();
    header( "Location: login.php?action=listCategories&status=changesSaved" );

  } elseif ( isset( $_POST['cancel'] ) ) {

    // User has cancelled their edits: return to the category list
    header( "Location: login.php?action=listCategories" );
  } else {

    // User has not posted the category edit form yet: display the form
    $results['category'] = new Category;
    require( TEMPLATE_PATH . "/admin/editCategory.php" );
  }

}


function editCategory() {

  $results = array();
  $results['pageTitle'] = "Edit Article Category";
  $results['formAction'] = "editCategory";

  if ( isset( $_POST['saveChanges'] ) ) {

    // User has posted the category edit form: save the category changes

    if ( !$category = Category::getById( (int)$_POST['categoryId'] ) ) {
      header( "Location: login.php?action=listCategories&error=categoryNotFound" );
      return;
    }

    $category->storeFormValues( $_POST );
    $category->update();
    header( "Location: login.php?action=listCategories&status=changesSaved" );

  } elseif ( isset( $_POST['cancel'] ) ) {

    // User has cancelled their edits: return to the category list
    header( "Location: login.php?action=listCategories" );
  } else {

    // User has not posted the category edit form yet: display the form
    $results['category'] = Category::getById( (int)$_GET['categoryId'] );
    require( TEMPLATE_PATH . "/admin/editCategory.php" );
  }

}


function deleteCategory() {

  if ( !$category = Category::getById( (int)$_GET['categoryId'] ) ) {
    header( "Location: login.php?action=listCategories&error=categoryNotFound" );
    return;
  }

  $articles = Article::getList( 1000000, $category->id );

  if ( $articles['totalRows'] > 0 ) {
    header( "Location: login.php?action=listCategories&error=categoryContainsArticles" );
    return;
  }

  $category->delete();
  header( "Location: login.php?action=listCategories&status=categoryDeleted" );
}

?>
</body>
</html>

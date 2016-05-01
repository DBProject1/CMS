<HTML>
<HEAD>
<body background="bg.jpg">
<?php include "templates/include/header.php" ?>
      <link rel="stylesheet" href="/highlighter/styles/monokai.css">
      <h1 style="width: 75%;"><?php echo htmlspecialchars( $results['article']->title )?></h1>
      <div style="width: 75%; font-style: italic;"><?php echo htmlspecialchars( $results['article']->summary )?></div>
      <div style="width: 75%;"><?php echo $results['article']->content?></div>
      <p class="pubDate">Published on <?php echo date('j F Y', $results['article']->publicationDate)?>
<?php if ( $results['category'] ) { ?>
     in <a href="./?action=archive&amp;categoryId=<?php echo $results['category']->id?>"><?php echo htmlspecialchars( $results['category']->name ) ?></a>
<?php } ?>
   </p>
      <p class="pubDate">Published on <?php echo date('j F Y', $results['article']->publicationDate)?></p>

      <p><a href="./">Return to Homepage</a></p>
<script src="/highlighter/highlight.pack.js"></script>
<script>hljs.initHighlightingOnLoad();</script>
<?php include "templates/include/footer.php" ?>
</body>
</html>

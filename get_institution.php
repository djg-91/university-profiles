<?php

  header('Content-Type: application/json; charset=utf-8');

  require_once "pdo.php";
  
  $post_data = [];

  $stmt = $pdo->prepare(" SELECT name FROM institution 
                          WHERE name LIKE :nm");

  $stmt->execute([ ':nm' => '%'.$_REQUEST['term'].'%' ]);
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

  if (!empty($rows)) {
    foreach ($rows as $row) {
      $post_data[] = htmlentities($row['name']);
    }
  }

  echo(json_encode($post_data));
  return;
  
?>
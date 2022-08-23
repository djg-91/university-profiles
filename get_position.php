<?php

  header('Content-Type: application/json; charset=utf-8');

  require_once "pdo.php";
  
  $post_data = [];

  $stmt = $pdo->prepare(" SELECT year, description	
                          FROM position 
                          WHERE profile_id = :pid
                          ORDER BY rank");

  $stmt->execute([ ':pid' => $_REQUEST['profile_id'] ]);
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

  if (!empty($rows)) {

    foreach ($rows as $row) {
      $post_data[] = [
        'year' => htmlentities($row['year']),
        'description' => htmlentities($row['description'])
      ];
    }
  } 
  
  echo(json_encode($post_data));
  return;

?>
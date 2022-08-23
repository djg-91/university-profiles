<?php

  header('Content-Type: application/json; charset=utf-8');

  require_once "pdo.php";
  
  $post_data = [];

  $stmt = $pdo->prepare(" SELECT edu.year, ins.name institution_name
                          FROM education edu 
                            JOIN institution ins 
                            ON edu.institution_id = ins.institution_id 
                          WHERE edu.profile_id = :pid
                          ORDER BY edu.rank"); 

  $stmt->execute([ ':pid' => $_REQUEST['profile_id'] ]);
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

  if (!empty($rows)) {

    foreach ($rows as $row) {
      $post_data[] = [
        'year' => htmlentities($row['year']),
        'institution_name' => htmlentities($row['institution_name'])
      ];
    }
  } 
  
  echo(json_encode($post_data));
  return;
?>
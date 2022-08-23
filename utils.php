<?php

  // Groups basic checks for edit.php and delete.php
  function edit_delete_checks($pdo) {
    if (!isset($_SESSION['email'])) {
      die("Not logged in");
    } 
  
    if (!isset($_REQUEST['profile_id'])) {
      $_SESSION['error'] = "Missing profile_id";
      header('Location: index.php');
      return FALSE;
    }
  
    if (!check_id($pdo)) {
      header('Location: index.php');
      return FALSE;
    }
  
    if ($GLOBALS['user_id'] != $_SESSION['user_id']) {
      $_SESSION["error"] = "You are not the owner of the profile " . $_REQUEST['profile_id'];
      header('Location: index.php');
      return FALSE;
    }

    return TRUE;
  }

  // Validates input
  function check_input() {

    $columns = ['first_name', 'last_name', 'email', 'headline', 'summary'];

    foreach ($columns as $column) {
      if (!isset($_POST[$column]) || empty($_POST[$column])) {
        $_SESSION['error'] = "All fields are required";
        return FALSE;
      } elseif ($column == 'email' && !str_contains($_POST['email'], '@'))  {
        $_SESSION['error'] = "Email must have an at-sign (@)";
        return FALSE;
      }
    }

    // Check Positions
    $GLOBALS['positions'] = [];
    foreach ($_POST as $key => $value) {
      if (str_starts_with($key, 'position_year_')) {
        $pos = substr($key, -1);
        $GLOBALS['positions'][] = $pos;
        if (!isset($_POST['position_year_'.$pos]) || empty($_POST['position_year_'.$pos])
            || !isset($_POST['description_'.$pos]) || empty($_POST['description_'.$pos])
        ) {
          $_SESSION['error'] = "All fields are required";
          return FALSE;
        } elseif (!is_numeric($value)) {
          $_SESSION['error'] = "Year must be an integer";
          return FALSE;
        }
      }
    }

    // Check Education
    $GLOBALS['education'] = [];
    foreach ($_POST as $key => $value) {
      if (str_starts_with($key, 'education_year_')) {

        $pos = substr($key, -1);
        $GLOBALS['education'][] = $pos;
        if (!isset($_POST['education_year_'.$pos]) || empty($_POST['education_year_'.$pos])
            || !isset($_POST['institution_'.$pos]) || empty($_POST['institution_'.$pos])
        ) {
          $_SESSION['error'] = "All fields are required";
          return FALSE;
        } elseif (!is_numeric($value)) {
          $_SESSION['error'] = "Year  must be an integer";
          return FALSE;
        }
      }
    }
    return TRUE;
  }

  function check_id($pdo) {
    $stmt = $pdo->prepare('SELECT user_id, first_name, last_name, email, headline, summary
                           FROM profile 
                           WHERE profile_id = :pid');

    $stmt->execute([':pid' => $_REQUEST['profile_id']]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!empty($row)) {

      $GLOBALS['user_id'] = htmlentities($row['user_id']);
      $GLOBALS['first_name'] = htmlentities($row['first_name']);
      $GLOBALS['last_name'] = htmlentities($row['last_name']);
      $GLOBALS['email'] = htmlentities($row['email']);
      $GLOBALS['headline'] = htmlentities($row['headline']); 
      $GLOBALS['summary'] = htmlentities($row['summary']); 
      
    } else {
      $_SESSION["error"] = "There's no profile with profile_id " . $_REQUEST['profile_id'];
      return FALSE;
    }
    return TRUE;
  }

  function insert_positions($pdo, $profile_id) {
    if (count($GLOBALS['positions']) > 0 && $profile_id !== 0) {
      foreach ($GLOBALS['positions'] as $pos) {
        $stmt = $pdo->prepare(' INSERT INTO position (profile_id, rank, year, description) 
                                VALUES (:pid, :ra, :ye, :ds)');
        $stmt->execute([
          ':pid' => $profile_id,
          ':ra' => $pos,
          ':ye' => $_POST['position_year_'.$pos],
          ':ds' => $_POST['description_'.$pos]
        ]);
      }
    }
    return TRUE; 
  }

  function insert_education($pdo, $profile_id) {
    if (count($GLOBALS['education']) > 0 && $profile_id !== 0) {
      
      // To call get_institution an get the variable $post_data
      $_REQUEST['php_api'] = TRUE;

      foreach ($GLOBALS['education'] as $pos) {

        $stmt = $pdo->prepare(" SELECT name FROM institution 
                                WHERE name = :nm");

        $stmt->execute([':nm' => $_POST['institution_'.$pos]]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row === FALSE) {
          $stmt = $pdo->prepare(' INSERT INTO institution (name)
                                  VALUES (:nm)');
          $stmt->execute([':nm' => $_POST['institution_'.$pos]]);
          $institution_id = $pdo->lastInsertId();
        } else {          
          $stmt = $pdo->prepare(' SELECT institution_id FROM institution
                                  WHERE name = :nm');
          $stmt->execute([':nm' => $_POST['institution_'.$pos]]);
          
          $row = $stmt->fetch(PDO::FETCH_ASSOC);

          if ($row === FALSE) {
            $_SESSION["error"] = "There's no institution_id with name " . $_POST['institution_'.$pos];
            return FALSE;
          } else {
            $institution_id = $row['institution_id'];
          }
        }

        $stmt = $pdo->prepare(' INSERT INTO education (profile_id, institution_id, rank, year) 
                                VALUES (:pid, :ind, :ra, :yr)');
        $stmt->execute([
          ':pid' => $profile_id,
          ':ind' => $institution_id,
          ':ra' => $pos,
          ':yr' => $_POST['education_year_'.$pos]
        ]);
      }
    }
    return TRUE;
  }

  function clean_positions($pdo, $profile_id) {
    $stmt = $pdo->prepare('DELETE FROM position WHERE profile_id = :pid');
    $stmt->execute([':pid' => $profile_id]);
  }

  function clean_education($pdo, $profile_id) {
    $stmt = $pdo->prepare('DELETE FROM education WHERE profile_id = :pid');
    $stmt->execute([':pid' => $profile_id]);
  }

  function profiles_table($type) {
    $input_extra = '';
    
    switch ($type) {
      case 'add':
        $GLOBALS['first_name'] = $GLOBALS['last_name'] = $GLOBALS['email'] = $GLOBALS['headline'] = $GLOBALS['summary'] = '';
        break;
      case 'view':
      case 'delete':
        $input_extra = '" disabled';
        break;
      case 'edit':
        break;
    }

    echo ('
      <div class="input-group mb-2 user-input">
        <input  type="text" class="form-control" placeholder="First Name" name="first_name" 
                value="'.htmlentities($GLOBALS['first_name']).'" '.$input_extra.' />
      </div>

      <div class="input-group mb-2 user-input">
        <input  type="text" class="form-control" placeholder="Last Name" name="last_name" 
                value="'.htmlentities($GLOBALS['last_name']).'" '.$input_extra.' />
      </div>
      
      <div class="input-group mb-2 user-input">
        <input  type="text" class="form-control" placeholder="Email" 
                name="email" value="'.htmlentities($GLOBALS['email']).'" '.$input_extra.' />
      </div>
      
      <div class="input-group mb-2 user-input">
        <input  type="text" class="form-control" placeholder="Headline" name="headline" 
                value="'.htmlentities($GLOBALS['headline']).'" '.$input_extra.' />
      </div>

      <div class="input-group mb-2 user-input">
        <textarea class="form-control" placeholder="Summary" name="summary" '.$input_extra.'>'
        .htmlentities($GLOBALS['summary']).'</textarea>
      </div>
    ');
  }

  function check_msg() {
    if ( isset($_SESSION['success']) ) {
      echo('<p style="color: green;">'.$_SESSION['success']."</p>\n");
      unset($_SESSION['success']);
    } else if (isset($_SESSION["error"])) {
      echo('<p style="color:red">'.$_SESSION["error"]."</p>\n");
      unset($_SESSION["error"]);
    }
  }

 
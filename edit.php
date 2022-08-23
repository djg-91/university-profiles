<?php
  session_start();

  require_once "pdo.php";
  require_once "utils.php";
  require_once "javascript.php";

  if (!edit_delete_checks($pdo)) {
    header('Location: index.php');
    return;
  }
  
  if (isset($_POST['Save'])) {
    
    if (!check_input()) {
      header("Location: edit.php?profile_id=".$_REQUEST['profile_id']);
      return;
    }

    $stmt = $pdo->prepare('UPDATE profile
                           SET first_name = :fn, last_name = :ln, email = :em, headline = :hl, summary = :sm
                           WHERE profile_id = :ai');                           
    
    $stmt->execute([
      ':ai' => $_REQUEST['profile_id'],
      ':fn' => $_POST['first_name'],
      ':ln' => $_POST['last_name'],
      ':em' => $_POST['email'],
      ':hl' => $_POST['headline'],
      ':sm' => $_POST['summary']
    ]);

    clean_positions($pdo, $_REQUEST['profile_id']);
    $p = insert_positions($pdo, $_REQUEST['profile_id']);

    clean_education($pdo, $_REQUEST['profile_id']);
    $e = insert_education($pdo, $_REQUEST['profile_id']);

    if ($p && $e) {
      $_SESSION["success"] = "Record edited";
      header("Location: index.php");
      return;
    } else {
      header("Location: edit.php?profile_id=".$_REQUEST['profile_id']);
      return;
    }
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Jose Gregorio Constenla Agreda - Resume Edit</title>

  <script>
    var education_arr = [];
    var position_arr = [];

    $(() => {
      let education = get_education(getURL('profile_id'));
      if (education.length > 0) education.forEach(element => createDivs(education_arr, 'education', {}, element));

      let positions = get_position(getURL('profile_id'));
      if (positions.length > 0) positions.forEach(element => createDivs(position_arr, 'position', element, {}));

      doTables(education_arr, 'education');
      doTables(position_arr, 'position');

    });
  </script>
</head>
<body>

  <div class="container">
    <h1><?php echo($GLOBALS['first_name'].' '.$GLOBALS['last_name']."'s Resume") ?></h1>
    
    <?php check_msg() ?>
    
    <form method="POST">
      <?php profiles_table('edit'); ?>

      <p>Education: <input type='button' class="btn-sm btn btn-light" id='add_education' value='+' /></p>
      <div id="education"></div>
       
      <p>Position: <input type='button' class="btn-sm btn btn-light" id='add_position' value='+' /></p>
      <div id="position"></div>

      <button type="submit" class="btn btn-primary mb-3" name="Save" value="Save">Save</button>
      <a href="index.php" class="btn btn-primary mb-3">Cancel</a>
    </form>

  </div>
  <?php require_once "styles.php"; ?>
</body>
</html>
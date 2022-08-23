<?php
  session_start();

  if (!isset($_SESSION['email'])) {
    die("Not logged in");
  } 

  require_once "pdo.php";
  require_once "utils.php";
  require_once "javascript.php";

  if (isset($_POST['Add'])) {

    if (!check_input()) {
      header("Location: add.php");
      return;
    }

    $stmt = $pdo->prepare('INSERT INTO profile (user_id, first_name, last_name, email, headline, summary) 
                           VALUES (:ui, :fn, :ln, :em, :hl, :sm)');
    $stmt->execute([
      ':ui' => $_SESSION['user_id'],
      ':fn' => $_POST['first_name'],
      ':ln' => $_POST['last_name'],
      ':em' => $_POST['email'],
      ':hl' => $_POST['headline'],
      ':sm' => $_POST['summary']
    ]);
    
    $profile_id = $pdo->lastInsertId();

    $p = insert_positions($pdo, $profile_id);
    $e = insert_education($pdo, $profile_id);

    if ($p && $e) {
      $_SESSION["success"] = "Record added";
      header("Location: index.php");
      return;
    } else {
      header("Location: add.php");
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
  <title>Jose Gregorio Constenla Agreda - Resume Add</title>

  <script>
    // These are vars because I need to modify them within the function that's call in the OnClick event
    var education_arr = [];
    var position_arr = [];

    $(() => {
      doTables(education_arr, 'education');
      doTables(position_arr, 'position');
    })

  </script>

</head>
<body>
  <div class="container">
    <h1>Add a Resume</h1>

    <?php check_msg(); ?>
    
    <form method="POST">

      <?php profiles_table('add'); ?>

      <p>Education: <input type='button' class="btn-sm btn btn-light" id='add_education' value='+' /></p>
      <div id="education"></div>
       
      <p>Position: <input type='button' class="btn-sm btn btn-light" id='add_position' value='+' /></p>
      <div id="position"></div>
      
      <button type="submit" class="btn btn-primary mb-3" name="Add" value="Add">Add</button>
      <a href="index.php" class="btn btn-primary mb-3">Cancel</a>

    </form>
  </div>
  <?php require_once "styles.php"; ?>

</body>
</html>
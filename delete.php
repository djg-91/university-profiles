<?php
  session_start();

  require_once "pdo.php";
  require_once "utils.php";
  require_once "javascript.php";

  if (!edit_delete_checks($pdo)) {
    header('Location: index.php');
    return;
  }

  if (isset($_POST['Delete'])) {

    $stmt = $pdo->prepare(' DELETE FROM education
                            WHERE profile_id = :pid');
    $stmt->execute([':pid' => $_REQUEST['profile_id']]);

    $stmt = $pdo->prepare(' DELETE FROM position
                            WHERE profile_id = :pid');
    $stmt->execute([':pid' => $_REQUEST['profile_id']]);

    $stmt = $pdo->prepare(' DELETE FROM profile
                            WHERE profile_id = :pid');
    $stmt->execute([':pid' => $_REQUEST['profile_id']]);
    
    $_SESSION["success"] = "Record deleted";
    header("Location: index.php");
    return;
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Jose Gregorio Constenla Agreda - Autos Delete</title>

  <script>
    $(function() {
      let positions = get_position(getURL('profile_id'));
      if (positions.length > 0) print_positions(positions);

      let education = get_education(getURL('profile_id'));
      if (education.length > 0) print_education(education);
    });
  </script>
</head>
<body>

  <div class="container">
    <h1>Delete from Autos DB</h1>
    
    <form method="POST" onsubmit="return confirm('Do you really want to delete the resume?');">
      <?php profiles_table('delete'); ?>
      <div id="educations"></div><br>
      <div id="positions"></div>
      <button type="submit" class="btn btn-primary mt-3" name="Delete" value="Delete">Delete</button>
      <a href="index.php" class="btn btn-primary mt-3">Cancel</a>
    </form>

  </div>
  <?php require_once "styles.php"; ?>

</body>
</html>
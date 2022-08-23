<?php
  session_start();

  
  if (!isset($_REQUEST['profile_id'])) {
    $_SESSION['error'] = "Missing profile_id";
    header('Location: index.php');
    return;
  }
  
  require_once "javascript.php";
  require_once "pdo.php";
  require_once "utils.php";

  if (!check_id($pdo)) {
    header('Location: index.php');
    return;
  }

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Jose Gregorio Constenla Agreda - Resume View</title>

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
    <h1><?php echo($GLOBALS['first_name'].' '.$GLOBALS['last_name']."'s Resume") ?></h1>
    
    <?php profiles_table('view'); ?>

    <div id="educations"></div><br>
    <div id="positions"></div>

    <a href="index.php" class="btn btn-primary mt-3">Cancel</a>

  </div>
  <?php require_once "styles.php"; ?>

</body>
</html>
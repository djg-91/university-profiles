<?php
  session_start();

  if (isset($_SESSION['email'])) {
    header("Location: index.php");
    return;
  }

  if (isset($_POST['email']) && isset($_POST['pass'])) {
    if (empty($_POST['email']) || empty($_POST['pass'])) {
      $_SESSION['error'] = "User name and password are required";
      header('Location: login.php');
      return;
    } elseif (!str_contains($_POST['email'], '@'))  {
      $_SESSION['error'] = "Email must have an at-sign (@)";
      header('Location: login.php');
      return;
    } else {
      require_once 'pdo.php';

      $salt = 'XyZzy12*_';

      $check = hash('md5', $salt . $_POST['pass']);

      $sql = "SELECT user_id, name 
              FROM users 
              WHERE email = :em 
                AND password = :pw";

      $stmt = $pdo->prepare($sql);
      $stmt->execute([
        ':em' => $_POST['email'], 
        ':pw' => $check
      ]);

      $row = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($row === FALSE) {
        error_log("Login fail ".$_POST['email']." $check");
        $_SESSION['error'] = "Incorrect password";
        header("Location: login.php");
        return;
      } else { 
        error_log("Login success ".$_POST['email']);
        $_SESSION['email'] = $_POST['email'];
        $_SESSION['name'] = $row['name'];
        $_SESSION['user_id'] = $row['user_id'];
        $_SESSION["success"] = "Logged in.";
        header("Location: index.php");
        return;
      }
    }
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Jose Gregorio Constenla Agreda - Login Page</title>
</head>
<script src="utils.js"></script>
<body>
  <div class="container">
    <h1>Please Log In</h1>

    <?php require_once 'utils.php'; check_msg(); ?>

    <form method="POST">
        <input class="user-input mb-1 " type="text" name="email" id="email" class="form-control" placeholder="Email">
        <br>
        <input class="user-input" type="password" name="pass" id="pass" class="form-control" placeholder="Password">
        <br>
        <button type="submit" class="btn btn-primary mt-2" onclick="return doValidate();">Log In</button>
        <a href="index.php" class="btn btn-primary mt-2">Cancel</a>
    </form>

  </div>
  <?php require_once "styles.php"; ?>
</body>
</html>
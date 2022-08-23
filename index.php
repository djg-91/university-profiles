<?php
  session_start();

  require_once "pdo.php";
  $stmt = $pdo->query(" SELECT profile_id, first_name, last_name, email, headline 
                        FROM profile");
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Jose Gregorio Constenla Agreda - Resumes' Database</title>
</head>
<body>
  <div class="container">

    <div class="d-flex justify-content-between my-2">
      <?php
        if (isset($_SESSION['email'])) {
          echo('<h1>'.$_SESSION['name']."'s Resumes</h1>");
        }
      ?>
      <div>
        <?php
          if (isset($_SESSION['email'])) {
            echo('<a href="add.php" class="btn btn-primary">Add New Entry</a>'."\n");
            echo('<a href="logout.php" class="btn btn-primary">Logout</a>'."\n");
          } else {
            echo('<a href="login.php" class="btn btn-primary">Please log in</a>'."\n");
          }
        ?>
      </div>
    </div>

    <?php
      require_once "utils.php";

      check_msg();

      if (!empty($rows)) {

        echo('<table class="table table-striped"><thead><tr>
                <th scope="col">Name</th>
                <th scope="col">Email</th>
                <th scope="col">Headline</th>
                <th scope="col">Action</th>
              </tr></thead>'."\n");

        foreach ($rows as $row) {
          echo("<tr>");
            echo("<td>".htmlentities($row['first_name']).' '.htmlentities($row['last_name'])."</td>");
            echo("<td>".htmlentities($row['email'])."</td>");
            echo("<td>".htmlentities($row['headline'])."</td>");
            echo("<td>");
              if (isset($_SESSION['email'])) {
                echo('<a href="edit.php?profile_id='.$row['profile_id'].'">Edit</a> / ');
                echo('<a href="delete.php?profile_id='.$row['profile_id'].'">Delete</a>'."\n");
              } else {
                echo('<a href="view.php?profile_id='.$row['profile_id'].'">View</a>'."\n");
              }
            echo("</td>");
          echo("</tr>\n");
        }

        echo("</table>\n");

      } else {
        echo("<p>No rows found</p>\n");
      }

      require_once "styles.php";
    ?>
  </div>
</body>
</html>
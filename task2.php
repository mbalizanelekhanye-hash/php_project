<?php include 'menu.inc'; ?>
<!DOCTYPE html>
<html>
<head>
  <title>Task 2 - Database Operations</title>
  <style>
    body { font-family: Arial; margin: 20px; }
    table { border-collapse: collapse; margin-bottom: 20px; width: 80%; }
    th, td { border: 1px solid #333; padding: 8px; text-align: left; }
    h2 { color: #2c3e50; }
    form { margin: 20px 0; }
  </style>
</head>
<body>

<?php
//InfinityFree DB connection
$servername = "sql106.infinityfree.com"; 
$username   = "if0_42470139";  
$password   = "fUEMIElDSQfksP";
$dbname     = "if0_42470139_ict3612_assignment3";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Handle forms first (so changes show immediately)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if (isset($_POST['add_module'])) {
    $code = $_POST['ModuleCode'];
    $name = $_POST['ModuleName'];
    $conn->query("INSERT INTO Modules VALUES ('$code','$name')");
    header("Location: task2.php"); exit;
  }
  if (isset($_POST['modify_role'])) {
    $module = $_POST['ModuleCode'];
    $lecturer = $_POST['LecturerID'];
    $role = $_POST['RoleID'];
    $year = $_POST['Year'];
    $conn->query("UPDATE ModuleLecturerRole 
                  SET RoleID='$role' 
                  WHERE ModuleCode='$module' AND LecturerID='$lecturer' AND Year='$year'");
    header("Location: task2.php"); exit;
  }
  if (isset($_POST['delete_lecturer'])) {
    $id = $_POST['LecturerID'];
    // maintain referential integrity
    $conn->query("DELETE FROM ModuleLecturerRole WHERE LecturerID='$id'");
    $conn->query("DELETE FROM Lecturers WHERE LecturerID='$id'");
    header("Location: task2.php"); exit;
  }
}

// Display all tables
$tables = ["Modules", "Lecturers", "Roles", "ModuleLecturerRole"];
foreach ($tables as $table) {
  echo "<h2>$table</h2>";
  $result = $conn->query("SELECT * FROM $table");
  if ($result && $result->num_rows > 0) {
    echo "<table><tr>";
    while ($field = $result->fetch_field()) echo "<th>{$field->name}</th>";
    echo "</tr>";
    while ($row = $result->fetch_assoc()) {
      echo "<tr>";
      foreach ($row as $value) echo "<td>$value</td>";
      echo "</tr>";
    }
    echo "</table>";
  } else {
    echo "No data found in $table.";
  }
}

// SELECT queries
if (isset($_POST['query'])) {
  $choice = $_POST['query'];
  switch ($choice) {
    case 'order':
      $sql = "SELECT * FROM Modules ORDER BY ModuleName";
      break;
    case 'like':
      $sql = "SELECT * FROM Lecturers WHERE LecturerName LIKE '%John%'";
      break;
    case 'join':
      $sql = "SELECT Modules.ModuleName, Lecturers.LecturerName 
              FROM ModuleLecturerRole 
              INNER JOIN Modules ON ModuleLecturerRole.ModuleCode=Modules.ModuleCode 
              INNER JOIN Lecturers ON ModuleLecturerRole.LecturerID=Lecturers.LecturerID";
      break;
    case 'where':
      $sql = "SELECT * FROM ModuleLecturerRole WHERE Year=2026 AND RoleID=1";
      break;
    case 'max':
      $sql = "SELECT MAX(Year) AS LatestYear FROM ModuleLecturerRole";
      break;
  }
  $result = $conn->query($sql);
  echo "<h2>Query Result</h2><table><tr>";
  while ($field = $result->fetch_field()) echo "<th>{$field->name}</th>";
  echo "</tr>";
  while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    foreach ($row as $value) echo "<td>$value</td>";
    echo "</tr>";
  }
  echo "</table>";
}
?>

<!-- Forms -->
<h2>Add Module</h2>
<form method="post">
  Code: <input type="text" name="ModuleCode">
  Name: <input type="text" name="ModuleName">
  <button type="submit" name="add_module">Add</button>
</form>

<h2>Modify ModuleLecturerRole</h2>
<form method="post">
  ModuleCode: <input type="text" name="ModuleCode">
  LecturerID: <input type="text" name="LecturerID">
  RoleID: <input type="text" name="RoleID">
  Year: <input type="text" name="Year">
  <button type="submit" name="modify_role">Modify</button>
</form>

<h2>Delete Lecturer</h2>
<form method="post">
  LecturerID: <input type="text" name="LecturerID">
  <button type="submit" name="delete_lecturer">Delete</button>
</form>
    
  <h2>Run SELECT Queries</h2>
<form method="post">
  <label><input type="radio" name="query" value="order"> SELECT * FROM Modules ORDER BY ModuleName</label><br>
  <label><input type="radio" name="query" value="like"> SELECT * FROM Lecturers WHERE LecturerName LIKE '%John%'</label><br>
  <label><input type="radio" name="query" value="join"> SELECT with INNER JOIN</label><br>
  <label><input type="radio" name="query" value="where"> SELECT with WHERE AND</label><br>
  <label><input type="radio" name="query" value="max"> SELECT with MAX()</label><br>
  <button type="submit">Run Query</button>
</form>

<?php

if (isset($_POST['query'])) {
  $choice = $_POST['query'];
    
  $result = $conn->query($sql);
  echo "<h2>Query Result</h2><table><tr>";
  while ($field = $result->fetch_field()) echo "<th>{$field->name}</th>";
  echo "</tr>";
  while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    foreach ($row as $value) echo "<td>$value</td>";
    echo "</tr>";
  }
  echo "</table>";
}
?>  


<iframe src="task2.txt" height="400" width="1200"></iframe>

</body>
</html>

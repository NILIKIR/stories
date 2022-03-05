<?php

include_once 'api/config/database.php';
$database = new Database();
$db = $database->getConnection();
$query = "SELECT * FROM debug";
$stmt = $db->prepare($query);
$stmt->execute();

echo "<table border='2px'>";
echo "<tr><td>id</td><td>name</td><td>value</td><td>property</td></tr>";
while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
  echo "<tr><td>".$row["id"]."</td><td>".$row["name"]."</td><td>".$row["value"]."</td><td>".$row["property"]."</td></tr>";
}
echo "</table>"



 ?>

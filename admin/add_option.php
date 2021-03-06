<?php
/*
- - - - - - - - - - - - - - - - - - - - - - - - - - - - -
Project Title: Lunch Randomizer
File Title: add_option.php
Original Author: Yuriy Myronchenko
Modified By: Patrick Swanson

Description: Allows an admin to add a lunch option 

Created: 03/21/2011
Modified: 04/05/2011
- - - - - - - - - - - - - - - - - - - - - - - - - - - - -
*/

session_start();
include('../resources/functions.php');
$errorText = '';

connectToDatabase();

if (isset($_POST['name'])) {
  $newLocation = sanitize_input($_POST['name']);
  $categoryID = sanitize_input($_POST['category']);
  $availability = sanitize_input($_POST['availability']);

  // If they didn't enter something valid, we return an error.
  if($newLocation == '') {
    $errorText = 'You need to provide the name of the place to add.';
  } else if($availability == '') {
    $errorText = 'You need to provide the availability of the place.';
  }

  // Check to see if this entry already exists.
  $qry = '
    SELECT name 
    FROM options 
    WHERE name = \'' . $newLocation . '\'';
  $result = runQuery($qry);
  if(mysql_num_rows($result)) {
    $errorText = 'The entry: \'' . $newLocation . '\' already exists.';
  }

  if($errorText == '') {
    $qry = 'INSERT INTO options (name, categoryID, availability) VALUES (\'' . $newLocation . '\', ' . $categoryID . ', \'' . $availability . '\')';
    runQuery($qry);
  }
}


// Get the possible categories.
$qry = '
  SELECT categoryID, category
  FROM categories
  ORDER BY category';
$result = runQuery($qry);

$categories = '';
for($i = 0; $row = mysql_fetch_assoc($result); $i ++) {
  $categories .= '<option value=\'' . $row['categoryID'] . '\'>' . $row['category'] . '</option>';
}

// Get the already existing options.
$qry = '
  SELECT name, category, availability
  FROM options NATURAL JOIN categories
  ORDER BY availability, name';
$result = runQuery($qry);
$numOptions = mysql_num_rows($result);

$options = '';
while($row = mysql_fetch_assoc($result)) {
  $options .= 
    '<tr>' . 
      '<td>' . $row['name'] . '</td>' . 
      '<td>' . $row['category'] . '</td>' . 
      '<td>' . $row['availability'] . '</td>' . 
    '</tr>';
}

?>

<html>
  <head>
  </head>
  <body>
    <h1>Add Lunch Option</h1>
    <p style="color:red;"><?php echo $errorText; ?></p>
    <form action="add_option.php" method="post">
      <p>Name of Place: <input type="text" name="name" /></p>
      <p>Category: 
        <select name="category">
          <?php
            echo $categories;
          ?>
        </select>
      </p>
      <p>Availability: 
        <select name="availability">
          <option></option>
          <option>Catered</option>
          <option>non-Catered</option>
          <option>Either</option>
        </select>
      </p>
      <input type="submit" value="Submit" />
    </form>
    <div>
      <p>There are <?php echo $numOptions; ?> options available.</p>
      <table border="1">
        <tr>
          <th>Name</td>
          <th>Category</td>
          <th>Availability</td>
        </tr>
        <?php echo $options; ?>
      </table>
    </div>
  </body>
</html>
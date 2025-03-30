<?php
//有更改，cse_department 變$table
session_start();
if (isset($_POST['course'])) {
    $_SESSION['database'] = $_POST['course']; // Assign selected course to session variable
}else{//add
    $_SESSION['database'] = 'cse_department';//add
}
$table = $_SESSION['database'];//add

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Importing UI</title>
<link rel="stylesheet" href=".css/style.php">
<script type="text/javascript" src="https://unpkg.com/xlsx@0.15.1/dist/xlsx.full.min.js"></script>
</head>
<body>
    <main class = "table" id = "timetable">
        <section class="table_header">
            <h1>Importing</h1>
            <div class="input-group">
                <input type="search" placeholder="Search Data...">
                <img src="image/search-512.webp" alt="">
            </div>
			<div class="export__file">
                <label for="export-file" class="export__file-btn" title="Export File"><img src="image/export.jpg" width=25px height=25px></label>
                <input type="checkbox" id="export-file">
                <div class="export__file-options">
                    <label>Export As &nbsp; &#10140;</label>
                    <label for="export-file" id="toEXCEL">EXCEL <img src="image/excel.png"></label>
                    <label for="export-file" id="toCSV">CSV <img src="image/csv.png"></label>
                </div>
            </div>
        </section>
		<section class ="webline-section">
                    <h3> Please click on each page one by one from left to the right, otherwise, there will be some error.</h3>
				<section class = "flexcontainer">
					<a href="index.php" class = "timeline-name"> importing page <span class="fix-arrow">&UpArrow;</span></a>
                    <a href="error box section.php" class = "timeline-name"> fixing conflict page <span class="fix-arrow">&LeftArrow;</span></a>
					<a href="visualization.php" class = "timeline-name"> visualization page <span class="fix-arrow">&LeftArrow;</span></a>
				</section>
		</section>
		<section class ="button-section">	
                <form method="post" action="" >
                <select name='course' class="course" onchange="this.form.submit()">                  
                    <?php if (isset($_SESSION['database'])): ?>
                        <option value="" disabled selected><?php echo "Currently selected database table: " . htmlspecialchars($_SESSION['database']); ?></option>
                    <?php endif; ?>

                    <option value="core_course" <?php echo (isset($_SESSION['database']) && $_SESSION['database'] == 'core_course') ? 'selected' : ''; ?>>Core Course</option>
                    <option value="faculty_course" <?php echo (isset($_SESSION['database']) && $_SESSION['database'] == 'faculty_course') ? 'selected' : ''; ?>>Faculty Course</option>
                    <option value="cse_department" <?php echo (isset($_SESSION['database']) && $_SESSION['database'] == 'cse_department') ? 'selected' : ''; ?>>Department Course</option>
                </select>
                </form> 
                <!--work on the input function here-->                         		
                <form method="post" action="php_function/import.php" enctype="multipart/form-data">
                    <input type="hidden" name="course" value="<?php echo isset($_SESSION['database']) ? $_SESSION['database'] : ''; ?>">
                    <input class="btn" type="submit" name="reset" value="Reset"> 
                    <input class="btn" type="file" name="file" accept=".csv">
                    <input class="btn" type="submit" name="submit" value="Upload">
                    <input class="btn" type="submit" name="input" value="Input">
                </form>
        </section>
		
        <section class="table_body">
            <table>
                <thead>
                    <tr>
                        <th>Class Code <span class="icon-arrow">&UpArrow;</span></th>
                        <th>Course Title <span class="icon-arrow">&UpArrow;</span></th>
                        <th>Credit <span class="icon-arrow">&UpArrow;</span></th>
                        <th>Teaching Staff <span class="icon-arrow">&UpArrow;</span></th>
                        <th>Course Component <span class="icon-arrow">&UpArrow;</span></th>
                        <th>Period <span class="icon-arrow">&UpArrow;</span></th>
                        <th>Edit column </th>
						
                    </tr>
                </thead>
                <tbody>
                        <?php
                            $conn =  new mysqli("localhost", "root", "", "upload_file_test");
                            if ($conn -> connect_error){
                                die("Connection failed:". $conn -> connect_error);
                            }
                            $sql = mysqli_query($conn, "SELECT * FROM  {$_SESSION["database"]}");

                            while ($fetch = mysqli_fetch_array($sql))
                            {
                            echo "<tr>";
                            echo "<td>" . $fetch['Class_Code'] . "</td>";
                            echo "<td>" . $fetch['Course_Title'] . "</td>";
                            echo "<td>" . $fetch['Units'] . "</td>";
                            echo "<td>" . $fetch['Teaching_Staff'] . "</td>";
                            echo "<td>" . $fetch['Course_Component'] . "</td>";
                            echo "<td>" . $fetch['Period'] . "</td>";
                            echo "<td><a href='php_function/edit.php?id=" . $fetch['id'] . "'>Edit</a></td>";
                            echo "</tr>";
                            }
                        ?>
                </tbody>
            </table>
        </section>
    </main>
    <script src=".js/script.js"></script>
</body>
</html>
<?php
$conn = new mysqli("localhost", "root", "", "upload_file_test");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$update_table = ["core_course", "faculty_course", "cse_department"];

for ($i = 0; $i < 3; $i++) {
    $sql = mysqli_query($conn, "SELECT * FROM {$update_table[$i]}");

    while ($fetch = mysqli_fetch_array($sql)) {
        $day_and_time = explode(" ", $fetch['Period'], 2);
        $Day = $day_and_time[0];

        if (isset($day_and_time[1])) {
            $time = explode(" - ", $day_and_time[1], 2);
            for ($j = 0; $j < 2; $j++) {
                $A_or_P = substr($time[$j], 5, 1);
                $first_hour = substr($time[$j], 0, 2);

                if ($A_or_P == 'A' && $first_hour !== "12") {
                    $time[$j] = substr($time[$j], 0, 5);
                } elseif ($A_or_P == 'A' && $first_hour == "12") {
                    $first_hour = substr($time[$j], 0, 2) - 12;
                    $time[$j] = str_pad($first_hour, 2, '0', STR_PAD_LEFT) . substr($time[$j], 2, 3);
                } elseif ($A_or_P == 'P' && $first_hour !== "12") {
                    $first_hour = substr($time[$j], 0, 2) + 12;
                    $time[$j] = $first_hour . substr($time[$j], 2, 3);
                } elseif ($A_or_P == 'P' && $first_hour == "12") {
                    $time[$j] = substr($time[$j], 0, 5);
                }
            }

            $sqlUpdate = "UPDATE {$update_table[$i]} SET Day = ?, Start_time = ?, End_time = ? WHERE id = ?";
            $stmt = $conn->prepare($sqlUpdate);
            if ($stmt) {
                $stmt->bind_param("sssi", $Day, $time[0], $time[1], $fetch['id']);
                $stmt->execute();
                $stmt->close(); // Close the statement
            } else {
                echo "Error in statement preparation: " . $conn->error;
            }
        } else {
            $sqlUpdate = "UPDATE {$update_table[$i]} SET Day = ? WHERE id = ?";
            $stmt = $conn->prepare($sqlUpdate);
            if ($stmt) {
                $stmt->bind_param("si", $Day, $fetch['id']);
                $stmt->execute();
                $stmt->close(); // Close the statement
            } else {
                echo "Error in statement preparation: " . $conn->error;
            }
        }
    }
}

$conn->close(); // Close the database connection
?>
<?php
    $conn =  new mysqli("localhost", "root", "", "upload_file_test");
    if ($conn -> connect_error){
        die("Connection failed:". $conn -> connect_error);
    }
    $sqlchecknull = mysqli_query($conn, 'SELECT * FROM cse_department_data_after_combine');
    $sql = mysqli_query($conn, 'SELECT * FROM cse_department');

    if (($fetch_from_new_table = mysqli_fetch_array($sqlchecknull)) !== ""){
        $sqltruncate = "TRUNCATE TABLE cse_department_data_after_combine";
        $result = $conn->query($sqltruncate);
    }
    while ($fetch = mysqli_fetch_array($sql)) {
        $Day = 0;
        $Course = str_split($fetch['Class_Code'], 4);
        $day_and_time = explode(" ", $fetch['Period'], 2);
        $date_array = array("Mo", "Tu", "We", "Th", "Fr", "Sa");
        for ($i = 0; $i < 6; $i++){
            if ($date_array[$i] ==  $fetch['Day']){
                $Day = $i + 1;
            }
        }
        if (isset($Course[0]) && isset($Course[1]) ) {
            if (isset($day_and_time[1])){
                $sqlInsert = "INSERT INTO cse_department_data_after_combine 
                (id, Class_Subject, Class_Nbr, Units, Section_Code, Teaching_Staff, 
                Course_Component, Day, Period, Start_time, End_time)
                VALUES ('" . $fetch['id'] . "', '" . $Course[0] . "', '" . $Course[1] . "', '"
                . $fetch['Units'] . "', '" . $fetch['Section_Code']. "', '" . $fetch['Teaching_Staff'] . "', '"
                . $fetch['Course_Component'] . "', '" . $Day . "', '"
                . $day_and_time[1] . "', '" . $fetch['Start_time'] . "', '" . $fetch['End_time'] . "')";
            $result = $conn->query($sqlInsert);
        }
        else {
            $sqlInsert = "INSERT INTO cse_department_data_after_combine 
                (id, Class_Subject, Class_Nbr, Units, Section_Code, Teaching_Staff, 
                Course_Component, Day)
                VALUES ('" . $fetch['id'] . "', '" . $Course[0] . "', '" . $Course[1] . "', '"
                . $fetch['Units'] . "', '" . $fetch['Section_Code'] . "', '" . $fetch['Teaching_Staff'] . "', '"
                . $fetch['Course_Component'] . "', '" . $day_and_time[0]. "')";
            $result = $conn->query($sqlInsert);
        }
            
        }
        else {
            echo "Class_Code does not have enough parts: " . $fetch['Class_Code'];
        }
    }
    function assign_value($database){
        if ($_SESSION["database"] != $database){
        $_SESSION["database"] = $database;
        echo '<script>window.location.href = "index.php";</script>'; 
    }
    else {
        $_SESSION["database"] = 'cse_department';
    }
}
?>
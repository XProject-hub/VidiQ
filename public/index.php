<?php
// index.php
$title = "VidiQ Panel";

// Function to call Python scripts
function callPythonScript($scriptPath) {
    $output = shell_exec("python3 " . escapeshellarg($scriptPath));
    return $output ? $output : "No response from Python script.";
}

// Check for a button press
if (isset($_POST['fetchStats'])) {
    $result = callPythonScript("/home/VidiQ/panel/scripts/fetch_stats.py");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
    <div class="header">
        <h1>Welcome to <?php echo $title; ?></h1>
    </div>
    <div class="container">
        <form method="post">
            <button type="submit" name="fetchStats" class="button">Fetch System Stats</button>
        </form>
        <?php if (!empty($result)): ?>
            <pre><?php echo $result; ?></pre>
        <?php endif; ?>
    </div>
    <script src="js/main.js"></script>
</body>
</html>

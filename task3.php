<?php include 'menu.inc'; ?>
<!DOCTYPE html>
<html>
<head>
  <title>Task 3 - Dataset Processing</title>
  <style>
    body { font-family: Arial; margin: 20px; }
    table { border-collapse: collapse; margin-bottom: 20px; width: 80%; }
    th, td { border: 1px solid #333; padding: 8px; text-align: left; }
    h2 { color: #2c3e50; }
  </style>
</head>
<body>

<h2>Upload Dataset Zip</h2>
<form method="post" enctype="multipart/form-data">
  <input type="file" name="dataset">
  <button type="submit" name="upload">Process Dataset</button>
</form>

<?php
function processDataset($extractPath) {
    $classesFile = $extractPath . "/classes.txt";
    if (!file_exists($classesFile)) {
        echo "<p>classes.txt not found in dataset folder.</p>";
        return;
    }

    $classes = array_map('trim', file($classesFile));
    $counts = [];

    foreach (['train','validate','test'] as $folder) {
        $folderPath = $extractPath . "/" . $folder;
        $counts[$folder] = array_fill_keys($classes, 0);

        if (is_dir($folderPath)) {
            foreach (glob($folderPath . "/*.txt") as $file) {
                foreach (file($file) as $line) {
                    foreach ($classes as $class) {
                        if (strpos($line, $class) === 0) {
                            $counts[$folder][$class]++;
                        }
                    }
                }
            }
        }
    }

    // Display results
    echo "<h2>Instance Counts</h2>";
    foreach ($counts as $folder => $classCounts) {
        echo "<h3>$folder</h3><table><tr><th>Class</th><th>Count</th></tr>";
        foreach ($classCounts as $class => $count) {
            echo "<tr><td>$class</td><td>$count</td></tr>";
        }
        echo "</table>";
    }

    // Totals
    $totalCounts = array_fill_keys($classes, 0);
    foreach ($counts as $classCounts) {
        foreach ($classCounts as $class => $count) {
            $totalCounts[$class] += $count;
        }
    }
    echo "<h3>Total (dataset)</h3><table><tr><th>Class</th><th>Count</th></tr>";
    foreach ($totalCounts as $class => $count) {
        echo "<tr><td>$class</td><td>$count</td></tr>";
    }
    echo "</table>";
}

// --- Main logic ---
$extractPath = __DIR__ . "/dataset";

if (isset($_POST['upload']) && isset($_FILES['dataset'])) {
    // Try to unzip uploaded file
    $zipFile = $_FILES['dataset']['tmp_name'];
    $zip = new ZipArchive;
    if (class_exists('ZipArchive') && $zip->open($zipFile) === TRUE) {
        $zip->extractTo($extractPath);
        $zip->close();
        echo "<p>Dataset extracted successfully from upload.</p>";
        processDataset($extractPath);
    } else {
        echo "<p>ZipArchive not available. Please upload dataset already extracted into /htdocs/dataset/.</p>";
    }
} else {
    // If no upload, check existing dataset folder
    if (is_dir($extractPath)) {
        echo "<p>Using already uploaded dataset folder.</p>";
        processDataset($extractPath);
    } else {
        echo "<p>No dataset found. Upload dataset.zip or place extracted dataset folder in /htdocs.</p>";
    }
}
?>

<iframe src="task3.txt" height="400" width="1200"></iframe>

</body>
</html>

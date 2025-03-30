<!DOCTYPE html>
<!--🛠 Created with ❤️ by Dhruval Joshi with HostingSpell.com for smart RDNS management.-->
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RDNS Automation Tool</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #2c2f33;
            color: #ffffff;
        }
        .container {
            margin-top: 50px;
        }
        .dark-card {
            background-color: #23272a;
            border-color: #7289da;
        }
        .dark-btn {
            background-color: #7289da;
            border-color: #7289da;
            color: white;
        }
    </style>
</head>
<body>

<div class="container">
    <h1 class="text-center">RDNS Record Automation Tool</h1>
    <div class="card dark-card p-4">
        <form method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="rdnsInput" class="form-label">Paste RDNS Records or Upload a File</label>
                <textarea class="form-control" id="rdnsInput" name="rdnsInput" rows="6" placeholder="One per line, for example: 192.168.0.101 PTR fivecron.com OR int3rnet.net,192.168.100.101"></textarea>
            </div>
            <div class="mb-3">
                <label for="rdnsFile" class="form-label">OR Upload File (.txt only, max 2KB)</label>
                <input type="file" class="form-control" id="rdnsFile" name="rdnsFile" accept=".txt">
            </div>
            <button type="submit" class="btn dark-btn" name="process">Process Records</button>
        </form>

        <?php
        if (isset($_POST['process'])) {
            // Restrict file type and size
            if (!empty($_FILES['rdnsFile']['tmp_name'])) {
                $file = $_FILES['rdnsFile'];
                $allowedTypes = ['text/plain'];
                $maxFileSize = 2048;  // 2 KB

                // Check if the uploaded file is a .txt file
                if (!in_array($file['type'], $allowedTypes)) {
                    echo "<p class='text-danger'>Error: Only .txt files are allowed.</p>";
                    exit;
                }

                // Check if the file size is less than or equal to 2 KB
                if ($file['size'] > $maxFileSize) {
                    echo "<p class='text-danger'>Error: File size exceeds 2 KB.</p>";
                    exit;
                }

                // Read file contents
                $rdnsInput = file_get_contents($file['tmp_name']);
            }

            // Handle RDNS Input pasted in textarea
            $rdnsInput = $_POST['rdnsInput'] ?? '';

            function processRDNS($input) {
                $lines = explode("\n", trim($input));
                $output = [];
                $errors = [];  // Collect errors here
                $ip_subnet = ''; // For generating the filename

                foreach ($lines as $line) {
                    $line = trim($line);  // Trim any unnecessary spaces or line breaks
                    if (empty($line)) {
                        continue;
                    }

                    // Handle cases where commas or PTR are missing
                    if (strpos($line, 'PTR') !== false) {
                        [$ip, $hostname] = explode(' PTR ', $line);
                    } else {
                        if (strpos($line, ',') !== false) {
                            [$hostname, $ip] = explode(',', $line);
                        } else {
                            // Detect missing delimiters, assume the IP starts after the domain
                            preg_match('/(.+?)(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})/', $line, $matches);
                            if (count($matches) == 3) {
                                $hostname = $matches[1];
                                $ip = $matches[2];
                            } else {
                                // Invalid entry, add to errors
                                $errors[] = "Invalid format: $line";
                                continue;
                            }
                        }
                    }

                    if (filter_var($ip, FILTER_VALIDATE_IP)) {
                        $last_octet = trim(explode('.', $ip)[3]);
                        $ip_subnet = $ip_subnet ?: implode('.', array_slice(explode('.', $ip), 0, 3)); // Get the subnet
                        $hostname = trim($hostname);
                        if (!str_ends_with($hostname, '.')) {
                            $hostname .= '.';
                        }
                        $output[] = "$last_octet PTR $hostname";
                    } else {
                        $errors[] = "Invalid IP: $line";
                    }
                }

                return [$output, $errors, $ip_subnet];
            }

            // Process RDNS Records
            [$processedRecords, $errors, $ip_subnet] = processRDNS($rdnsInput);

            // Generate dynamic filename
            $timestamp = date('Ymd_His');  // Format: YYYYMMDD_HHMMSS
            $filename = "rdns_{$ip_subnet}_{$timestamp}.txt";

            // If there are valid records, allow downloading them
            if (!empty($processedRecords)) {
                echo "<h3 class='mt-4'>Preview Processed Records:</h3>";
                echo "<pre class='bg-dark text-light p-3'>";
                foreach ($processedRecords as $record) {
                    echo htmlspecialchars($record) . "\n";
                }
                echo "</pre>";

                // Pass the serialized records and filename to the download.php script
                echo "<form method='post' action='download.php'>
                        <input type='hidden' name='records' value='" . base64_encode(serialize($processedRecords)) . "'>
                        <input type='hidden' name='filename' value='$filename'>
                        <button type='submit' class='btn dark-btn mt-3'>Download Processed File</button>
                      </form>";
            } else {
                echo "<p class='text-danger'>No valid records were processed.</p>";
            }

            // Display any errors encountered during processing
            if (!empty($errors)) {
                echo "<h3 class='mt-4 text-warning'>Errors:</h3>";
                echo "<pre class='bg-danger text-light p-3'>";
                foreach ($errors as $error) {
                    echo htmlspecialchars($error) . "\n";
                }
                echo "</pre>";
            }
        }
        ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

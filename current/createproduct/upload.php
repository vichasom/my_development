<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap-theme.min.css" rel="stylesheet">
    <script src="js/jquery-1.12.4.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <title>File Upload</title>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12 jumbotron text-center"
             style="font-size: 16px; font-weight: 600; color: #000000; text-transform: uppercase">
            <?php
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
            $uploadOk = 1;
            $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
            // Check if image file is a actual image or fake image
            if (isset($_POST["submit"])) {
                // Check if file already exists
                if (file_exists($target_file)) {
                    echo "<span style='color:red;'> Sorry, file already exists.</span><br>";
                    $uploadOk = 0;
                }

                // Check file size
                if ($_FILES["fileToUpload"]["size"] > 500000) {
                    echo "<span style='color:red;'>Sorry, your file is too large.</span><br>";
                    $uploadOk = 0;
                }

                // Allow certain file formats
                if ($imageFileType != "csv") {
                    echo "<span style='color:red;'>Sorry, only CSV files are allowed.</span><br>";
                    $uploadOk = 0;
                }

                // Check if $uploadOk is set to 0 by an error
                if ($uploadOk == 0) {
                    echo "<span style='color:red;'>your file was not uploaded.</span><br>";
                    print ("<div class='col-xs-12'>
                            <button onclick='goBack()' class='btn btn-default'>Go Back</button>
                            </div>");
                    // if everything is ok, try to upload file
                } else {
                    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                        echo "<span style='color:green;'>The file " . basename($_FILES["fileToUpload"]["name"]) . " has been uploaded.</span><br>";
                        header('location:createsimple.php?file='.basename($_FILES["fileToUpload"]["name"]));
                    } else {
                        echo "<span style='color:red;'>Sorry, there was an error uploading your file</span>.<br>";
                    }
                    print ("<div class='col-xs-12'>
                            <button onclick='goBack()' class='btn btn-default'>Go Back</button>
                            </div>");
                }
            }

            ?>
        </div>
    </div>
</div>
<script>
    function goBack() {
        window.history.back();
    }
</script>
</body>
</html>

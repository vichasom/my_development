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
                <div class="col-xs-12 jumbotron text-center" style="font-size: 16px; font-weight: 600; color: #000000; text-transform: uppercase">
                    Product Import File
                </div>
                <div class="col-xs-12 col-sm-6 col-sm-offset-3">
                    <form class="form-horizontal" action="upload.php" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="file" class="col-xs-12 col-sm-2 control-label">File</label>
                            <div class="col-sm-10">
                                <input type="file" class="form-control" name="fileToUpload" id="fileToUpload" placeholder="Email">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-xs-12 text-right">
                                <button type="submit" name="submit" class="btn btn-default">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>
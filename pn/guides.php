<?php
require("api/auth/login_check.php"); //Make sure the users is logged in
$title = "OT | Home"; //Set the browser title
$highlight = "guides"; //Select which tab in the navigation to highlight
require("structure/top.php"); //Include the sidebar HTML


?>
    <html>
    <head>
        <!--  <script type="module" src="api/dashboard/dashboard.js">-->
        <!--  </script>-->

    </head>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Guides</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="guides.php">Guides</a></li>
                            <li class="breadcrumb-item active">Guides</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->
        <!-- Main content -->
        <div class="content">
            <div class="container-fluid">

                <!-- Body -->
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">Search</h3>
                    </div>
                    <div class="card-body">
                                <div class="row">
                                    <input id="search-input" class="form-control col-11" type="text" placeholder="Search for...">
                                    <button id="search-submit" class="btn btn-primary col-1" onclick="perform_search()">
                                        Search
                                    </button>
                                </div>
                    </div>
                </div>

            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
    </html>
<?php include('structure/bottom.php'); ?>
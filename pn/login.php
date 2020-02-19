<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>OT | Login</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="AdminLTE/plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="AdminLTE/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="AdminLTE/dist/css/adminlte.min.css">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
  <script type="text/javascript">
    function process(){
      //Check if all the required fields are filled out
      if( $('#username').val().length < 5 || $('#username').val().length > 50){
        $('#statusMsg').html("That username is invalid.");
        return;
      }
      
      //Check the password length
      if( $('#password').val().length < 5 || $('#password').val().length > 50){
        $('#statusMsg').html("That password is too short or too long.");
        return;
      }

      //Disable the login button
      $('#loginBtn').prop('disabled', true);

      //Send the form data
      $.ajax({
        type: "POST",
        dataType: 'text',
        url: 'api/auth/login.php',
        data: {
          username: $('#username').val(),
          password: $('#password').val()
        },
        success: function(data, status){
          if(data == "success"){
            window.location.replace("index.php");
          }else{
            $('#statusMsg').html(data);
            $('#loginBtn').prop('disabled', false);
          }
        }
      });
    }

  </script>
</head>
<body class="hold-transition register-page">
<div class="register-box">
  <div class="register-logo">
    <b>Organic</b>Transformations
  </div>

  <div class="card">
    <div class="card-body register-card-body">
      <p class="login-box-msg" id="statusMsg">Welcome! Please login.</p>


      <div class="input-group mb-3">
        <input id="email" type="email" class="form-control" placeholder="Email">
        <div class="input-group-append">
          <div class="input-group-text">
            <span class="fas fa-envelope"></span>
          </div>
        </div>
      </div>

      <div class="input-group mb-3">
        <input id="password" type="password" class="form-control" placeholder="Password">
        <div class="input-group-append">
          <div class="input-group-text">
            <span class="fas fa-lock"></span>
          </div>
        </div>
      </div>


      <div class="row">

        <!-- /.col -->
        <div class="col-4">
          <button id="loginBtn" onclick="process()" class="btn btn-primary btn-block">Login</button>
        </div>
        <!-- /.col -->
      </div>
    </div>
    <!-- /.form-box -->
  </div><!-- /.card -->
</div>
<!-- /.register-box -->

<!-- jQuery -->
<script src="AdminLTE/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="AdminLTE/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="AdminLTE/dist/js/adminlte.min.js"></script>
</body>
</html>

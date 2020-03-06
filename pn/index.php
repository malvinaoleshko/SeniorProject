<?php
require("api/auth/login_check.php"); //Make sure the users is logged in
$title = "OT | Home"; //Set the browser title
$highlight = "index"; //Select which tab in the navigation to highlight
require("structure/top.php"); //Include the sidebar HTML
//require("TwitterAPIExchange.php");

?>
<html>
<head>
  <script type="text/javascript">
    $( window ).on( "load", function() {
        get_guides(4, $('#nutrition_favorites'), 'nutrition');
        get_guides(4, $('#exercise_favorites'), 'exercise');
        get_guides(4, $('#highlighted_guides'), 'nutrition');
    });

    function get_guides(number, container, tag) {
        $.ajax({
            type:'POST',
            url: 'api/dashboard/home.php',
            //dataType: 'string',
            data: {
                number: number,
                tag: tag
            },
            success: function(data) {
                container.html(make_cards(JSON.parse(data)));
            },
            error: function() {
                console.log("ERROR");
            }
        });
    }

    function make_cards(data) {
        let str_hold = Array();
        let day_str;
        let ribbon_str;
        for(let key in data) {
            if(data.hasOwnProperty(key)) {
                day_str = calc_time_since(data[key]["date_last_modified"]);
                ribbon_str = get_ribbon(data[key]["subscription_level"]);
                str_hold.push(
                    '<div class="col-lg-3 col-md-6 col-sm-12">' +
                      ribbon_str +
                      '<div class="small-box">' +
                        '<div class="inner">' +
                          '<img src="' + data[key]["thumbnail"] + '" alt="" class="img-fluid" style=" width=100%; height= 15vw; object-fit: cover">' +
                        '</div>' +
                        '<a class="small-box-footer">' +
                          '<div class="row pl-1 pr-1">' +
                            '<div class="text-left col-6">' + data[key]["guide_name"] + '</div>' +
                            '<div class="text-right col-6">' + 'Added ' + day_str  + '</div>' +
                          '</div>' +
                        '</a>' +
                      '</div>' +
                    '</div>'
                );
            }
        }
        return str_hold;
    }

    function calc_time_since(string) {
        const year_month_day = string.split('-');
        const date = new Date(year_month_day[0], year_month_day[1] - 1, year_month_day[2]);
        const cur_date = new Date();
        const milli_in_day = 24 * 60 * 60 * 1000;
        const days_since = Math.floor(Math.abs(cur_date - date) / milli_in_day);
        if(days_since >= 365) {
            const years_since = cur_date.getFullYear() - date.getFullYear();
            return years_since.toString() + ((years_since > 1) ? (' years ago') : (' year ago'));
        }
        if(days_since > 30) {
            let months_since = cur_date.getMonth() - date.getMonth();
            if(months_since < 0) months_since = months_since + 12;
            return months_since.toString() + ((months_since > 1) ? (' months ago') : (' month ago'));
        }
        switch(days_since) {
            case 0:
                return 'today';
            case 1:
                return 'yesterday';
            default:
                return days_since.toString() + ' days ago';
        }
    }

    function get_ribbon(level) {
        if(level === "WELCOME") return "";
        let color_levels = {
            "BEGINNER" : '<div class="ribbon bg-white">',
            "INTERMEDIATE" : '<div class="ribbon bg-yellow">',
            "ADVANCED" : '<div class="ribbon bg-red">',
            "PERSONAL" : '<div class="ribbon bg-black">'
        };
        return (
          '<div class="ribbon-wrapper ribbon-lg">' +
            color_levels[level] +
              level +
            '</div>' +
          '</div>'
        );
    }
  </script>
</head>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 text-dark">Dashboard</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item active">Dashboard</li>
          </ol>
        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content-header -->
  <!-- Main content -->
  <div class="content">
    <div class="container-fluid">

      <!-- Announcements -->
      <!-- Connecting to twitter may prove difficult, there's a lot more stuff needed than initially thought -->
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Announcements</h3>
        </div>
        <div class="card-body p-0">
          <ul class="products-list product-list-in-card">
            <li class="item">
              <div class="row product-info ml-3 mr-3">
                <div class="pl-0 pr-0 col-10">
                  <div class="product-description">Hello, this is an annoucement!.</div>
                </div>
                <div class="text-right pl-0 pr-0 col-2">
                  <div class="product-description">Added 100 days ago</div>
                </div>
              </div>
            </li>
          </ul>
        </div>
      </div>

      <!-- Goals -->
      <div class="card">
          <div class="card-header">
            <h3 class="card-title">My Goals</h3>
          </div>
          <ul class="todo-list">
            <li class="item">Do the things.</li>
            <li class="item">Work on stuff.</li>
          </ul>
      </div>

      <!-- Body -->
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">My Body</h3>
        </div>
      </div>

      <!-- Highlighted For You -->
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Highlighted For You</h3>
        </div>
        <div class="card-body">
          <div id="highlighted_guides" class="row">
            <!-- This is populated by get_guides in the header -->
          </div>
        </div>
      </div>

      <!-- Nutrition Favorites -->
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Nutrition Favorites</h3>
        </div>
        <div class="card-body">
          <div id="nutrition_favorites" class="row">
            <!-- This is populated by get_guides in the header -->
          </div>
        </div>
      </div>

        <!-- Exercise Favorites -->
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Exercise Favorites</h3>
        </div>
      </div>
      <div class="card-body">
        <div id="exercise_favorites" class="row">
          <!-- This is populated by get_guides in the header -->
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->
</html>
<?php include('structure/bottom.php'); ?>
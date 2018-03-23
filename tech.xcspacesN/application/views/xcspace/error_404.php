<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">
    <meta name="renderer" content="webkit">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>XCSPACES</title>
    <meta name="description" content="">
    <meta name="keywords" content="">
    <link rel="shortcut icon" href="/images/favicon.ico">
    <!-- BOOTSTRAP STYLES-->
    <link href="/static/css/common.css" rel="stylesheet" />
    <!-- FONTAWESOME STYLES-->
    <link href="/static/css/font-awesome.css" rel="stylesheet" />
     <!-- PAGE LEVEL STYLES-->
    <link href="/static/css/error.css" rel="stylesheet" />
    <!-- GOOGLE FONTS-->
    <!-- <link href='http://fonts.useso.com/css?family=Open+Sans' rel='stylesheet' type='text/css' /> -->

</head>
<body>
    <div class="container">
         <div class="row text-center">
            <div class="col-md-12 set-pad" >
                <strong class="error-txt">XCSPACES ! 404</strong>
            </div>
        </div>
    </div>
    <div class="c-err">
        <div class="container">
            <!--Search Section Start-->
            <div class="">
                <form action="/search" id='searchFrom' method="get">
                    <div class="form-group input-group col-md-6 col-md-offset-3">
                        <input type="text" name="s" class="form-control" placeholder="输入关键字" />
                        <span class="input-group-btn">
                            <a class="btn btn-primary" type="submit" href="javascript:;" onclick="searchFrom()">
                                <i class="fa fa-gear fa-spin"></i>&nbsp;&nbsp;搜索
                            </a>
                        </span>
                    </div>
                </form>

                <br />

            </div>
            <!--Search Section end-->
        </div>
    </div>
    <div class="container">
        <div class="row text-center">
            <div class="col-md-12">
            <br />
            <br />
            <a class="btn btn-success" href="/">
            PLEASE GO BACK TO WEBSITE ! NOTHING HERE
            </a>
            <br />
            <br />
            </div>
        </div>
    </div>
  
  
    <hr />
    <div class="container">
        <div class="row text-center mb20">
            <div class="col-md-12">
                All Rights Reserved | &copy www.xcsapces.com
            </div>

        </div>

    </div>

<script>
function searchFrom(){
    $('#searchFrom').submit();
}
</script>
    
</body>
</html>

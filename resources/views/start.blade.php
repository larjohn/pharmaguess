<html>
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="stylesheet" href="{{URL::asset("webfonts/comiczineot_regular_macroman/stylesheet.css")}}" type="text/css" charset="utf-8" />

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">

    <style>
        html,body {
            height:100%;
            width:100%;
            margin:0;
        }
        body {
            background: url('{{URL::asset('images/policebox.jpg')}}');
            background-size: cover;
            display:flex;
        }
        form {
            margin:auto;/* nice thing of auto margin if display:flex; it center both horizontal and vertical :) */
            margin-bottom: 70px;
        }

        .blue.button:hover {
            background-color: #007d9a;
        }
        /* Sizes ---------- */
        .small.awesome {
            font-size: 14px;
            border-radius: 12px;

        }
        .medium.awesome {
            font-size: 13px;
        }
        .large.awesome {
            font-size: 14px;
            padding: 8px 14px 9px;
        }


        /* Colors ---------- */
        .blue.awesome {
            background-color: #2daebf;
        }
        .red.awesome {
            background-color: #e33100;
        }
        .magenta.awesome {
            background-color: #a9014b;
        }
        .orange.awesome {
            background-color: #ff5c00;
        }
        .yellow.awesome {
            background-color: #ffb515;
        }

        .awesome{
            background: #222 url(/images/alert-overlay.png) repeat-x;
            display: inline-block;
            padding: 5px 10px 6px;
            color: #fff;
            text-decoration: none;
            font-weight: bold;
            line-height: 1;
            -moz-border-radius: 5px;
            -webkit-border-radius: 5px;
            -moz-box-shadow: 0 1px 3px #999;
            -webkit-box-shadow: 0 1px 3px #999;
            text-shadow: 0 -1px 1px #222;
            border-bottom: 1px solid #222;
            position: relative;
            cursor: pointer;
        }

        input[type=text], textarea {
            -webkit-transition: all 0.30s ease-in-out;
            -moz-transition: all 0.30s ease-in-out;
            -ms-transition: all 0.30s ease-in-out;
            -o-transition: all 0.30s ease-in-out;
            outline: none;
            padding: 3px 0px 3px 3px;
            margin: 5px 1px 3px 0px;
            border: 1px solid #DDDDDD;
            box-shadow: 0 0 5px rgba(81, 203, 238, 1);
            padding: 3px 0px 3px 3px;
            margin: 5px 1px 3px 0px;
            border: 1px solid rgba(81, 203, 238, 1);
            border-radius: 14px;
            padding: 0 10px   ;
        }


    </style>
</head>
<body>

    <form class="form form-vertical" method="post" action="{{URL::to("start")}}" id="form_login">
        <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />


        <input name="name" type="text" placeholder="Enter your name here Doc">
        <button class="button small awesome blue" type="submit">Enter</button>
    </form>

</body>
</html>
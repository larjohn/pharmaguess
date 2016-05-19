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
            background: url('{{URL::asset('images/drwho.jpg')}}');
            background-size: cover;
            display:flex;
        }
        div {
            width: 300px;
            margin: auto;
            margin-bottom: 70px;
            color: black;
            padding: 27px;
            margin-left: 10px;
            background: rgba(255, 255, 255, 0.64);
            text-align: justify;
            font-family: fantasy;
        }
        h1, h2, h3, h4, h5, h6{
            font-family: comic_zine_otregular;
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

        .awesome:focus{
            outline:none;
        }

        .awesome{
            background: #222 url({{URL::asset("/images/alert-overlay.png")}}) repeat-x;
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

<div >
    <h2>
        Wake up Doctor!
    </h2>
    <p> TARDIS has brought you in 2079.</p>
    <p>The Daleks have swept away our knowledge sources. In the medical field, this means we don't know how to fight disease any more. To our surprise, both Big Pharma and the naturopaths have worshiped Davros and conspired with the Daleks.</p>

    <p> People are suffering of ancient diseases and their last hope are the hackers from the Open Knowledge Foundation. You are the only one who can help the Foundation retrieve information on medications, by breaking into the enemy's Battle Computer, tonight.</p>

    <p>You gotta be careful tho' - remain stealth, otherwise they are going to destroy the information we are seeking for.</p>

    <a class="button small awesome blue"  href="{{URL::to('play')}}">Start</a>

</div>

</body>
</html>
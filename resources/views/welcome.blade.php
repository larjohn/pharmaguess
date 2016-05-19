<!DOCTYPE html>
<html>
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <meta charset="utf-8">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{URL::asset("webfonts/comiczineot_regular_macroman/stylesheet.css")}}" type="text/css" charset="utf-8" />
    <style type="text/css">


        .barOuter {
            background: #252525;
            border: 1px solid #000;
            border-radius: 8px;
            box-shadow: inset 1px 1px 3px 0 rgba(0, 0, 0, 0.8), 1px 1px 0 0 rgba(255, 255, 255, 0.12);
            height: 16px;
            width: 85%;
            margin-bottom: 10px;
            position: relative;
        }

        .barLabel {
            color: white;
            font-size: 12px;
            position: absolute;
            right: -40px;
            top: 0;
        }

        .barInner {
            background-color: #b10016;
            border-right: 1px solid #60000b;
            border-radius: 7px;
            height: 100%;
            width: 50%;
            background-size: 100% 100%, 20px 20px, 100% 100%;
            background-image:
                    linear-gradient(to bottom,
                    rgba(255, 255, 255, 0.33) 0,
                    rgba(255, 255, 255, 0.08) 50%,
                    rgba(0, 0, 0, 0.25) 50%,
                    rgba(0, 0, 0, 0.1) 100%),
                    linear-gradient(45deg,
                    rgba(255, 255, 255, 0.16) 25%,
                    rgba(0, 0, 0, 0) 25%,
                    rgba(0, 0, 0, 0) 50%,
                    rgba(255, 255, 255, 0.16) 50%,
                    rgba(255, 255, 255, 0.16) 75%,
                    rgba(0, 0, 0, 0) 75%,
                    rgba(0, 0, 0, 0)),
                    linear-gradient(to right, #60000b, #b10016);
            box-shadow: inset 0 1px 1px 0px rgba(255, 215, 215, 0.5),
            inset 0 -1px 1px 0px rgba(255, 255, 255, 0.25),
            1px 1px 3px 0 rgba(0,0,0,0.33);
        }

        .node, .link {
            cursor: pointer;
        }
        #tree-container{
            display: flex;
        }


        .node circle {
            fill: #fff;
            stroke: steelblue;
            stroke-width: 2px;
        }

        .node text {
            font-size: 10px;
            font-family: monospace;
            fill:gold;
        }

        .link {
            fill: none;

            stroke: darkred;
            stroke-width: 4px;
        }

        .templink {
            fill: none;
            stroke: red;
            stroke-width: 3px;
        }

        .ghostCircle.show {
            display: block;
        }

        .ghostCircle, .activeDrag .ghostCircle {
            display: none;
        }

        #inventory{
            position: fixed;
            bottom: 10px;
            right: 10px;

        }

        body{
            color:white;
        }

        h1, h2, h3, h4, h5, h6{
            font-family: comic_zine_otregular;
        }


        body{
            font-family: monospace;
        }
        .message{
            font-family: fantasy;
        }

        .infobit-controls{
            padding: 10px;
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


        /** Bootstrap Dark Modal Styles **/
        .modal-open .modal, .btn:focus {
            outline: none !important;
        }

        .modal select{
            background: black;
            font-family: fantasy;
            font-size: 18px;
            cursor: pointer;
        }

        .modal { background-color: #2d3032; }
        .modal .modal-body {
            background: #40464b;
        }

        .modal .modal-header {
            background: #2d3032;
            border-bottom: 1px solid #2a2c2e;
        }
        .modal .modal-header h1, .modal .modal-header h2, .modal .modal-header h3, .modal .modal-header h4 {
            color: #ccc;
        }

        .modal .modal-footer {
            background: #2d3032;
            border-top: 1px solid #2a2c2e;
            -webkit-box-shadow: none;
            -moz-box-shadow: none;
            box-shadow: none;
        }
        .ball {
            background-color: rgba(0,0,0,0);
            border: 5px solid rgba(0,183,229,0.9);
            opacity: .9;
            border-top: 5px solid rgba(0,0,0,0);
            border-left: 5px solid rgba(0,0,0,0);
            border-radius: 50px;
            box-shadow: 0 0 35px #2187e7;
            width: 50px;
            height: 50px;
            margin: 0 auto;
            -moz-animation: spin .5s infinite linear;
            -webkit-animation: spin .5s infinite linear;
        }

        .ball1 {
            background-color: rgba(0,0,0,0);
            border: 5px solid rgba(0,183,229,0.9);
            opacity: .9;
            border-top: 5px solid rgba(0,0,0,0);
            border-left: 5px solid rgba(0,0,0,0);
            border-radius: 50px;
            box-shadow: 0 0 15px #2187e7;
            width: 30px;
            height: 30px;
            margin: 0 auto;
            position: relative;
            top: -50px;
            -moz-animation: spinoff .5s infinite linear;
            -webkit-animation: spinoff .5s infinite linear;
        }

        @-moz-keyframes spin {
            0% {
                -moz-transform: rotate(0deg);
            }

            100% {
                -moz-transform: rotate(360deg);
            };
        }

        @-moz-keyframes spinoff {
            0% {
                -moz-transform: rotate(0deg);
            }

            100% {
                -moz-transform: rotate(-360deg);
            };
        }

        @-webkit-keyframes spin {
            0% {
                -webkit-transform: rotate(0deg);
            }

            100% {
                -webkit-transform: rotate(360deg);
            };
        }

        @-webkit-keyframes spinoff {
            0% {
                -webkit-transform: rotate(0deg);
            }

            100% {
                -webkit-transform: rotate(-360deg);
            };
        }
    </style>
    <script src="bower/jquery/dist/jquery.js"></script>
    <script src="bower/jsviews/jsviews.js"></script>
    <script src="bower/arrive/minified/arrive.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <script src="bower/bootbox.js/bootbox.js"></script>

    <script src="http://d3js.org/d3.v3.min.js"></script>
    <script src="dndTree.js"></script>
    <script src="http://3Dmol.csb.pitt.edu/build/3Dmol-nojquery.js"></script>
    <script id="theTmpl" type="text/x-jsrender">
            {^{if ~root.currentDrug==null}}
                <div style="margin-top:100px"><div class="ball"></div>
                    <div class="ball1"></div></div>
            @{{else}}
             <div> <h4 style="color:lightcoral">Dr. {{$name}}'s Stealth</h4>
                <div id="container">
                    <div class="barOuter"><div class="barInner" data-link="css-width{:100*~root.credit/100+'%'}" ></div><div class="barLabel"> {^{:100*~root.credit/100}}%</div></div>
                </div>



            </div>


            <div class="infobit-controls">
                {^{if ~root.selectedNode!=null}}
                <h2 style="color:cyan">Infobit: {^{:~root.selectedNode.name}}</h2>
                 {^{if ~root.selectedNode.root}}
                    <p style="margin-bottom:50px">{^{:~root.selectedNode.description}}</p>
                 @{{/if}}

        {^{if ~root.selectedNode.locked}}


        @{{/if}}
        <div>
            {^{if !~root.selectedNode.locked}}

                {^{if ~root.selectedNode.name=="structure"}}

                    <div style="height: 370px; width: 370px; position: relative;" class='viewer_3Dmoljs' data-href='mol/@{{:~root.selectedNode.value}}.sdf'  data-type="sdf" data-backgroundcolor='0x00000000' data-style='stick'></div>

                        @{{else}}
        <p class="text-justify" style="font-family:monospace;">{^{:~root.selectedNode.value}}</p>

    @{{/if}}
        @{{else}}

        {^{if ~root.credit-~root.selectedNode.cost<0}}
         <div class="message">You do not have enough stealth to retrieve this infobit. Try another infobit, or, even better, guessing the drug!</div>
        @{{else}}
        <div class="message"> Give away {^{:~root.selectedNode.cost}}% of your stealth to <button class="blue small awesome button" id="retrieve-btn">retrieve</button> this infobit.</div>

       @{{/if}}

        {^{if ~root.selectedNode.root}}
            <div class="message">Even better, try to <button class="blue small awesome button" id="guess-btn">guess</button> this drug. If you guess correct, you fill up your stealth with an extra {^{:~root.selectedNode.cost}}%. Guess wrong and you loose {^{:~root.selectedNode.cost/2}}%.</div>
        @{{/if}}
        @{{/if}}
        </div>

    @{{/if}}



            </div>
{^{if !~root.guidehidden}}
<div class="alert alert-danger fade in" id="guide">
    <a href="#" class="close" data-dismiss="alert">&times;</a>
    <strong>Sssshhh! Welcome!</strong> <p> Click on the hidden infobits of the knowledge net. Each time you retrieve an infobit from the Battle Computer, your stealth decreases. </p> <p>Try to recover as few infobits as possible, in order to guess the drug that lies at the root of the net. </p><p> Go on and guess as many drugs as you can to bring health back to our fellow humans!</p>
</div>

@{{/if}}
            <div id="inventory">
                <h3 style="color:gold">Discovered Drugs Inventory</h3>
                 <ul>
                    {^{for ~root.discoveredDrugs}}
                        <li>{^{:value}}</li>
                    @{{/for}}
                </ul>

                {^{if ~root.discoveredDrugs.length<1}}
                    <div>You have not discovered any drug yet.</div>
                @{{/if}}

            </div>

        @{{/if}}





        </script>
    <script id="theListTmpl" type="text/x-jsrender">
        <select name="guess-list" id="guess-list">
           {^{for allDrugs}}
                <option value="@{{:id}}">{^{:title}}</option>

           @{{/for}}
        </select>


    </script>

</head>

<body style="background: url('{{URL::asset("images/lab.jpg")}}'); background-size: cover;">




<div id="tree-container" class="col-sm-8"></div>

<div id="control-panel" class="col-sm-4">

    <div id="result"></div>
    <script>



    </script>
</div>

<div id="inventory"></div>
</body>


<script>
    $(document).ready(function() {
        $3Dmol.viewers = {};
        var nviewers = 0;
        $(document).arrive(".viewer_3Dmoljs",  function() {
            var viewerdiv = $(this);
            var datauri = null;
            if(viewerdiv.css('position') == 'static') {
                //slight hack - canvas needs this element to be positioned
                viewerdiv.css('position','relative');
            }

            var callback = (typeof(window[viewerdiv.data("callback")]) === 'function') ?
                    window[viewerdiv.data("callback")] : null;

            var type = null;
            if (viewerdiv.data("pdb")) {
                datauri = "http://www.rcsb.org/pdb/files/" + viewerdiv.data("pdb") + ".pdb";
                type = "pdb";
            } else if(viewerdiv.data("cid")) {
                //this doesn't actually work since pubchem does have CORS enabled
                type = "sdf";
                datauri = "http://pubchem.ncbi.nlm.nih.gov/rest/pug/compound/cid/" + viewerdiv.data("cid") +
                        "/SDF?record_type=3d";
            }
            else if (viewerdiv.data("href"))
                datauri = viewerdiv.data("href");

            var bgcolor = $3Dmol.CC.color(viewerdiv.data("backgroundcolor"));
            var style = {line:{}};
            if(viewerdiv.data("style")) style = $3Dmol.specStringToObject(viewerdiv.data("style"));
            var select = {};
            if(viewerdiv.data("select")) select = $3Dmol.specStringToObject(viewerdiv.data("select"));
            var selectstylelist = [];
            var surfaces = [];
            var labels = [];
            var d = viewerdiv.data();

            //let users specify individual but matching select/style tags, eg.
            //data-select1 data-style1
            var stylere = /style(.+)/;
            var surfre = /surface(.*)/;
            var reslabre = /labelres(.*)/;
            var keys = [];
            for(var dataname in d) {
                if(d.hasOwnProperty(dataname)) {
                    keys.push(dataname);
                }
            }
            keys.sort();
            for(var i = 0; i < keys.length; i++) {
                var dataname = keys[i];
                var m = stylere.exec(dataname);
                if(m) {
                    var selname = "select"+m[1];
                    var newsel = $3Dmol.specStringToObject(d[selname]);
                    var styleobj = $3Dmol.specStringToObject(d[dataname]);
                    selectstylelist.push([newsel,styleobj]);
                }
                m = surfre.exec(dataname);
                if(m) {
                    var selname = "select"+m[1];
                    var newsel = $3Dmol.specStringToObject(d[selname]);
                    var styleobj = $3Dmol.specStringToObject(d[dataname]);
                    surfaces.push([newsel,styleobj]);
                }
                m = reslabre.exec(dataname);
                if(m) {
                    var selname = "select"+m[1];
                    var newsel = $3Dmol.specStringToObject(d[selname]);
                    var styleobj = $3Dmol.specStringToObject(d[dataname]);
                    labels.push([newsel,styleobj]);
                }
            }

            //apply all the selections/styles parsed out above to the passed viewer
            var applyStyles = function(glviewer) {
                glviewer.setStyle(select,style);
                for(var i = 0; i < selectstylelist.length; i++) {
                    var sel = selectstylelist[i][0] || {};
                    var sty = selectstylelist[i][1] || {"line":{}}
                    glviewer.setStyle(sel, sty);
                }
                for(var i = 0; i < surfaces.length; i++) {
                    var sel = surfaces[i][0] || {};
                    var sty = surfaces[i][1] || {}
                    glviewer.addSurface($3Dmol.SurfaceType.VDW, sty, sel, sel);
                }
                for(var i = 0; i < labels.length; i++) {
                    var sel = labels[i][0] || {};
                    var sty = labels[i][1] || {}
                    glviewer.addResLabels(sel, sty);
                }

                glviewer.zoomTo();
                glviewer.render();
            }


            var glviewer = null;
            try {
                glviewer = $3Dmol.viewers[this.id || nviewers++] = $3Dmol.createViewer(viewerdiv, {defaultcolors: $3Dmol.rasmolElementColors});
                glviewer.setBackgroundColor(bgcolor);
            } catch ( error ) {
                //for autoload, provide a useful error message
                window.location = "http://get.webgl.org";
            }

            if (datauri) {

                type = viewerdiv.data("type") || viewerdiv.data("datatype") || type;
                if(!type) {
                    type = datauri.substr(datauri.lastIndexOf('.')+1);
                }

                $.get(datauri, function(ret) {
                    glviewer.addModel(ret, type);
                    applyStyles(glviewer);
                    if (callback)
                        callback(glviewer);
                }, 'text');
            }

            else {

                if (viewerdiv.data("element")) {
                    var moldata = $("#" + viewerdiv.data("element")).val() || "";
                    var type = viewerdiv.data("type") || viewerdiv.data("datatype");

                    if (!type){

                        console.log("Warning: No type specified for embedded viewer with moldata from " + viewerdiv.data("element") +
                                "\n assuming type 'pdb'")

                        type = 'pdb';
                    }

                    glviewer.addModel(moldata, type);
                }

                applyStyles(glviewer);
                if (callback)
                    callback(glviewer);
            }

        });

    });
</script>

</html>
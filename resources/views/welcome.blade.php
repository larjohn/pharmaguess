<!DOCTYPE html>
<html>
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <meta charset="utf-8">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <style type="text/css">

        .node, .link {
            cursor: pointer;
        }

        .overlay {
            background-color: #EEE;
        }

        .node circle {
            fill: #fff;
            stroke: steelblue;
            stroke-width: 2px;
        }

        .node text {
            font-size: 10px;
            font-family: sans-serif;
        }

        .link {
            fill: none;
            stroke: #ccc;
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
                <div>Connecting...</div>
            @{{else}}
             <div>Credit:€{^{:~root.credit}}</div>

                {^{if ~root.selectedNode!=null}}
                <h4>{^{:~root.selectedNode.name}}</h4>
                 {^{if ~root.selectedNode.root}}
                    <p>{^{:~root.selectedNode.description}}</p>
                 @{{/if}}

                {^{if ~root.selectedNode.locked}}


                    €{^{:~root.selectedNode.cost}}
                @{{/if}}
                <div>
                    {^{if !~root.selectedNode.locked}}

                        {^{if ~root.selectedNode.name=="structure"}}

                            <div style="height: 400px; width: 400px; position: relative;" class='viewer_3Dmoljs' data-href='mol/@{{:~root.selectedNode.value}}.sdf'  data-type="sdf" data-backgroundcolor='0xffffff' data-style='stick'></div>

                        @{{else}}
                            {^{:~root.selectedNode.value}}

                        @{{/if}}
                    @{{else}}

                       {^{if ~root.credit-~root.selectedNode.cost<0}}
                        <div>You do not have enough credit to reveal this information. Try another information node, or, even better, guessing the drug!</div>
                       @{{else}}
                        <button id="retrieve-btn">Retrieve</button>
                       @{{/if}}

                     {^{if ~root.selectedNode.root}}
                         <button id="guess-btn">Guess</button>
                     @{{/if}}
                @{{/if}}
                    </div>

                @{{/if}}


            <h3>Discovered Drugs Inventory</h3>
            <ul>
                {^{for ~root.discoveredDrugs}}
                    <li>{^{:value}}</li>
                @{{/for}}
            </ul>

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

<body class="row">




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
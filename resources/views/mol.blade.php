<!DOCTYPE html>
<html>
<head>
    <script src="bower/jquery/dist/jquery.js"></script>

    <script src="http://ajax.googleapis.com/ajax/libs/dojo/1.9.3/dojo/dojo.js" type="text/javascript"></script>

    <script src="Scilligence.JSDraw2.Pro.js"></script>



</head>

<body class="row">



    <div id="div1"></div>
    <script type="text/javascript">

        function loadSdf() {

            $.ajax('{{URL::to("mol/2381.sdf")}}', function (data) {
                if (data == null || data == "")
                    return;

                if (jss == null)
                    jss = new JSDraw2.Table(null, { data: ret.data, spreadsheet: true, searchable: true, removehydrogens: true }, "div1");
                else
                    jss.setSdf(ret.data, null, null, true);
            });


        }

        loadSdf();

    </script>


</body>
</html>
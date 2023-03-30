<html>

<head>
    <title>Grid - Spielerei</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link rel="stylesheet" href="./css/bootstrap_4.0.0_css_bootstrap.min.css" />

    <style type="text/css">
        #grindInh{
            display: grid;
            grid-column-gap: 20px;
            grid-row-gap: 20px;
            grid-template-columns: 1fr 1fr 1fr 1fr 1fr 1fr;
            grid-template-rows:  1fr 1fr 1fr 1fr 1fr;
        }

        #grindInh .element{
            width: 50px;
            height: 50px;
        }

        #grindInh .element.st0{
            background-color: red;
        }

        #grindInh .element.st1{
            background-color: green;
        }

        #grindInh .element.st2{
            background-color: blue;
        }

        #grindInh .element.st3{
            background-color: yellow;
        }

        #grindInh .element.st4{
            background-color: purple;
        }

        
     </style>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div id="grindInh"></div>
            </div>
        </div>
    </div>

    <script type="text/javascript" src="./js/jquery-3.2.1.min.js"></script>
    <script type="text/javascript">

        var arrElem = [];

        class pElement
        {
            getRandomInt(max) {
                return Math.floor(Math.random() * Math.floor(max));
            }


            constructor (px, py)
            {
                this.px = px;
                this.py = py;
                this.st = this.getRandomInt(5);
            }


            getHtmlElement()
            {
                var html = '<div id="element_'+this.px+'_'+this.py+'" class="element st'+this.st+'" style="grid-column: '+this.px+'; grid-row: '+this.py+';"></div>';
                return html;
            }


        }

        function showElements()
        {
            $('#grindInh').html('');

            for(let x in arrElem){
                for(let y in arrElem[x]){
                    let html = arrElem[x][y].getHtmlElement();
                    $(html).appendTo($('#grindInh'));
                }
            }
        }

        function checkAktiveElements()
        {
            for(let x in arrElem){
                for(let y in arrElem[x]){
                    let html = arrElem[x][y].getHtmlElement();
                    $(html).appendTo($('#grindInh'));
                }
            }

        }

        $(function(){

            
            var mX = 6;
            var mY = 5;

            for(x = 1; x<=mX;x++){
                arrElem[x] = [];
                for(y=1;y<=mY;y++){
                    arrElem[x][y] = new pElement(x,y);
                }
            }

            showElements();

        });

    </script>
</body>

</html>
<html>

<head>
    <title>SVG - Grafik - Kurven</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link rel="stylesheet" href="./css/bootstrap_4.0.0_css_bootstrap.min.css" />

    <style type="text/css">
        #imgWrapper {
            min-height: 300px;
        }

        #imgWrapper svg {
            width: 100%;
            height: auto;
            max-height: 800px;
        }

        #fileupload {
            display: none !important;
        }

        input.inpVal {
            width: 4em;
        }

        #vorlage {
            display: none !important;
        }

        .inpWrapper {
            margin-bottom: 0.5em;
        }

        .inpWrapper:nth-child(2n) {
            background-color: #ededed;
        }

        .tension {
            width: 3em;
        }

        .rangeSlider {
            width: 100%;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1>Kurven als Vektor</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-12" id="imgWrapper">

            </div>
        </div>
        <form id="parameter" action="javascript:;">
            <div class="row">
                <div class="col-12 text-right">
                    <input id="addGraph" class="btn btn-primary" type="button" value="+" title="neuer Graph" />
                </div>
            </div>
            <div id="graphWrapper"></div>
            <div class="row justify-content-start">
                <div class="col-auto">
                    <input id="downloadJson" class="btn btn-primary" type="button" value="Export" title="Daten exportieren" />
                </div>
                <div class="col-auto">
                    <input id="importJson" class="btn btn-primary" type="button" value="Import" title="Daten importieren" />
                </div>
                <div class="col-auto ml-auto">
                    <input id="downloadImg" class="btn btn-primary" type="button" value="download" title="Bild herunterladen" />
                </div>
                <div class="col-auto text-right ml-auto">
                    <input id="renderImg" class="btn btn-primary" type="button" value="aktualisieren" title="Bild berechnen" />
                </div>
            </div>
        </form>
        <div id="vorlage">
            <div class="row inpWrapper mt-5">
                <div class="row w-100">
                    <div class="col-auto">
                        <div class="col-auto">
                            <div class="row h-50 trenner">
                                <div class="col-auto grName">Graph #</div>
                            </div>
                            <div class="row h-50">
                                <div class="col-auto"><input class="clSelect" name="color" type="color" value="#000000" /></div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="row inpWrappTbl">
                            <div class="col-auto inpTbl mb-2">
                                <div class="row h-50 trenner">
                                    <div class="col-auto">
                                        <input class="inpVal inpx" type="text" name="inpx" value="" placeholder="x" />
                                    </div>
                                </div>
                                <div class="row h-50">
                                    <div class="col-auto">
                                        <input class="inpVal inpy" type="text" name="inpy" value="" placeholder="y" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="row">
                            <div class="col-auto">
                                <div class="row h-50 trenner">
                                    <div class="col-auto">x</div>
                                </div>
                                <div class="row h-50">
                                    <div class="col-auto">y</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row w-100 mt-2">
                    <div class="col-12">
                        <div class="row">
                            <div class="col-auto">
                                <label>Kurvenkorrektur</label>
                            </div>
                            <div class="col-auto"><input type="text" name="tension" class="tension" value="0">&permil;</div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <input type="range" min="-1000" max="1000" value="0" name="inpSld" id="inpSld" class="rangeSlider">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <form id="fileupload" action="javascript:;">
        <input id="fileimport" type="file" />
    </form>

    <script type="text/javascript" src="./js/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="./js/graph.js">


    </script>
</body>

</html>
<?php

include './cls/pxy.php';
include './cls/graph.php';
include './cls/svgGraph.php';


$svgGraph = new svgGraph();

foreach($_POST as $key=>$val){
    if(substr($key,0,4)==='inpx'){
        $gId = str_replace('inpx','',$key);

        if(isset($_POST['inpy'.$gId])){
            $valY = $_POST['inpy'.$gId];
        }

        $color = '#000000';
        if(isset($_POST['color'.$gId])){
            $color = $_POST['color'.$gId];
        }


        $g = $svgGraph->addGraph($color);
        foreach($val as $aId=>$px){
            $py = $valY[$aId];
            if(trim($px)!=='' && trim($py)!==''){
                $g->addPoint((float) $px,(float) $py);
            }
        }

        if(isset($_POST['tension'.$gId])){
            $val = (float) $_POST['tension'.$gId] / 10000;
            $g->setTension($val);
        }
    }
}


header('Content-Type: image/svg+xml');


echo $svgGraph->getSvg();



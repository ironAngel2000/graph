<?php

class svgGraph
{
    protected $graphs = [];

    public function __construct()
    {
        
    }

    public function addGraph($color = '#000000') : graph
    {
        $graph = new graph($color);

        $this->graphs[] = & $graph;

        return $graph;
    }

    protected function getMaxVals()
    {
        $x = 0;
        $y = 0;
        $lx = [];
        $ly = [];

        foreach($this->graphs as $graph){
            if($graph instanceof graph){
                $coords = $graph->getCoords();
                foreach($coords as $p){
                    $x = max($x,$p->Cx());
                    $y = max($y,$p->Cy());
                    $lx[] = $p->Cx();
                    $ly[] = $p->Cy();
                }
            }
        }

        return ['x'=>$x,'y'=>$y,'lx'=>$lx,'ly'=>$ly];
    }

    public function getSvg()
    {
        $maxVals = $this->getMaxVals();

        $width = $maxVals['x'] + 100;
        $height = $maxVals['y'] + 100;
        
        $svg = '<?xml version="1.0" encoding="UTF-8"?>';
        $svg .= '<svg xmlns="http://www.w3.org/2000/svg"
        xmlns:xlink="http://www.w3.org/1999/xlink"
        version="1.1" baseProfile="full"
        width="'.$width.'" height="'.$height.'"
        viewBox="-50 -'.$height.' '.($width + 50).' '.($height + 50).'"
        >';

        $svg .= '<line x1="-50" y1="0" x2="'.$width.'" y2="0" stroke="black" />';
        $svg .= '<line x1="0" y1="-'.$height.'" x2="0" y2="50" stroke="black" />';

        for($x = -0; $x < $width; $x+=50){
            if($x!==0){
                $svg .= '<text x="'.$x.'" y="20" fill="black">'.$x.'</text>';
            }
        }

        foreach($maxVals['ly'] as $y){
            if((int)$y!==0){
                $svg .= '<line x1="0" y1="-'.$y.'" x2="'.$width.'" y2="-'.$y.'" stroke="#dddddd" />';
            }
        }

        for($y = -0; $y < $height; $y+=50){
            if($y!==0){
                $svg .= '<text x="-50" y="'.($y * -1).'" fill="black">'.$y.'</text>';
            }
        }


        foreach($maxVals['lx'] as $x){
            if((int)$x!==0){
                $svg .= '<line x1="'.$x.'" y1="-'.$height.'" x2="'.$x.'" y2="0" stroke="#dddddd" />';
            }
        }


        foreach($this->graphs as $graph){

            if($graph instanceof graph){

                $color = $graph->getColor();

                $coords = $graph->getCoords(true);
                if(count($coords)==1){
                    $p = $coords[0];
                    $svg .= '<path d="M'.$p->cX().' '.$p->cY().'"/>';
                    $svg .= '<circle cx="'.$p->cX().'" cy="'.$p->cY().'" r="2" fill="'.$color.'"  transform="scale(1,-1)"/>';
                }
                elseif(count($coords)==2){
                    $p1 = $coords[0];
                    $p2 = $coords[1];

                    $svg .= '<path d="M'.$p1->cX().' '.$p1->cY().'"/>';

                    //$svg .= '<circle cx="'.$p1->cX().'" cy="'.$p1->cY().'" r="2" fill="'.$color.'"  transform="scale(1,-1)"/>';
                    //$svg .= '<circle cx="'.$p2->cX().'" cy="'.$p2->cY().'" r="2" fill="'.$color.'"  transform="scale(1,-1)"/>';


                    $svg .= '<line x1="'.$p1->cX().'" y1="'.$p1->cY().'" x2="'.$p2->cX().'" y2="'.$p2->cY().'" stroke="'.$color.'"   transform="scale(1,-1)"/>';

                }
                elseif(count($coords)> 2){

                    $p = $coords[0];
                    $svg .= '<path d="M'.$p->cX().' '.$p->cY().'"/>';


                    foreach($coords as $i=>$p){
                        if(isset($coords[($i-1)]) && isset($coords[($i+1)])){
                            $y1 = $coords[($i-1)]->cY();
                            $y2 = $coords[($i)]->cY();
                            $y3 = $coords[($i+1)]->cY();

                            $dY = $y3 - $y1;
                            $ab = $dY * ($graph->getTension() * 100);

                            $y2 -= $ab;
                            $coords[$i]->cY($y2);
                        }
                        /*
                        if(isset($coords[($i+1)])){
                            $gy = $coords[$i]->cY();
                            $gy += $coords[($i+1)]->cY();
                            $gy = $gy / 2;
                            $ab = $gy * ($graph->getTension() * 10);
                            $coords[$i]->cY(($coords[$i]->cY() + $ab));
                            $coords[($i+1)]->cY(($coords[($i+1)]->cY() - $ab));
                        }
                        */
                    }



                    foreach($coords as $i=>$p){
                        if(isset($coords[($i+2)])){
                            $fxQ = $graph->getFxQuad($i,($i+1),($i+2),$coords);
                        }

                        if(isset($coords[($i+1)])){
                            $p1 = $coords[$i];
                            $p2 = $coords[($i+1)];

                            $svgPoint = $graph->getSvgQuadBezierPoint($p1,$p2,$fxQ);

                            
                            $x1 = $p1->cX();
                            $x2 = $p2->cX();
                            $y1 = $p1->cY();
                            $y2 = $p2->cY();

                            $qx1 = $svgPoint->Cx();
                            $qy1 = $svgPoint->Cy();
                            $qx2 = $svgPoint->Cx();
                            $qy2 = $svgPoint->Cy();

                            $svg .= '<path d="M'.$x1.' '.$y1.' C '.$qx1.' '.$qy1.' '.$qx2.' '.$qy2.' '.$x2.' '.$y2.'" stroke="'.$color.'" fill="transparent" transform="scale(1,-1)"/>';

                            /*
                            $svg .= '<circle cx="'.$qx1.'" cy="'.$qy1.'" r="1" fill="magenta"  transform="scale(1,-1)"/>';
                            $svg .= '<circle cx="'.$qx2.'" cy="'.$qy2.'" r="1" fill="magenta"  transform="scale(1,-1)"/>';
                            $svg .= '<line x1="'.$qx1.'" y1="'.$qy1.'" x2="'.$x1.'" y2="'.$y1.'" stroke="lime"   transform="scale(1,-1)"/>';
                            $svg .= '<line x1="'.$qx2.'" y1="'.$qy2.'" x2="'.$x2.'" y2="'.$y2.'" stroke="lime"   transform="scale(1,-1)"/>';
//                            $svg .= '<circle cx="'.$p->cX().'" cy="'.$p->cY().'" r="4" fill="blue"  transform="scale(1,-1)"/>';
                            //*/

                            /*
                            for($x = $x1; $x<=$x2;$x++){
                                $y = ($fxQ['a'] * $x * $x) + ($fxQ['b'] * $x) + $fxQ['c'];
                                $svg .= '<circle cx="'.$x.'" cy="'.$y.'" r="1" fill="magenta"  transform="scale(1,-1)"/>';
                            }
                            */

                        }


                    }

                    /*
                    $coordsOrg = $graph->getCoords(false);
                    foreach($coordsOrg as $i=>$p){
//                        $svg .= '<circle cx="'.$p->cX().'" cy="'.$p->cY().'" r="2" fill="lime"  transform="scale(1,-1)"/>';
                    }
                    */

                }



            }

            //break;
        }



        $svg .= '</svg>';

        return $svg;

    }


}

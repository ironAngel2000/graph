<?php


class graph
{
    protected $color;
    protected $coord = [];
    protected $tension = 0;
    public static $decimal = 16;



    public function __construct($color = '#00000')
    {
        $this->color = $color;
    }

    public function getColor()
    {
        return $this->color;
    }

    public function addPoint(float $x, float $y)
    {
        $p = new pxy($x,$y);
        $this->coord[] = $p;


        $sort = [];
        foreach($this->coord as $key=>$p){
            $sort[$p->Cx()] = $p;
        }

        ksort($sort);

        $this->coord = [];
        foreach($sort as $p){
            $this->coord[] = $p;
        }
    }

    public function getCoords() :array
    {
        return $this->coord;
    }

    public function getPoint($id)
    {
        $ret = null;

        if(isset($this->coord[$id])){
            $ret = $this->coord[$id];
        }

        return $ret;
    }

    public function removePoint($id)
    {
        if(isset($this->coord[$id])){
            unset($this->coord[$id]);
        }
    }

    public function setTension(float $tension)
    {
        $this->tension = $tension;
    }

    public function getTension()
    {
        return (float) $this->tension;
    }


    public function getFxQuad($p1=0,$p2=1,$p3=2,$arrCoords=[])
    {
        $sCoords = $this->coord;
        if(count($arrCoords) > 2){
            $sCoords = $arrCoords;
        }


        if(!isset($sCoords[$p1])){
            user_error('Coord p1 ('.$p1.') not found',E_USER_ERROR);
        }
        if(!isset($sCoords[$p2])){
            user_error('Coord p2 ('.$p2.') not found',E_USER_ERROR);
        }
        if(!isset($sCoords[$p3])){
            user_error('Coord p3 ('.$p3.') not found',E_USER_ERROR);
        }

        $x1 = $sCoords[$p1]->Cx();
        $x2 = $sCoords[$p2]->Cx();
        $x3 = $sCoords[$p3]->Cx();

        $y1 = $sCoords[$p1]->Cy();
        $y2 = $sCoords[$p2]->Cy();
        $y3 = $sCoords[$p3]->Cy();

        $a = ($x1*($y2-$y3)+$x2*($y3-$y1)+$x3*($y1-$y2))/(($x1-$x2)*($x1-$x3)*($x3-$x2));
        $b = ($x1*$x1*($y2-$y3)+$x2*$x2*($y3-$y1)+$x3*$x3*($y1-$y2))/(($x1-$x2)*($x1-$x3)*($x2-$x3));
        $c = ($x1*$x1*($x2*$y3-$x3*$y2)+$x1*($x3*$x3*$y2-$x2*$x2*$y3)+$x2*$x3*$y1*($x2-$x3))/(($x1-$x2)*($x1-$x3)*($x2-$x3));

        $a = round($a,graph::$decimal);
        $b = round($b,graph::$decimal);
        $c = round($c,graph::$decimal);

        $ret = [];
        $ret['a'] = $a;
        $ret['b'] = $b;
        $ret['c'] = $c;

        return $ret;
    }

    public function getPointMiddle($p1=0,$p2=1)
    {
        if(!$p1 instanceof pxy){
            if(!isset($this->coord[$p1])){
                user_error('Coord p1 ('.$p1.') not found',E_USER_ERROR);
            }

            $p1 = $this->coord[$p1];
        }

        if(!$p2 instanceof pxy){
            if(!isset($this->coord[$p2])){
                user_error('Coord p2 ('.$p2.') not found',E_USER_ERROR);
            }
            $p2 = $this->coord[$p2];
        }

        $x1 = $p1->Cx();
        $x2 = $p2->Cx();

        $y1 = $p1->Cy();
        $y2 = $p2->Cy();

        $mx = ($x1 + $x2) / 2;
        $my = ($y1 + $y2) / 2;

        return new pxy($mx,$my);
    }

    public function getScheitelpunktFxQuad(array $qFx)
    {
        $a = $qFx['a'];
        $b = $qFx['b'];
        $c = $qFx['c'];


        $xs = $b*(-1) / (2*$a);
        $ys = $c-$b*$b/(4*$a);

        return new pxy($xs,$ys);

    }

    public function getFxMiddleLine($p1,$p2)
    {
        if($p1 instanceof pxy){

        }
        elseif(isset($this->coord[$p1])){
            $p1 = $this->coord[$p1];
        }
        else{
            user_error('Coord p1 ('.$p1.') not found',E_USER_ERROR);
        }

        if($p2 instanceof pxy){

        }
        elseif(isset($this->coord[$p2])){
            $p2 = $this->coord[$p2];
        }
        else{
            user_error('Coord p2 ('.$p2.') not found',E_USER_ERROR);
        }

        $x1 = $p1->Cx();
        $x2 = $p2->Cx();

        $y1 = $p1->Cy();
        $y2 = $p2->Cy();

        $pm = $this->getPointMiddle($p1,$p2);

        $mx = $pm->Cx();
        $my = $pm->Cy();

        $s = ($y2-$y1)/ ($x2 - $x1); // Steigung
        $s = 1/$s; // Kehrwert

        $b = $my - ($s * $mx);

        $s = round($s,graph::$decimal);
        $b = round($b,graph::$decimal);


        $ret = [];
        $ret['a'] = $s;
        $ret['b'] = $b;

        return $ret;

    }

    public function getSchnittpunktFx(array $fx1, array $fx2)
    {
        $qFx = null;
        $l2Fx = null;
        if(count($fx1)==3){
            $qFx = $fx1;
            $lFx = $fx2;
        }
        elseif(count($fx2)==3){
            $qFx = $fx2;
            $lFx = $fx1;
        }
        else{
            $lFx = $fx1;
            $l2Fx = $fx2;
        }

        if(is_array($qFx)){

            $a = $qFx['a'];
            $b = $qFx['b'];
            $c = $qFx['c'];

            $g = $lFx['a'];
            $m = $lFx['b'];

            $b = $b-$g;
            $c = $c-$m;

            $b = $b / $a; // x² muss 1 sein
            $c = $c / $a;
            
            $sqrt = (($b / 2) * ($b / 2)) - $c;

            $sqrt = sqrt($sqrt);

            $p = ($b / 2) * (-1);

            $x1 = $p + $sqrt;
            $x2 = $p - $sqrt;

            $y1 = $g * $x1 + $m;
            $y2 = $g * $x2 + $m;

            $x1 = round($x1,graph::$decimal);
            $x2 = round($x2,graph::$decimal);
            $y1 = round($y1,graph::$decimal);
            $y2 = round($y2,graph::$decimal);

            $ret = [];
            $ret[] = new pxy($x1,$y1); 
            $ret[] = new pxy($x2,$y2); 

        }
        else{

            $a = $lFx['a'];
            $c = $lFx['b'];

            $b = $l2Fx['a'];
            $d = $l2Fx['b'];


            if($a==$b){
                $x1 = NAN;
            }
            else{
                $x1 = ($d -$c) / ($a - $b);
            }
            $y1 = ($a * $x1) + $c;

            $ret = new pxy($x1,$y1); 

        }


        return $ret;

    }

    protected function tangenteQuad(array $qFx, pxy $p)
    {

        $a = $qFx['a'];
        $b = $qFx['b'];
        $c = $qFx['c'];

        $x = $p->Cx();
        $y = $p->Cy();

        //$yFx = ($a * ($x * $x)) + ($b * $x) + $c;

        $m = $a * 2;
        $p = $b;

        $st = ($x * $m) + $p;

        $n = $y - ($st * $x);


        $a = $st;
        $b = $n;

        $ret = [];
        $ret['a'] = $st;
        $ret['b'] = $n;

        return $ret;
    }

    public function getSvgQuadBezierPoint(pxy $p0, pxy $p2, array $qFx)
    {
        
        $x0 = $p0->Cx();
        $x2 = $p2->Cx();

        $y0 = $p0->Cy();
        $y2 = $p2->Cy();

        $a = $qFx['a'];
        $b = $qFx['b'];
        $c = $qFx['c'];

        
        $t0 = $this->tangenteQuad($qFx,$p0);
        $t2 = $this->tangenteQuad($qFx,$p2);


        $sp = $this->getSchnittpunktFx($t0,$t2);

        if(is_nan($sp->Cx()) || is_nan($sp->Cy())){
            $sp = $this->getPointMiddle($p0,$p2);
        }
        else{

            // Senkrechte von Punkt Sp auf die Kurve


            $m = $qFx['a'] * 2;

            $x = $sp->Cx();
            $y = $sp->cY();

            $st = ($x * $m) + $qFx['b'];
            if((float) $st != 0){
                $st = -1 / $st;
            }
            $b = $y - ($st * $x);

            $fxM['a'] = $st;
            $fxM['b'] = $b;

            $this->testVal = $fxM;

            $sFxQFx = $this->getSchnittpunktFx($qFx,$fxM);

            $tPy1 = $sp->cY() - $sFxQFx[0]->cY();
            $tPy1 = abs($tPy1);

            $tPy2 = $sp->cY() - $sFxQFx[1]->cY();
            $tPy2 = abs($tPy2);

            if($tPy1 < $tPy2){
                $tpY = $sFxQFx[0]->cY();
            }
            else{
                $tpY = $sFxQFx[1]->cY();
            }

            if($sp->Cy() < $tpY){
                $sp->cX($sp->cX() * (1 + $this->tension));
            }
            else{
                $sp->cX($sp->cX() *(1 - $this->tension));
            }

            $ty = ($sp->cX() * $fxM['a']) + $fxM['b'];
            $sp->cY($ty);

        }
        
        return $sp;
    }

    public $testVal = [];


    private $iteration = 2;
    private $arrBezier = [];
    private function bezierCasteljauIteration(pxy $p0, pxy $p1, pxy $p2, pxy $p3, int $depth)
    {
        if($depth > $this->iteration){
            $this->arrBezier[] = [$p0, $p1, $p2, $p3];
        }
        else{
            // Hilfspunkte
            $qx01 = ($p0->Cx() + $p1->Cx()) / 2;
            $qy01 = ($p0->Cy() + $p1->Cy()) / 2;

            $qx12 = ($p1->Cx() + $p2->Cx()) / 2;
            $qy12 = ($p1->Cy() + $p2->Cy()) / 2;

            $qx23 = ($p2->Cx() + $p3->Cx()) / 2;
            $qy23 = ($p2->Cy() + $p3->Cy()) / 2;

            $qx012  = ($qx01 + $qx12) / 2;   
            $qy012  = ($qy01 + $qy12) / 2;
            $qx123  = ($qx12 + $qx23) / 2;   
            $qy123  = ($qy12 + $qy23) / 2;
            $qx0123 = ($qx012 + $qx123) / 2; 
            $qy0123 = ($qy012 + $qy123) / 2;

            $q01 = new pxy($qx01,$qy01);
            $q12 = new pxy($qx12,$qy12);
            $q23 = new pxy($qx23,$qy23);
            $q012 = new pxy($qx012,$qy012);
            $q123 = new pxy($qx123,$qy123);
            $q0123 = new pxy($qx0123,$qy0123);

            $depth++;

            $this->bezierCasteljauIteration($p0, $q01, $q012, $q0123, $depth);
            $this->bezierCasteljauIteration($q0123, $q123, $q23, $p3, $depth);
        }
    }

    public function getBezierCasteljau(pxy $p0, pxy $p1, pxy $p2, pxy $p3)
    {
        $this->arrBezier = [];
        $this->bezierCasteljauIteration($p0, $p1, $p2, $p3, 0);
        return $this->arrBezier;
    }

 
    private function pytagorasC(float $side1, float $side2)
    {
        return sqrt(($side1+$side1) + ($side2 * $side2));
    }

    public function splineCurve()
    {
        $ret = [];
        if(count($this->coord)<3){
            $ret = $this->coord;
        }
        else{

            
            $maxLen = count($this->coord) - 1;

            foreach($this->coord as $i=>$pPivot){

                if(isset($this->coord[($i+2)])){
                    $fx = $this->getFxQuad($i,($i+1),$i+2);
                }

                $pPivot->fxQ = $fx;

                if(isset($this->coord[($i+1)])){
                    $pRight = $this->coord[($i+1)];
                }
                else{
                    $pRight = $this->coord[($i)];
                }

                if(isset($this->coord[($i-1)])){
                    $pLeft = $this->coord[($i-1)];
                }
                else{
                    $pLeft = $this->coord[($i)];
                }

                $pCpl = $this->pytagorasC(($pPivot->cX() - $pLeft->cX()),($pPivot->cY()-$pLeft->cY()));
                $pCrp = $this->pytagorasC(($pRight->cX() - $pPivot->cX()),($pRight->cY()-$pRight->cY()));

                $pc = $pCpl + $pCrp;

                if($pc >0 ){
                    $fa = $this->tension * $pCpl / $pc;
                    $fb = $this->tension * $pCrp / $pc;
                }
                else{
                    $fa = 0;
                    $fb = 0;
                }

                $w = $pRight->cX() - $pLeft->cX(); 
                $h = $pRight->cY() - $pLeft->cY(); 

                $fxQ1 = $this->tangenteQuad($fx,(new pxy($w,$h)));
                $pPivot->fxQ1 = $fxQ1;

                $px = $pPivot->cX() - $fa * $w;
                $py = $pPivot->cY() - $fa * $h;
                $pCa = new pxy($px,$py);

                $px = $pPivot->cX() + $fb * $w;
                $py = $pPivot->cY() + $fb * $h;
                $pCb = new pxy($px,$py);

                $ret[$i] = ['p'=>$pPivot,
                            'ca'=>$pCa,
                            'cb'=>$pCb];

            }
        }



        return $ret;

    }

}
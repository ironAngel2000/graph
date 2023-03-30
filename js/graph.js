function base64ToBlob(base64, mimetype, slicesize) {
    if (!window.atob || !window.Uint8Array) {
        // The current browser doesn't have the atob function. Cannot continue
        return null;
    }
    mimetype = mimetype || '';
    slicesize = slicesize || 512;
    var bytechars = atob(base64);
    var bytearrays = [];
    for (var offset = 0; offset < bytechars.length; offset += slicesize) {
        var slice = bytechars.slice(offset, offset + slicesize);
        var bytenums = new Array(slice.length);
        for (var i = 0; i < slice.length; i++) {
            bytenums[i] = slice.charCodeAt(i);
        }
        var bytearray = new Uint8Array(bytenums);
        bytearrays[bytearrays.length] = bytearray;
    }
    return new Blob(bytearrays, {type: mimetype});
};        

var grIndex = 0;

function checkInp($graph)
{
    var $inpWrapper = $graph.find('.inpWrapper');
    $graph.find('.inpVal').each(function(){
        var $inp = $(this);
        var $parent = $inp.parents('.inpTbl').first();
        $inp.on('blur',function(){
            if($parent.hasClass('done')==false){

                if($.trim($inp.val())!==''){
                    $pcl = $parent.clone()
                    $pcl.find('.inpVal').val('');
                    $pcl.appendTo($graph.find('.inpWrappTbl').first());
                    $parent.addClass('done');

                    checkInp($graph);
                }
            }
        });
    });

}

function newGraph()
{
    var $graph = $('#vorlage .row').first().clone();

    grIndex++;
    $graph.find('.grName').html('Graph '+ grIndex);


    $graph.appendTo($('#graphWrapper'));

    checkInp($graph)

    $graph.find('.inpVal.inpx').attr('name','inpx'+grIndex+'[]');
    $graph.find('.inpVal.inpy').attr('name','inpy'+grIndex+'[]');
    $graph.find('.clSelect').attr('name','color'+grIndex);
    $graph.attr('id','inpWrapper'+grIndex);

    var $inpsld = $graph.find('#inpSld').attr('name','inpSld'+grIndex).attr('id','inpSld'+grIndex);
    var $txpsld = $graph.find('.tension').attr('name','tension'+grIndex);

    $inpsld.on('change',function(){
        $txpsld.val($inpsld.val());
        renderImg();
    });

    $txpsld.on('change blur',function(){
        $inpsld.val($txpsld.val());
        renderImg();
    });

}

var ajaxRender = null;

function renderImg()
{

    if(ajaxRender){
        ajaxRender.abort();
        ajaxRender = null;
    }

    var form_data = $('#parameter').serialize();

    ajaxRender = $.ajax({
        url : './genSvg.php',
        type: 'POST',
        data : form_data
    }).done(function(response){ //
        var xmlDoc = (new XMLSerializer().serializeToString(response));
        $('#imgWrapper').html(xmlDoc);
    });
}

$(function(){
    $('#addGraph').on('click',function(){
       newGraph();
    });

    newGraph();


    $('#renderImg').on('click',function(){
        renderImg();
    });

    $('#downloadImg').on('click',function(){
        var form_data = $('#parameter').serialize();

        $.ajax({
            url : './genSvg.php',
            type: 'POST',
            data : form_data
        }).done(function(response){ //
            var xmlDoc = (new XMLSerializer().serializeToString(response));

            var blob = base64ToBlob(btoa(xmlDoc), 'image/svg+xml');
            var url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.style.display = 'none';
            a.href = url;
            // the filename you want
            a.download = 'graph.svg';
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
        });
        
    });

    $('#fileimport').each(function(){
        var $inp = $(this);
        $inp.on('change',function(event){
            var reader = new FileReader();
            reader.onload = onReaderLoad;
            reader.readAsText(event.target.files[0]);
        });

        function onReaderLoad(event){
            $('#graphWrapper').html('');
            grIndex = 0;
            var obj = JSON.parse(event.target.result);
            for(let g in obj){

                if(g != grIndex){
                    newGraph();
                }

                var graph = obj[g];
                var aktGrIndex = grIndex;

                $('input[name=color'+aktGrIndex+']').val(graph.color);
                $('input[name=tension'+aktGrIndex+']').val(graph.tension);
                $('#inpSld'+aktGrIndex+'').val(graph.tension);

                for(let c in graph.coords){
                    var coords = graph.coords[c];
                    var $inpX = $('#inpWrapper'+aktGrIndex+' input.inpx').last();
                    var $inpY = $('#inpWrapper'+aktGrIndex+' input.inpy').last();

                    $inpX.val(coords.x);
                    $inpY.val(coords.y);

                    var $parent = $inpX.parents('.inpTbl').first();
                    if($.trim($inpX.val())!==''){
                        $pcl = $parent.clone()
                        $pcl.find('.inpVal').val('');
                        $pcl.appendTo($('#inpWrapper'+aktGrIndex+' .inpWrappTbl'));
                        $parent.addClass('done');
                        checkInp($('#inpWrapper'+aktGrIndex+' .inpWrappTbl'));
                    }

                }


            }

            renderImg();
        }
    });

    $('#downloadJson').on('click',function(){
        var expData = {};

        var aktGrIndex = 0;
        $('#graphWrapper .inpWrapper').each(function(){
            var aktRow = {};
            aktGrIndex++;
            var $graph =$(this);

            let color = $graph.find('input.clSelect').val();
            aktRow.color= color;

            let tension = $graph.find('input.tension').val();
            aktRow.tension= tension;

            var coords = {};
            var cind = 0;
            $graph.find('.inpTbl').each(function(){

                var $tbl = $(this);

                var $inpX = $tbl.find('input.inpx');
                var $inpY = $tbl.find('input.inpy');
            
                if($.trim($inpX.val())!==''){
                    var aktCoord = {};
                    aktCoord.x = $inpX.val();
                    aktCoord.y = $inpY.val();
                    coords[cind] = aktCoord;
                    cind++;
                }

            });
            aktRow.coords = coords;
            expData[aktGrIndex] = aktRow;


        });


        var jsondata = JSON.stringify(expData);

        var blob = base64ToBlob(btoa(jsondata), 'application/json');
        var url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.style.display = 'none';
        a.href = url;
        // the filename you want
        a.download = 'export.json';
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
    });


    $('#importJson').on('click',function(){
        $('#fileimport').trigger('click');
    });
});
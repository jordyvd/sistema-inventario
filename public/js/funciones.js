function enviarInfoSunat(params) {
    $.ajax("http://localhost/testcpe/api_cpe/RecibeDatos.php", {
        type: 'POST',
        dataType: 'json',
        data: {
            'nombre': params.nombre,
            'total_pagar': '74.34',
            'total_letras': $("#result_precio_texto").val(),
            'num_venta': 'F001-0000001',
            'documento': 'factura',
        }
    }).then(function (res) {
        console.log(res);
    });
}
function zeroPad(num, numZeros) {
    var n = Math.abs(num);
    var zeros = Math.max(0, numZeros - Math.floor(n).toString().length);
    var zeroString = Math.pow(10, zeros).toString().substr(1);
    if (num < 0) {
        zeroString = '-' + zeroString;
    }

    return zeroString + n;
}
$("#refrescar").click(function () {
    location.reload();
});
$(document).ready(function () {
    setTimeout(function () { $("#bienvenida").fadeOut(1500); }, 3800);
})
function Full_W_P(item) {
    let $fecha = "";
    if (item.fecha_t === "" || item.fecha_t === null) {
        $fecha = item.fecha;
    } else {
        $fecha = item.fecha_t;
    }
    // let url = "http://localhost/printticked/print_pdf.php?codigo="+item.cod_sucursal + "&cliente="+item.nombre_cliente +"&fecha="+$fecha;
    let url = "imprimirticked/" + item.cod_sucursal;
    // params  = 'width='+screen.width;
    params = 'width= 400';
    params += ', height=' + screen.height;
    params += ', top=0, left=500'
    params += ', fullscreen=yes';
    params += ', directories=no';
    params += ', location=no';
    params += ', menubar=no';
    params += ', resizable=no';
    params += ', scrollbars=no';
    params += ', status=no';
    params += ', toolbar=no';


    newwin = window.open(url, 'FullWindowAll', params);
    if (window.focus) { newwin.focus() }
    return false;
}
// ***** solo numero *****
$(".solonumero").keydown(function (event) {
    //alert(event.keyCode);
    if ((event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105) && event.keyCode !== 190 && event.keyCode !== 110 && event.keyCode !== 8 && event.keyCode !== 9) {
        return false;
    }
});
// ********* click derecho *********
$(document).ready(function () {
    $("#table-productos").mousedown(function (e) {
        //1: izquierda, 2: medio/ruleta, 3: derecho
        if (e.which == 3) {
            $("#modal_kardex").click();
        }
    });
});

$(document).keypress(function (e) {
    if (e.charCode == 61) {
        document.getElementById("nuevaventa").click();
    }
});
// $(document).keypress(function (e) {
//     if (e.charCode == 119) {
//         document.getElementById("producto_crud").click();
//     }
// });
$(document).keypress(function (e) {
    if (e.charCode == 59) {
        document.getElementById("inventario_click").click();
    }
});


// ******************* CAROUSEL ********************

var current = 0;
var imagenes = new Array();

$(document).ready(function () {
    var numImages = 6;
    if (numImages <= 3) {
        $('.right-arrow').css('display', 'none');
        $('.left-arrow').css('display', 'none');
    }

    $('.left-arrow').on('click', function () {
        if (current > 0) {
            current = current - 1;
        } else {
            current = numImages - 3;
        }

        $(".carrusel").animate({ "left": -($('#product_' + current).position().left) }, 600);

        return false;
    });

    $('.left-arrow').on('hover', function () {
        $(this).css('opacity', '0.5');
    }, function () {
        $(this).css('opacity', '1');
    });

    $('.right-arrow').on('hover', function () {
        $(this).css('opacity', '0.5');
    }, function () {
        $(this).css('opacity', '1');
    });

    $('.right-arrow').on('click', function () {
        if (numImages > current + 3) {
            current = current + 1;
        } else {
            current = 0;
        }

        $(".carrusel").animate({ "left": -($('#product_' + current).position().left) }, 600);

        return false;
    });

});
// $(document).ready(function () {
//     $("body").tooltip({ selector: '[data-toggle=tooltip]' });
// });


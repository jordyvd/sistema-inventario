<?php
$medidaTicket = 180;
// $codigoF = "V001-000000081"
?>
<!DOCTYPE html>
<html>

<head>
    <style>
        * {
            font-size: 8px;
            font-family: 'DejaVu Sans', serif;
        }
        .title{
             /* border-top:1px dashed black;
             border-bottom:1px dashed black; */
        }
        h1 {
            font-size: 8px;
        }

        .ticket {
            margin: 2px;
        }

        td,
        th,
        tr,
        table {
            /* border-top: 1px dashed black; */
            border-collapse: collapse;
            margin: 0 auto;
        }
        table{
            width:100%;
        }
        td.precio {
            text-align: center;
            font-size: 8px;
        }

        td.cantidad {
            font-size: 8px;
            text-align: center;
        }

        td.producto {
            text-align: left;
            font-size: 6px;
        }

        th {
            text-align: center;
        }

        .txt-left{
            text-align:left;
            /* padding:0 6px; */
        }
        .centrado {
            text-align: center;
            align-content: center;
        }

        .ticket {
            width: <?php echo $medidaTicket ?>px;
            max-width: <?php echo $medidaTicket ?>px;
        }

        img {
            /* max-width: inherit;
            width: inherit; */
            width:110px;
            /* padding-bottom:12px; */
        }

        * {
            margin: 0;
            padding: 0;
        }

        .ticket {
            margin: 0;
            padding: 4;
        }
        .pdd-3{
            padding:1px 0;
        }
        small{
            font-size:5px;
        }
        .text-left{
            text-align:left;
        }
        .rem{
            width:8rem;
        }
        .footer{
            padding:15px 0;
        }
        body {
            text-align: center;
        }
        .montletra{
            /* border-top:1px dashed black; */
            border-top: 0.5px solid;
        }
        .image{
            padding:0;
            margin-top:-5px;
        }
        .content{
            margin-top:-10px;
        }
    </style>
</head>

<body>
    <div class="ticket centrado">
        <div class="image"><img src="images/logosinfondo.png" ></div>
        <div class="content">
        <p>RUC: 10405163131</p>
        <p><?php foreach($datos['venta'] as $venta) {
            if($venta['sucursal'] == "huaral"){
                echo "Calle Morales Bermúdez # 340 y 342";
            }else if($venta['sucursal'] == "lima-abancay"){
                echo "Av. Abancay # 368 Int. 1090 - Galería La Casona";
            }else if($venta['sucursal'] == "barranca"){
                echo "JR. Castilla 148";
            }
        } ?></p>
        <p>Teléfono: 
        <?php 
            foreach($datos['venta'] as $venta) {
                if($venta['sucursal'] == "huaral"){
                    echo "5845067";
                }else if($venta['sucursal'] == "lima-abancay"){
                    echo "937522124";
                }else if($venta['sucursal'] == "barranca"){
                    echo "5984852";
                }
            }
        ?> </p>
            <div class="title"><b>TICKED DE COMPRA</b>: <?php foreach($datos['venta'] as $venta) {
            echo $venta['cod_sucursal'];
        } ?> </div>
        <div class="pdd-3">
        <p class="txt-left"><b>cliente</b>: <?php
        // if($cliente == "null"){
            foreach($datos['venta'] as $venta) {
                if($venta['nombre_cliente'] == ""){
                    echo "varios";
                }else{
                    echo $venta['nombre_cliente'];
                }
            }
        // }else{
            // echo $cliente;
     ?></p>
        <p class="txt-left"><b>fecha</b>: <?php foreach($datos['venta'] as $venta) {
            echo $venta['fecha_t'];
        } ?></p>
        </div>
        <?php
        ?>

        <table>
            <thead>
                <tr class="centrado">
                    <th class="text-left">COD.</th>
                    <th class="precio">DESCRIP.</th>
                    <th class="precio">CANT.</th>
                    <th class="precio">DTO</th>
                    <th class="precio">P.UNIT</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total = 0;
                foreach ($datos['detalles'] as $producto) {
                    $total += $producto["cantidad"] * $producto["precio"] - $producto["descuento"];
                ?>
                    <tr>
                        <td class="producto"><?php echo $producto["codigo"] ?></td>
                        <td class="producto rem"><?php echo $producto["nompro"] ?></td>
                        <td class="cantidad"><?php echo number_format($producto["cantidad"], 2) ?></td>
                        <td class="precio"><?php echo number_format($producto["descuento"], 2) ?></td>
                        <td class="precio"><?php echo number_format($producto["precio"], 2) ?></td>
                    </tr>
                <?php } ?>
            </tbody>
            <tr>
                <td class="cantidad"></td>
                <td class="producto rem"></td>
                <td class="cantidad"></td>   
                <td class="producto"><strong>TOTAL</strong></td>
                <td class="precio"><?php echo number_format($total, 2) ?></td>
            </tr>
        </table>
        <div class="montletra text-left">
        <?php echo "SON: ".$datos['total']; ?>
        </div>
        <p class="centrado footer">¡GRACIAS POR SU COMPRA!
            <br><small>no se aceptan cambios, después de una semana</small></p>
        </div>
    </div>
</body>

</html>
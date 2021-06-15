import Vue from 'vue'
import Router from 'vue-router'

Vue.use(Router)
export default new Router({
    routes:[
        {
            path:'/',
            name:'home',
            component : require('./views/home').default
        },
        {
            path:'/products',
            name:'products',
            component : require('./views/productos').default
        },
        {
            path:'/trasladar',
            name:'trasladar',
            component : require('./views/trasladar').default
        },
        {
            path:'/traslados',
            name:'traslados',
            component : require('./views/traslados').default
        },
        {
            path:'/ingresos',
            name:'ingresos',
            component : require('./views/ingresos').default
        },
        {
            path:'/ventas',
            name:'ventas',
            component : require('./views/ventas').default
        },
        {
            path:'/clientes',
            name:'clientes',
            component : require('./views/clientes').default
        },
        {
            path:'/caja',
            name:'caja',
            component : require('./views/caja').default
        },
        {
            path:'/marcas',
            name:'marcas',
            component : require('./views/marcas').default
        },
        {
            path:'/sucursal',
            name:'sucursal',
            component : require('./views/sucursal').default
        },
        {
            path:'/roles',
            name:'roles',
            component : require('./views/roles').default
        },
        {
            path:'/permisos',
            name:'permisos',
            component : require('./views/permisos').default
        },
        {
            path:'/usuarios',
            name:'usuarios',
            component : require('./views/usuarios').default
        },
        {
            path:'/almacen_inventario',
            name:'almacen_products',
            component : require('./views/almacen_products').default
        },
        {
            path:'/agregar_producto_nuevo',
            name:'agregar_product',
            component : require('./views/agregarproduct').default
        },
        {
            path:'/nuevaventa',
            name:'nuevaventa',
            component : require('./views/generarventa').default
        },
        {
            path:'/VentaMayor',
            name:'VentaMayor',
            component : require('./views/nuevaVentaMayor').default
        },
        {
            path:'/creditos',
            name:'creditos',
            component : require('./views/pago_credito').default
        },
        {
            path:'/pdf',
            name:'pdf',
            component : require('./views/facturapdf').default
        },
        {
            path:'/grafico-ventas',
            name:'graficos-ventas',
            component : require('./views/graficos/venta').default
        },
        {
            path:'/grafico-ganancias',
            name:'graficos-ganancias',
            component : require('./views/graficos/ganancias').default
        },
        {
            path:'/grafico-clientes',
            name:'graficos-cliente',
            component : require('./views/graficos/cliente').default
        },
        {
            path:'/grafico-vendedor',
            name:'graficos-vendedor',
            component : require('./views/graficos/usuario').default
        },
        {
            path:'/grafico-proveedor',
            name:'graficos-proveedor',
            component : require('./views/graficos/proveedor').default
        }, 
        {
            path:'/grafico-Sinternas',
            name:'grafico-Sinternas',
            component : require('./views/graficos/Sinternas').default
        }, 
        {
            path:'/nueva_salida',
            name:'nueva_salida',
            component : require('./views/salida_product').default
        },
        {
            path:'/lista_salida',
            name:'lista_salida',
            component : require('./views/lista_salida').default
        },
        {
            path:'/proveedor',
            name:'proveedor',
            component : require('./views/proveedor').default
        },
        {
            path:'/compra',
            name:'nota_pedido',
            component : require('./views/notapedido').default
        },
        {
            path:'/listado_compras',
            name:'notas_pedidos',
            component : require('./views/notaspedidos').default
        },
        {
            path:'/movimientos',
            name:'movimientos',
            component : require('./views/movimientos').default,
        },
        {
            path:'/kardex=:slug?',
            name:'kardex',
            component : require('./views/kardex').default,
            props:true
        },
        {
            path:'*',
            component : require('./views/404').default
        },
    ],
    mode: 'history'

})
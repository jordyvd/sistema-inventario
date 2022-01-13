const { default: Axios } = require('axios');
let user_id = document.head.querySelector('meta[name="user_id"]');
let user_name = document.head.querySelector('meta[name="user_name"]');
let user_sucursal = document.head.querySelector('meta[name="user_sucursal"]');
let user_rol = document.head.querySelector('meta[name="user_rol"]');
let user_id_sucursal = document.head.querySelector('meta[name="user_id_sucursal"]');
// ******** PERMISOS ***********
let traslados = document.head.querySelector('meta[name="traslados"]');
let ventas = document.head.querySelector('meta[name="ventas"]');
let sucursal_ventas = document.head.querySelector('meta[name="sucursal_ventas"]');
let reportes = document.head.querySelector('meta[name="reportes"]');
let report_ingre = document.head.querySelector('meta[name="report_ingre"]');
let report_egre = document.head.querySelector('meta[name="report_egre"]');
let ganancias = document.head.querySelector('meta[name="ganancias"]');
let clientes = document.head.querySelector('meta[name="clientes"]');
let mantenimiento = document.head.querySelector('meta[name="mantenimiento"]');
let agregar_modif_mante = document.head.querySelector('meta[name="agregar_modif_mante"]');
let almacen = document.head.querySelector('meta[name="almacen"]');
let permisos = document.head.querySelector('meta[name="permisos"]');
let compras = document.head.querySelector('meta[name="compras"]');

module.exports = {
    data() {
        return {
            permiso: [],
        }
    },
    computed: {
        user_name() {
            return user_name.content;
        },
        user_sucursal() {
            return user_sucursal.content;
        },
        user_rol() {
            return user_rol.content;
        },
        product_name() {
            return "item.nompro";
        },
        user_id_sucursal(){
            return user_id_sucursal.content;
        },
        // ***** permisos *****
        user_traslado() {
            return traslados.content;
        },
        user_ventas() {
            return ventas.content;
        },
        sucursal_ventas() {
            return sucursal_ventas.content;
        },
        user_reportes() {
            return reportes.content;
        },
        user_report_ingre(){
            return report_ingre.content;
        },
        user_report_egre(){
            return report_egre.content;
        },
        user_ganancias(){
            return ganancias.content;
        },
        user_clientes() {
            return clientes.content;
        },
        user_mantenimiento() {
            return mantenimiento.content;
        },
        user_agregar_modif_mante(){
            return agregar_modif_mante.content;
        },
        user_almacen(){
            return almacen.content;
        },
        user_permisos() {
            return permisos.content;
        },
        user_compras(){
            return compras.content;
        },
        facturadorUrl(){
            return "http://181.224.250.87:88/api_cpe/ReceiveInformation.php";
        },
        facturadorFile(){
           // return "http://181.224.250.87:88/api_cpe/";
            return "http://localhost/FacturadorSunat/api_cpe/";
        },
    },
    methods:{
        no_permisos(){
            swal("", "no tienes permiso para esta acción", "info");
        },
    }
}
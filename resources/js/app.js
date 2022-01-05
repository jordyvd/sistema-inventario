require('./bootstrap');
window.Vue = require('vue');
import VueHtmlToPaper from "vue-html-to-paper";
import pdf from 'vue-pdf';
import moment from 'moment';

Vue.component('login_user', require('./components/LoginComponent.vue').default);
Vue.component('menu_sistema', require('./components/MenuComponent.vue').default);
Vue.component('bajos-stock', require('./components/BajosStock.vue').default);
Vue.component('grafico-ventas', require('./components/GraficoVentas.vue').default);
Vue.component('grafico-ganancia', require('./components/GraficoGanancia.vue').default);
Vue.component('grafico-clientes', require('./components/GraficoClientes.vue').default);
Vue.component('grafico-vendedor', require('./components/GraficoVendedores.vue').default);
Vue.component('grafico-proveedor', require('./components/GraficoProveedor.vue').default);
Vue.component('grafico-Sinternas', require('./components/GraficoSinternas.vue').default);
Vue.component('notpermiso', require('./components/NotpermisoComponent.vue').default);
Vue.component('spinner', require('./components/Spinner.vue').default);
// importar validacion de login 
import auth from './auth';
Vue.mixin(auth);
// function global
import global_f from './function_global';
Vue.mixin(global_f);
//PAGINACION
import pagination from './pagination';
Vue.mixin(pagination);
//sweetalert
import swal from 'sweetalert';
// TOAST
import VueToast from 'vue-toast-notification';
import 'vue-toast-notification/dist/theme-default.css';
Vue.use(VueToast);
// FIN TOAST
// routers
import router from './routes';
const app = new Vue({
    el: '#app',
    router
});
// IMPRIMIR
// ...
const options = {
    name: '_blank',
    specs: [
      'fullscreen=yes',
      'titlebar=yes',
      'scrollbars=yes'
    ],
    styles: [
      'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css',
      'https://unpkg.com/kidlat-css/css/kidlat.css'
    ]
  }
  
  Vue.use(VueHtmlToPaper, options);
  
  // or, using the defaults with no stylesheet
  Vue.use(VueHtmlToPaper);
  // ...

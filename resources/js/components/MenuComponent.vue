<template>
  <div>
    <div v-if="spinner">
      <div class="carga-principal bg-menu" id="bienvenida">
        <h1 class="bienvenida-text">Bienvenido(a) {{ user_name }}</h1>
      </div>
    </div>
    <spinner v-if="spinnerSystem" />
    <div class="wrapper d-flex align-items-stretch">
      <nav id="sidebar" class="menu-scroll bg-menu">
        <div class="card-header">
          <div class="card nav-header pointer" id="refrescar">
            <div class="row g-0">
              <div class="col-md-12">
                <img
                  src="images/logosinfondo.png"
                  class="mb-3 img-fluid logo"
                  alt="logo"
                />
              </div>
              <!-- <div class="col-md-8">
                <div class="card-body">
                  <p class="card-text text-white">Sistema de ventas</p>
                </div>
              </div> -->
            </div>
          </div>
        </div>
        <div class="p-4">
          <!-- <img
            class="mb-3 img-fluid logo pointer"
            id="refrescar"
            src="images/compra.png"
          />
          <p class="text-center text-white">
            {{ user_name }}({{ user_sucursal }})
          </p> -->

          <ul class="list-unstyled components mb-5">
            <li v-bind:class="classObject">
              <router-link :to="{ name: 'home' }">
                <i class="fa fa-home"></i> Home
              </router-link>
            </li>
            <li v-if="menu.traslados > 0">
              <a
                href="#homeSubmenu"
                data-toggle="collapse"
                aria-expanded="false"
                class="dropdown-toggle"
              >
                <i class="fas fa-people-carry"></i> Traslados
              </a>
              <ul class="collapse list-unstyled" id="homeSubmenu">
                <li>
                  <router-link :to="{ name: 'trasladar' }"
                    >generar traslado</router-link
                  >
                </li>
                <li>
                  <router-link :to="{ name: 'traslados' }"
                    >traslados</router-link
                  >
                </li>
                <li>
                  <router-link :to="{ name: 'ingresos' }">ingresos</router-link>
                </li>
              </ul>
            </li>
            <li v-if="menu.ventas > 0">
              <a
                href="#ventas"
                data-toggle="collapse"
                aria-expanded="false"
                class="dropdown-toggle"
              >
                <!-- <i class="fas fa-coins"></i>  -->
                <i class="fas fa-piggy-bank"></i> Ventas
              </a>
              <ul class="collapse list-unstyled" id="ventas">
                <li>
                  <router-link :to="{ name: 'nuevaventa' }" id="nuevaventa"
                    >nueva venta</router-link
                  >
                </li>
                <li>
                  <router-link :to="{ name: 'ventas' }"
                    >lista de ventas</router-link
                  >
                </li>
                <li>
                  <router-link :to="{ name: 'creditos' }">creditos</router-link>
                </li>
                <li>
                  <a
                    href="#subMenuVentas"
                    data-toggle="collapse"
                    aria-expanded="false"
                    class="dropdown-toggle"
                  >
                    <i class="fas fa-cash-register"></i> por mayor
                  </a>
                  <ul class="collapse list-unstyled" id="subMenuVentas">
                    <li>
                      <router-link :to="{ name: 'VentaMayor' }"
                        >nuevo</router-link
                      >
                    </li>
                    <li>
                      <router-link :to="{ name: 'listamayor' }"
                        >lista de ventas</router-link
                      >
                    </li>
                  </ul>
                </li>
              </ul>
            </li>
            <li></li>
            <li v-if="menu.ventas > 0">
              <a
                href="#documentos"
                data-toggle="collapse"
                aria-expanded="false"
                class="dropdown-toggle"
              >
                <i class="fas fa-file-pdf"></i> Documentos
              </a>
              <ul class="collapse list-unstyled" id="documentos">
                <li>
                  <router-link :to="{ name: 'documentos' }"
                    >listado</router-link
                  >
                </li>
              </ul>
            </li>
            <li></li>
            <li v-if="menu.reportes > 0">
              <a
                href="#pageSubmenuCon"
                data-toggle="collapse"
                aria-expanded="false"
                class="dropdown-toggle"
              >
                <i class="fas fa-chart-bar"></i> Contabilidad
              </a>
              <ul class="collapse list-unstyled" id="pageSubmenuCon">
                <li v-if="menu.reporte_ingres > 0">
                  <a
                    href="#subMenuG"
                    data-toggle="collapse"
                    aria-expanded="false"
                    class="dropdown-toggle"
                  >
                    <i class="fas fa-chart-line"></i> ingresos
                  </a>
                  <ul class="collapse list-unstyled" id="subMenuG">
                    <li>
                      <router-link :to="{ name: 'graficos-ventas' }"
                        >ventas</router-link
                      >
                    </li>
                    <li v-if="user_ganancias > 0">
                      <router-link :to="{ name: 'graficos-ganancias' }"
                        >ganancias</router-link
                      >
                    </li>
                    <li>
                      <router-link :to="{ name: 'graficos-cliente' }"
                        >clientes</router-link
                      >
                    </li>
                    <li>
                      <router-link :to="{ name: 'graficos-vendedor' }"
                        >vendedor</router-link
                      >
                    </li>
                  </ul>
                </li>
                <li v-if="menu.reporte_egreso > 0">
                  <a
                    href="#subMenuGE"
                    data-toggle="collapse"
                    aria-expanded="false"
                    class="dropdown-toggle"
                  >
                    <i class="fas fa-chart-area"></i> egresos
                  </a>
                  <ul class="collapse list-unstyled" id="subMenuGE">
                    <li>
                      <router-link :to="{ name: 'graficos-proveedor' }"
                        >pago proveedores</router-link
                      >
                    </li>
                    <li>
                      <router-link :to="{ name: 'grafico-Sinternas' }"
                        >Salidas internas</router-link
                      >
                    </li>
                  </ul>
                </li>
              </ul>
            </li>
            <li>
              <a
                href="#salida_products"
                data-toggle="collapse"
                aria-expanded="false"
                class="dropdown-toggle"
              >
                <i class="fas fa-backspace"></i> Salidas internas
              </a>
              <ul class="collapse list-unstyled" id="salida_products">
                <li>
                  <router-link :to="{ name: 'nueva_salida' }"
                    >nuevo</router-link
                  >
                </li>
                <li>
                  <router-link :to="{ name: 'lista_salida' }"
                    >listado</router-link
                  >
                </li>
              </ul>
            </li>
            <li>
              <router-link :to="{ name: 'caja' }">
                <i class="fas fa-coins"></i> Caja
              </router-link>
            </li>
            <li v-if="menu.clientes > 0">
              <router-link :to="{ name: 'clientes' }">
                <i class="fas fa-users"></i> Clientes
              </router-link>
            </li>
            <li v-if="menu.mantenimiento > 0">
              <a
                href="#Mantenimientonav"
                data-toggle="collapse"
                aria-expanded="false"
                class="dropdown-toggle"
              >
                <i class="fas fa-boxes"></i> Crear y modificar
              </a>
              <ul class="collapse list-unstyled" id="Mantenimientonav">
                <li>
                  <router-link :to="{ name: 'products' }" id="producto_crud"
                    >productos</router-link
                  >
                </li>
                <li>
                  <router-link :to="{ name: 'marcas' }">marcas</router-link>
                </li>
                <li>
                  <router-link :to="{ name: 'sucursal' }">sucursal</router-link>
                </li>
              </ul>
            </li>

            <li v-if="menu.compras > 0">
              <a
                href="#compras"
                data-toggle="collapse"
                aria-expanded="false"
                class="dropdown-toggle"
              >
                <i class="fas fa-shopping-bag"></i>
                Compras
              </a>
              <ul class="collapse list-unstyled" id="compras">
                <li>
                  <router-link
                    v-if="menu.compras > 0"
                    :to="{ name: 'proveedor' }"
                    >proveedor</router-link
                  >
                </li>
                <li>
                  <router-link
                    v-if="menu.compras > 0"
                    :to="{ name: 'nota_pedido' }"
                    >agregar compra</router-link
                  >
                </li>
                <li>
                  <router-link
                    v-if="menu.compras > 0"
                    :to="{ name: 'notas_pedidos' }"
                    >listado de compras</router-link
                  >
                </li>
              </ul>
            </li>
            <li v-if="menu.almacen > 0">
              <a
                href="#almacen"
                data-toggle="collapse"
                aria-expanded="false"
                class="dropdown-toggle"
              >
                <i class="fas fa-archive"></i> Almacén
              </a>
              <ul class="collapse list-unstyled" id="almacen">
                <li>
                  <router-link
                    id="inventario_click"
                    :to="{ name: 'almacen_products' }"
                    >inventario</router-link
                  >
                </li>
                <li>
                  <router-link :to="{ name: 'agregar_product' }"
                    >agregar producto nuevo</router-link
                  >
                </li>
                <li>
                  <router-link :to="{ name: 'movimientos' }"
                    >movimientos</router-link
                  >
                </li>
              </ul>
            </li>

            <li v-if="menu.permisos > 0">
              <a
                href="#permisosnav"
                data-toggle="collapse"
                aria-expanded="false"
                class="dropdown-toggle"
              >
                <i class="fas fa-hand-paper"></i> Permisos
              </a>
              <ul class="collapse list-unstyled" id="permisosnav">
                <li>
                  <router-link :to="{ name: 'roles' }">roles</router-link>
                </li>
                <li>
                  <router-link :to="{ name: 'permisos' }">permisos</router-link>
                </li>
                <li>
                  <router-link :to="{ name: 'usuarios' }">usuarios</router-link>
                </li>
              </ul>
            </li>
            <!-- <li>
              <a style="cursor: pointer" @click="backud"
                ><i class="fa fa-database"></i> Backud</a
              >
            </li> -->
            <li>
              <a @click="cerrar_sesion" style="cursor: pointer">
                <i class="fas fa-times"></i> Cerrar sesión
              </a>
            </li>
          </ul>
          <div class="footer"></div>
        </div>
      </nav>
      <!-- boton desplazamiento  -->
      <div id="content" class="p-4 p-md-2">
        <!-- <nav class="navbar" style="background: transparent; boxshadow: 0px">
          <div class="container-fluid"> -->
        <button
          type="button"
          id="sidebarCollapse"
          class="btn bg-menu"
          style="color: white"
        >
          <i class="fa fa-bars"></i>
          <span class="sr-only">Toggle Menu</span>
        </button>
        <!-- </div>
        </nav> -->
        <!-- LAS VISTAS CARGARAN AQUI -->
        <div class="scroll my-2">
          <transition name="slide-fade" mode="out-in">
            <router-view :key="$route.fullPath"></router-view>
          </transition>
        </div>
      </div>
    </div>
    <button id="clickButtonSpinner" hidden @click="spinnerShowHide()"></button>
  </div>
</template>
<script>
let user_id = document.head.querySelector('meta[name="user_id"]');
export default {
  data() {
    return {
      menu: [],
      spinner: false,
      isActive: true,
      spinnerSystem: false,
      documentos: [],
    };
  },
  computed: {
    classObject: function () {
      // return {
      //   active:this.isActive != this.isActive
      // }
      this.isActive = true;
    },
  },
  created() {
    this.spinner = true;
    axios
      .get("/api/menu_permisos/" + user_id.content)
      .then((res) => {
        this.menu = res.data;
        // this.spinner = false;
      })
      .catch((error) => {
        //location.reload();
      });
  },
  methods: {
    cerrar_sesion() {
      swal({
        text: "¿cerrar sesión?",
        icon: "info",
        buttons: ["no", "sí"],
      }).then((willDelete) => {
        if (willDelete) {
          if (this.user_sucursal != "huaral") {
            this.logout();
          } else {
            this.listarDocumentos();
          }
        }
      });
    },
    listarDocumentos() {
      document.getElementById("clickButtonSpinner").click();
      const params = {
        sucursal: this.user_sucursal,
      };
      axios.post("/api/listar-documentos", params).then((res) => {
        let array = res.data;
        let contador = 0;
        array.forEach((element) => {
          if (element.estado == null) {
            contador++;
          }
        });
        if (contador > 0) {
          swal({
            title: "HEY! "+this.user_name,
            text: "hay comprobantes pendientes",
            icon: "error",
            buttons: "aceptar",
            dangerMode: false,
            closeOnClickOutside: false,
            closeOnEsc: false,
            allowOutsideClick: false,
          }).then((willDelete) => {
            if (willDelete) {
             this.$router.push('documentos');
            }
          });
        } else {
          this.logout();
        }
        document.getElementById("clickButtonSpinner").click();
      });
    },
    logout() {
      axios.post("/cerrar").then((response) => {
        location.reload();
      });
    },
    backud() {
      swal("contraseña:", {
        content: "input",
      }).then((value) => {
        if (value != null) {
          axios.post("/api/backud", { password: value }).then((res) => {
            swal({
              text: res.data,
              icon: "info",
              button: "aceptar",
            });
          });
        }
      });
    },
    spinnerShowHide() {
      this.spinnerSystem = !this.spinnerSystem;
    },
  },
};
</script>
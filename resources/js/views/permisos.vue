<template>
  <div>
    <div class="acciones" v-if="user_permisos < 1">
      <notpermiso />
    </div>
    <div class="container centrar" v-else>
      <div class="formularios_sistema">
        <div class="form-group acciones">
          <select
            class="form-control select-permisos"
            @change="editar_view(permisos.rol)"
            v-model="permisos.rol"
          >
            <option
              :value="item.id"
              selected
              v-for="(item, index) in roles"
              :key="index"
            >
              {{ item.nomrol }}
            </option>
          </select>
        </div>
        <!-- **************** PERMISOS **************** -->
        <div class="centrar" v-if="spinner">
            cargando...
        </div>
        <ul class="list-group" v-else>
          <li
            class="list-group-item d-flex justify-content-between align-items-center"
          >
            <b>traslados</b>
            <input type="checkbox" value="1" v-model="permisos.traslados" />
          </li>
          <li
            class="list-group-item d-flex justify-content-between align-items-center"
          >
            <b>ventas</b>
            <input
              type="checkbox"
              value="1"
              @change="modalventas"
              v-model="permisos.ventas"
            />
          </li>
          <li
            class="list-group-item d-flex justify-content-between align-items-center"
          >
            <b>gráficos</b>
            <input type="checkbox" @change="modalGraficos" value="1" v-model="permisos.reportes" />
          </li>
          <li
            class="list-group-item d-flex justify-content-between align-items-center"
          >
            <b>clientes</b>
            <input type="checkbox" value="1" v-model="permisos.clientes" />
          </li>
          <li
            class="list-group-item d-flex justify-content-between align-items-center"
          >
            <b>crear y modificar</b>
            <input
              type="checkbox"
              @change="modalMan"
              value="1"
              v-model="permisos.mantenimiento"
            />
          </li>
          <li
            class="list-group-item d-flex justify-content-between align-items-center"
          >
            <b>almacen</b>
            <input type="checkbox" value="1" v-model="permisos.almacen" />
          </li>
          <li
            class="list-group-item d-flex justify-content-between align-items-center"
          >
            <b>permisos</b>
            <input type="checkbox" value="1" v-model="permisos.permiso" />
          </li>
          <li
            class="list-group-item d-flex justify-content-between align-items-center"
          >
            <b>compras</b>
            <input type="checkbox" value="1" v-model="permisos.compras" />
          </li>
          <br />
          <button class="btn btn-system centrar" @click="agregar">
            <b>Guardar</b>
          </button>
        </ul>
      </div>
    </div>
    <!-- ****************modal****************** -->
    <!-- Button trigger modal -->
    <button
      type="button"
      id="modalMantenimiento"
      data-toggle="modal"
      data-target="#staticBackdrop"
    ></button>
    <!-- Modal -->
    <div
      class="modal fade"
      id="staticBackdrop"
      data-keyboard="false"
      tabindex="-1"
      aria-labelledby="staticBackdropLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-body">
            <li
              class="list-group-item d-flex justify-content-between align-items-center"
            >
              <b>agregar y modificar registros</b>
              <input
                type="checkbox"
                @change="modalMan"
                value="1"
                v-model="permisos.agregar_modif_mante"
              />
            </li>
          </div>
        </div>
      </div>
    </div>
    <!-- modal ventas -->
    <button
      type="button"
      id="modalventas_click"
      data-toggle="modal"
      data-target="#modalventasver"
    ></button>
    <div
      class="modal fade"
      id="modalventasver"
      data-keyboard="false"
      tabindex="-1"
      aria-labelledby="staticBackdropLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-body">
            <li
              class="list-group-item d-flex justify-content-between align-items-center"
            >
              <b>permitir seleccionar sucursales</b>
              <input
                type="checkbox"
                @change="modalventas"
                value="1"
                v-model="permisos.sucursal_ventas"
              />
            </li>
          </div>
        </div>
      </div>
    </div>
    <!-- modal graficos -->
    <button
      type="button"
      id="modalgraficos_click"
      data-toggle="modal"
      data-target="#modalgraficosv"
    ></button>
    <div
      class="modal fade"
      id="modalgraficosv"
      data-keyboard="false"
      tabindex="-1"
      aria-labelledby="staticBackdropLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-body">
            <li
              class="list-group-item d-flex justify-content-between align-items-center"
            >
              <b>Ingresos</b>
              <input
                type="checkbox"
                value="1"
                v-model="permisos.report_ingre"
              />
            </li>
            <li
              class="list-group-item d-flex justify-content-between align-items-center"
            >
              <b>Egresos</b>
              <input
                type="checkbox"
                value="1"
                v-model="permisos.report_egre"
              />
            </li>
            <li
              class="list-group-item d-flex justify-content-between align-items-center"
            >
              <b>gráfico ganancias</b>
              <input type="checkbox" value="1" v-model="permisos.ganancias" />
            </li>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  data() {
    return {
      permisos: {
        rol: "",
        traslados: false,
        ventas: false,
        sucursal_ventas: false,
        reportes: false,
        report_ingre: false,
        report_egre:false,
        ganancias: false,
        clientes: false,
        mantenimiento: false,
        almacen: false,
        permiso: false,
        agregar_modif_mante: false,
        compras: false,
      },
      roles: [],
      permisos_view: [],
      spinner: false,
    };
  },
  created() {
    axios.get("/api/listrol").then((res) => {
      this.roles = res.data.roles.data;
    });
  },
  methods: {
    modalMan() {
      if (this.permisos.mantenimiento != false) {
        document.getElementById("modalMantenimiento").click();
      }
    },
    modalventas() {
      if (this.permisos.ventas != false) {
        document.getElementById("modalventas_click").click();
      }
    },
    modalGraficos(){
      if (this.permisos.reportes != false) {
        document.getElementById("modalgraficos_click").click();
      }
    },
    agregar() {
      this.spinner = true;
      if (this.permisos.rol === "") {
        Vue.$toast.error("selecionar rol");
        this.spinner = false;
      } else {
        const params = {
          rol: this.permisos.rol,
          traslados: this.permisos.traslados,
          ventas: this.permisos.ventas,
          sucursal_ventas: this.permisos.sucursal_ventas,
          reportes: this.permisos.reportes,
          report_ingre:this.permisos.report_ingre,
          report_egre:this.permisos.report_egre,
          ganancias: this.permisos.ganancias,
          clientes: this.permisos.clientes,
          mantenimiento: this.permisos.mantenimiento,
          agregar_modif_mante: this.permisos.agregar_modif_mante,
          almacen: this.permisos.almacen,
          permiso: this.permisos.permiso,
          compras: this.permisos.compras,
        };
        axios.get("/api/existerol/" + this.permisos.rol).then((res) => {
          if (res.data == "si") {
            axios
              .post("/editarpermisos/" + this.permisos.rol, params)
              .then((res) => {
                Vue.$toast.success("Guardado");
                this.spinner = false;
              });
          } else {
            axios.post("/asignarpermisos", params).then((res) => {
              this.spinner = false;
              Vue.$toast.success("Guardado");
            });
          }
        });
      }
    },
    editar_view(item) {
      this.spinner = true;
      axios.get("/api/view_permisos/" + item).then((res) => {
        this.permisos_view = res.data;
        if (this.permisos_view.traslados > 0) {
          this.permisos.traslados = true;
        } else {
          this.permisos.traslados = false;
        }
        // **********
        if (this.permisos_view.reportes > 0) {
          this.permisos.reportes = true;
        } else {
          this.permisos.reportes = false;
        }
         // **********
        if (this.permisos_view.reporte_ingres > 0) {
          this.permisos.report_ingre = true;
        } else {
          this.permisos.report_ingre = false;
        }
         // **********
        if (this.permisos_view.reporte_egreso > 0) {
          this.permisos.report_egre = true;
        } else {
          this.permisos.report_egre = false;
        }
        // **********
        if (this.permisos_view.ganancias > 0) {
          this.permisos.ganancias = true;
        } else {
          this.permisos.ganancias = false;
        }
        // **********
        if (this.permisos_view.mantenimiento > 0) {
          this.permisos.mantenimiento = true;
        } else {
          this.permisos.mantenimiento = false;
        }
        // **********
        if (this.permisos_view.agregar_modif_mante > 0) {
          this.permisos.agregar_modif_mante = true;
        } else {
          this.permisos.agregar_modif_mante = false;
        }
        // **********
        if (this.permisos_view.almacen > 0) {
          this.permisos.almacen = true;
        } else {
          this.permisos.almacen = false;
        }
        // **********
        if (this.permisos_view.ventas > 0) {
          this.permisos.ventas = true;
        } else {
          this.permisos.ventas = false;
        }
        // **********
        if (this.permisos_view.sucursal_ventas > 0) {
          this.permisos.sucursal_ventas = true;
        } else {
          this.permisos.sucursal_ventas = false;
        }
        // **********
        if (this.permisos_view.clientes > 0) {
          this.permisos.clientes = true;
        } else {
          this.permisos.clientes = false;
        }
        // **********
        if (this.permisos_view.permisos > 0) {
          this.permisos.permiso = true;
        } else {
          this.permisos.permiso = false;
        }
        if (this.permisos_view.compras > 0) {
          this.permisos.compras = true;
        } else {
          this.permisos.compras = false;
        }
        // **********
        this.spinner = false;
      });
    },
  },
};
</script>
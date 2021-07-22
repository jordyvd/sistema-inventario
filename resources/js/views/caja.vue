<template>
  <div class="row">
    <div class="container">
      <div class="card-header">
        <button class="button" data-toggle="modal" data-target="#exampleModal">
          <i class="fas fa-piggy-bank"></i> agregar
        </button>
        <input
          type="date"
          @change="listar"
          id="input-date"
          class="input-search"
          v-model="fecha"
          min="2020-01-01"
        />
        <button class="button" @click="refrescar">
          <i class="fas fa-redo-alt"></i> refrescar
        </button>
        <input
          type="search"
          class="input-search"
          v-model="descripsea"
          placeholder="Descripción"
        />
        <button class="button" @click="buscar_descr">
          <i class="fas fa-search"></i> Buscar
        </button>
        <select
          class="input-search"
          v-model="seleccion_sucursal"
          style="padding: 12.5px 0px"
          v-if="sucursal_ventas > 0"
        >
          <option value="seleccionar...">seleccionar...</option>
          <option
            v-for="(item, index) in sucursal"
            :value="item.nombre"
            :key="index"
          >
            {{ item.nombre }}
          </option>
        </select>
        <a
          class="text-primary pointer"
          @click.prevent="masinfo()"
          data-toggle="modal"
          data-target="#modalIngresos"
          >...más</a
        >
      </div>
      <div v-if="spinner" class="centrar">
        <p>cargando...</p>
      </div>
      <div class="table-scroll" v-else>
        <div class="alert centrar" v-if="ingresos_salidas.length < 1">
          no hay movimiento
        </div>
        <table class="table" v-else>
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">descripcion</th>
              <th scope="col">monto</th>
              <th scope="col">condición</th>
              <th scope="col">fecha</th>
              <th scope="col">eliminar</th>
            </tr>
          </thead>
          <tbody v-for="(item, index) in ingresos_salidas" :key="index">
            <tr>
              <th scope="row">{{ index + 1 }}</th>
              <td>{{ item.descripcion }}</td>
              <td>{{ parseFloat(item.monto).toFixed(2) }}</td>
              <td>{{ item.condicion }}</td>
              <td>{{ item.fecha }}</td>
              <td>
                <button class="text-dark" @click="eliminar(item, index)">
                  <i class="far fa-trash-alt"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <!-- ******* PAGINACION ********* -->
      <nav>
        <ul class="pagination">
          <li v-if="pagination.current_page > 1" class="page-item">
            <a
              href="#"
              @click.prevent="changePage(pagination.current_page - 1)"
            >
              <span>
                <b>Atras</b>
              </span>
            </a>
          </li>
          <li
            class="page-item"
            v-for="(page, index) in pagesNumber"
            v-bind:class="[page == isActived ? 'active_pagination' : '']"
            :key="index"
          >
            <a href @click.prevent="changePage(page)">
              <b>{{ page }}</b>
            </a>
          </li>
          <li
            class="page-item"
            v-if="pagination.current_page < pagination.last_page"
          >
            <a
              href="#"
              @click.prevent="changePage(pagination.current_page + 1)"
            >
              <span>
                <b>Siguiente</b>
              </span>
            </a>
          </li>
        </ul>
      </nav>
      <!-- MODAL AGREGAR  -->
      <div
        class="modal fade"
        id="exampleModal"
        tabindex="-1"
        aria-labelledby="exampleModalLabel"
        aria-hidden="true"
      >
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">
                <i class="fas fa-piggy-bank"></i> agregar ingreso / salida
              </h5>
              <button
                type="button"
                class="close"
                data-dismiss="modal"
                aria-label="Close"
              >
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <!-- FORMULARIO -->
              <form @submit.prevent="agregar">
                <div class="form-group">
                  <label>descripcion</label>
                  <input
                    type="text"
                    v-model="datos.descripcion"
                    class="form-control"
                  />
                </div>
                <div class="form-group">
                  <label>monto</label>
                  <input
                    type="text"
                    class="form-control solonumero"
                    v-model="datos.monto"
                  />
                </div>
                <div class="form-group">
                  <label>condición</label>
                  <select class="form-control" v-model="condicion">
                    <option value="" disabled>selecionar...</option>
                    <option value="ingreso">ingreso</option>
                    <option value="salida">salida</option>
                  </select>
                </div>
                <button type="submit" class="btn bg-system" v-if="boton">
                  <i class="fa fa-save"></i> Guardar
                </button>
              </form>
              <!-- FIN FORMULARIO -->
            </div>
          </div>
        </div>
      </div>
      <!-- FIN MODAL -->
    </div>
    <!-- Modal ingresos -->
    <div
      class="modal fade"
      id="modalIngresos"
      tabindex="-1"
      role="dialog"
      aria-labelledby="exampleModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog" role="document">
        <div class="modal-content border-modal">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">
              movimientos económicos
            </h5>
            <button
              type="button"
              class="close"
              data-dismiss="modal"
              aria-label="Close"
            >
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <p v-if="cargando" class="text-center">cargando...</p>
            <ul class="list-group" v-else>
              <li class="list-group-item">
                ingresos a caja:
                <span class="badge badge-xl bg-menu text-white"
                  >S/. {{ parseFloat(monto_ingresa.total_ingresos).toFixed(2) }}</span
                >
              </li>
              <li class="list-group-item">
                salidas de caja:
                <span class="badge badge-xl bg-menu text-white"
                  >S/. {{ parseFloat(monto_salida.total_salidas).toFixed(2) }}</span
                >
              </li>
              <li class="list-group-item">
                ventas en efectivo:
                <span class="badge badge-xl bg-menu text-white"
                  >S/. {{ parseFloat(ventas_double).toFixed(2) }}</span
                >
              </li>
              <!-- ****************** PAGO BANCARIO ********************* -->
              <li class="list-group-item" v-if="caja_creditos_api.total_ventas < 1">
                ventas - pagos bancarios:
                <span class="badge badge-xl bg-menu text-white"
                  >S/. 0.00</span
                >
              </li>
              <li class="list-group-item" v-else>
                ventas - pagos bancarios:
                <span class="badge badge-xl bg-menu text-white"
                  >S/. {{ parseFloat(caja_creditos_api.total_ventas).toFixed(2) }}</span
                >
              </li>
              <!-- ****************** PAGO BANCARIO ********************* -->
              <li class="list-group-item">
                total general:
                <span class="badge badge-xl bg-menu text-white"
                  >S/.
                  {{
                    total_general.toFixed(2)
                  }}</span
                >
              </li>
            </ul>
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
      ingresos_salidas: [],
      boton: true,
      condicion: "",
      datos: {
        descripcion: "",
        monto: "",
      },
      spinner: false,
      fecha: "1",
      monto_ingresa: [],
      monto_salida: [],
      calculando: true,
      calculo_caja: [],
      cargando: false,
      ventas_double: 0,
      descripsea: "",
      seleccion_sucursal: "seleccionar...",
      sucursal: [],
      caja_creditos_api: 0,
    };
  },
  created() {
    this.seleccion_sucursal = this.user_sucursal;
    this.listar();
    axios.get("/api/listsucursal").then((res) => {
      this.sucursal = res.data.sucursal.data;
    });
  },
  methods: {
    agregar() {
      if (
        this.datos.descripcion.trim() === "" ||
        this.datos.monto == "" ||
        this.condicion.trim() === ""
      ) {
        Vue.$toast.error("completar todos los campos");
      } else {
        this.boton = false;
        const params = {
          descripcion: this.datos.descripcion,
          monto: this.datos.monto,
          condicion: this.condicion,
          sucursal: this.seleccion_sucursal,
        };
        axios.post("/ingresos_salidas_create", params).then((res) => {
          this.listar();
          this.limpiar_form();
          this.boton = true;
        });
      }
    },
    listar(page) {
      this.spinner = true;
      this.cargando = true;
      let url =
        "/api/listaringresos_salidas/" +
        this.seleccion_sucursal +
        "/" +
        this.fecha +
        "?page=" +
        page;
      axios.get(url).then((res) => {
        this.ingresos_salidas = res.data.datos.data;
        this.pagination = res.data.paginate;
        this.spinner = false;
      });
    },
    masinfo() {
      this.total_ingresos();
      this.total_salidas();
      this.total_caja();
      this.transferencia();
    },
    total_caja() {
      let url = "/api/total_caja/" + this.seleccion_sucursal + "/" + this.fecha;
      axios.get(url).then((res) => {
        this.calculo_caja = res.data;
        if (this.calculo_caja.total_venta === null) {
          this.ventas_double = 0;
        } else {
          this.ventas_double = this.calculo_caja.total_venta;
        }
        this.cargando = false;
      });
    },
    transferencia() {
      let url =
        "/api/caja_trasnferencia/" + this.seleccion_sucursal + "/" + this.fecha;
      axios.get(url).then((res) => {
        if (res.data != null) {
          this.caja_creditos_api = res.data;
        }
      });
    },
    eliminar(item, index) {
      swal({
        text: "¿estás seguro?",
        icon: "error",
        buttons: ["no", "sí"],
        dangerMode: true,
      }).then((willDelete) => {
        if (willDelete) {
          axios.get(`/deleteingreso_salida/${item.id}`).then((res) => {
            Vue.$toast.success("eliminado");
            this.listar();
          });
        }
      });
    },
    refrescar() {
      this.$data.fecha = "1";
      this.$data.descripsea = "";
      this.listar();
      this.total_ingresos();
      this.total_salidas();
    },
    limpiar_form() {
      this.$data.datos.descripcion = "";
      this.$data.datos.monto = "";
      this.$data.condicion = "";
    },
    total_ingresos() {
      let url =
        "/api/montoingresa/" + this.seleccion_sucursal + "/" + this.fecha;
      axios.get(url).then((res) => {
        if (res.data.total_ingresos === null) {
          this.monto_ingresa.total_ingresos = 0;
        } else {
          this.monto_ingresa = res.data;
        }
      });
    },
    total_salidas() {
      this.calculando = true;
      let url2 =
        "/api/montosalidas/" + this.seleccion_sucursal + "/" + this.fecha;
      axios.get(url2).then((res) => {
        // this.monto_salida = res.data;
        if (res.data.total_salidas === null) {
          this.monto_salida.total_salidas = 0;
        } else {
          this.monto_salida = res.data;
        }
        this.calculando = false;
      });
    },
    buscar_descr() {
      if (this.descripsea.trim() === "") {
        Vue.$toast.error("campo vacío");
      } else {
        this.spinner = true;
        axios
          .get(
            "/api/descripsea/" + this.descripsea + "/" + this.seleccion_sucursal+"/"+this.fecha
          )
          .then((res) => {
            this.ingresos_salidas = res.data.datos.data;
            this.pagination = res.data.paginate;
            this.spinner = false;
          });
      }
    },
    changePage(page) {
      this.pagination.current_page = page;
      this.listar(page);
    },
  },
  computed: {
    total_efectivo_fecha() {
      return (
        parseFloat(this.monto_salida.total_salidas) -
        parseFloat(this.monto_ingresa.total_ingresos)
      );
    },
    total_general() {
      return (
        parseFloat(this.monto_ingresa.total_ingresos) +
        parseFloat(this.ventas_double) -
        parseFloat(this.monto_salida.total_salidas)
      );
    },
    //  monto_ingresa.total_ingresos +
    //                 ventas_double -
    //                 monto_salida.total_salidas
  },
};
</script>
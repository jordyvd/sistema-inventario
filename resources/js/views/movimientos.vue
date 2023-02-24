<template>
  <div class="row">
    <div class="container">
      <div class="card-header">
        <input type="date" class="input-search" v-model="fechas.desde" />
        <input type="date" class="input-search" v-model="fechas.hasta" />
        <select
          class="input-search"
          style="padding: 14px 2px"
          v-model="condicion"
        >
          <option value="" disabled>Seleccionar..</option>
          <option value="venta">ventas</option>
          <option value="traslado">traslados</option>
          <option value="compra">compras</option>
          <option value="salida_interna">salidas internas</option>
        </select>

        <!-- <select
          class="input-search"
          style="padding: 14px 2px"
          v-model="cantidad_mov"
        >
          <option value="todo">todos</option>
          <option :value="item.num" v-for="(item,index) in cantidades" :key="index">{{item.num}}</option>
        </select> -->
        <select
          v-if="sucursal_ventas > 0"
          class="input-search"
          style="padding: 14px 2px"
          v-model="sucursal_item"
        >
          <option value="" disabled>Seleccionar..</option>
          <option
            :value="item.nombre"
            v-for="(item, index) in sucursal"
            :key="index"
          >
            {{ item.nombre }}
          </option>
        </select>
        <button class="button" @click="listar(dataPaginacion.currentPage)">
          <i class="fas fa-search"></i> Buscar
        </button>
        <button class="button" @click="exportarExcel">
          <i class="far fa-file-excel"></i> Exportar
        </button>
      </div>
      <div class="centrar" v-if="spinner">cargando...</div>
      <div class="table-scroll" v-else>
        <table class="table">
          <thead>
            <tr>
              <th scope="col">nro documento</th>
              <th scope="col">producto / descripción</th>
              <th scope="col">marca</th>
              <th scope="col">precio</th>
              <th scope="col">cantidad</th>
              <th scope="col">descuento</th>
              <!-- <th scope="col">condición</th> -->
              <th scope="col">condición</th>
              <th scope="col">fecha</th>
              <th scope="col">sucursal</th>
              <th scope="col" v-if="[1, 29, 21].includes(parseInt(user_id))">eliminar</th>
            </tr>
          </thead>
          <tbody v-for="(item, index) in movimiento" :key="index">
            <tr>
              <td>{{ item.nro_documento }}</td>
              <td>{{ item.nompro }}</td>
              <td>{{ item.marca }}</td>
              <td>{{ parseFloat(item.precio).toFixed(2) }}</td>
              <td>{{ item.cantidad }}</td>
              <td v-if="item.descuento != null">
                {{ parseFloat(item.descuento).toFixed(2) }}
              </td>
              <td v-else>--</td>
              <td v-if="item.condicion == 'salida'">--</td>
              <td v-else>{{ item.condicion }}</td>
              <td>{{ item.fecha }}</td>
              <td>{{ item.sucursal }}</td>
              <td v-if="[1, 29, 21].includes(parseInt(user_id))">
                <i
                  class="fas fa-trash cursor"
                  @click="deleteMovimiento(item.id)"
                ></i>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <nav
        aria-label="Page navigation example"
        v-if="dataPaginacion.cantidad > 0"
      >
        <ul class="pagination">
          <li class="page-item" :disabled="dataPaginacion.currentPage == 1">
            <a
              class="page-link"
              :class="dataPaginacion.currentPage == 1 ? '' : 'cursor'"
              @click="
                dataPaginacion.currentPage == 1
                  ? ''
                  : listar(dataPaginacion.currentPage - 1)
              "
              aria-label="Previous"
            >
              <span aria-hidden="true">&laquo;</span>
            </a>
          </li>
          <li
            v-for="(item, index) in dataPaginacion.cantidad"
            :key="index"
            class="page-item"
            :class="index + 1 == dataPaginacion.currentPage ? 'active' : ''"
          >
            <a
              class="page-link cursor"
              @click="listar(index + 1)"
              v-if="paginador(index + 1)"
            >
              {{ index + 1 }}
            </a>
          </li>
          <li class="page-item">
            <a
              class="page-link"
              :class="
                dataPaginacion.currentPage == dataPaginacion.cantidad
                  ? ''
                  : 'cursor'
              "
              @click="
                dataPaginacion.currentPage == dataPaginacion.cantidad
                  ? ''
                  : listar(dataPaginacion.currentPage + 1)
              "
              aria-label="Next"
            >
              <span aria-hidden="true">&raquo;</span>
            </a>
          </li>
        </ul>
      </nav>
    </div>
  </div>
</template>
<script>
import XLSX from "xlsx";
export default {
  data() {
    return {
      spinner: false,
      movimiento: [],
      fechas: {
        desde: null,
        hasta: null,
      },
      dataPaginacion: {
        perpage: 10,
        cantidad: 0,
        limitPagina: 4,
        currentPage: 1,
      },
      condicion: "venta",
      sucursal: [],
      sucursal_item: "",
      cantidades: [],
      cantidad_mov: "todo",
      exportar: [],
    };
  },
  created() {
    axios.get("/api/listsucursal").then((res) => {
      this.sucursal = res.data.sucursal.data;
      this.sucursal_item = this.user_sucursal;
      this.listar(1);
    });
  },
  methods: {
    listar(page) {
      this.clickSpinner();
      const params = {
        desde: this.fechas.desde,
        hasta: this.fechas.hasta,
        tipo: this.condicion,
        sucursal: this.sucursal_item,
        cantidad: this.cantidad_mov,
        page: page,
      };
      let url = "/api/listado_movimiento";
      axios.post(url, params).then((res) => {
        this.movimiento = res.data.data;
        this.dataPaginacion.cantidad = res.data.paginas;
        this.dataPaginacion.currentPage = page;
        this.dataPaginacion.perpage = res.data.perpage;
        console.log(this.dataPaginacion);
        this.clickSpinner();
      });
    },
    exportarExcel() {
      if (this.movimiento.length < 1) {
        Vue.$toast.error("no hay registros");
      } else {
        let url =
          "/api/exportar_movimiento/" +
          this.fechas.desde +
          "/" +
          this.fechas.hasta +
          "/" +
          this.condicion +
          "/" +
          this.sucursal_item;
        axios.get(url).then((res) => {
          console.log(res.data);
          let data = XLSX.utils.json_to_sheet(res.data);
          const workbook = XLSX.utils.book_new();
          const filename = this.sucursal_item + "-" + this.condicion;
          XLSX.utils.book_append_sheet(workbook, data, filename);
          XLSX.writeFile(workbook, `${filename}.xlsx`);
        });
      }
    },
    paginador(page) {
      if (
        page >= this.dataPaginacion.currentPage &&
        page <=
          this.dataPaginacion.limitPagina + this.dataPaginacion.currentPage
      ) {
        return true;
      } else {
        return false;
      }
    },
    deleteMovimiento(id) {
      swal({
        text: "¿estas seguro?",
        icon: "error",
        buttons: ["no", "si"],
        dangerMode: true,
      }).then((willDelete) => {
        if (willDelete) {
          this.clickSpinner();
          axios.delete("/api/delete-movimiento/" + id).then((res) => {
            this.clickSpinner();
            this.listar(this.dataPaginacion.currentPage);
          });
        }
      });
    },
  },
};
</script>
<style>
.cursor {
  cursor: pointer;
}
.active-pag {
  background: #ad1103 !important;
  color: white !important;
}
</style>
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
          <option value="vacio" disabled>Seleccionar...</option>
          <option value="venta">ventas</option>
          <option value="traslado">traslados</option>
          <option value="compra">compras</option>
          <option value="salida_interna">salidas internas</option>
        </select>
        <select
          v-if="sucursal_ventas > 0"
          class="input-search"
          style="padding: 14px 2px"
          v-model="sucursal_item"
        >
          <option value="" disabled>Seleccionar...</option>
          <option
            :value="item.nombre"
            v-for="(item, index) in sucursal"
            :key="index"
          >
            {{ item.nombre }}
          </option>
        </select>
        <button class="button" @click="buscar">
          Buscar <i class="fas fa-search"></i>
        </button>
      </div>
      <div class="centrar" v-if="spinner">cargando...</div>
      <div class="table-scroll" v-else>
        <table class="table">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">nro</th>
              <th scope="col">producto / descripción</th>
              <th scope="col">marca</th>
              <th scope="col">precio</th>
              <th scope="col">cantidad</th>
              <th scope="col">condición</th>
              <th scope="col">fecha</th>
              <th scope="col">detalle</th>
              <th scope="col">sucursal</th>
            </tr>
          </thead>
          <tbody v-for="(item, index) in movimiento" :key="index">
            <tr>
              <th scope="row">{{ index + 1 }}</th>
              <td>{{ item.nro_documento }}</td>
              <td>{{ item.nompro }}</td>
              <td>{{ item.marca }}</td>
              <td>{{ parseFloat(item.precio).toFixed(2) }}</td>
              <td>{{ item.cantidad }}</td>
              <td>{{ item.condicion }}</td>
              <td>{{ item.fecha }}</td>
              <td>{{ item.detalle }}</td>
              <td>{{ item.sucursal }}</td>
            </tr>
          </tbody>
        </table>
      </div>
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
    </div>
  </div>
</template>
<script>
export default {
  props: ["slug"],
  data() {
    return {
      spinner: true,
      movimiento: [],
      fechas: {
        desde: "00",
        hasta: "00",
      },
      condicion: "vacio",
      sucursal: [],
      sucursal_item: "",
    };
  },
  created() {
    axios.get("/api/listsucursal").then((res) => {
      this.sucursal = res.data.sucursal.data;
      this.sucursal_item = this.user_sucursal;
      this.listar();
    });
  },
  methods: {
    listar(page) {
      this.spinner = true;
      let url =
        "/api/list_kardex/" +
        this.fechas.desde +
        "/" +
        this.fechas.hasta +
        "/" +
        this.condicion +
        "/" +
        this.sucursal_item +
        "/" +
        this.slug +
        "?page=" +
        page;
      axios.get(url).then((res) => {
        this.movimiento = res.data.movimiento.data;
        this.pagination = res.data.paginate;
        this.spinner = false;
      });
    },
    buscar() {
      if (this.fechas.desde !== "00" || this.fechas.hasta !== "00") {
        if (this.fechas.desde === "00" || this.fechas.hasta === "00") {
          Vue.$toast.error("seleccionar ambas fechas");
        } else {
          this.listar();
        }
      } else {
        this.listar();
      }
    },
    changePage(page) {
      this.pagination.current_page = page;
      this.listar(page);
    },
  },
};
</script>
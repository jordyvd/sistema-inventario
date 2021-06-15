<template>
  <div>
    <div>
      <!-- SOURCE -->

      <button @click="print">pe</button>
      <div id="printMe" style="display: none">
        <div class="container">
          <div class="row">
            <div class="col-sm">
              <p>pedo</p>
              <p>pedo</p>
              <p>pedo</p>
              <p>pedo</p>
            </div>
            <div class="col-sm">
              <b style="font-size: 31px">GIOMAR SPORT</b>
              <p>Direccion legal: #########</p>
              <p>telefono: #########</p>
              <p>sucursal: #########</p>
            </div>
            <div class="col-sm border border-dark rounded" style="left: 14%">
              <br />
              <p
                style="
                  display: flex;
                  align-items: center;
                  justify-content: center;
                "
              >
                R.U.C. 201133998
              </p>
              <p
                style="
                  display: flex;
                  align-items: center;
                  justify-content: center;
                "
              >
                FACTURA DE VENTA
              </p>
              <p
                style="
                  display: flex;
                  align-items: center;
                  justify-content: center;
                "
              >
                N° 00119998
              </p>
              <p
                style="
                  display: flex;
                  align-items: center;
                  justify-content: center;
                "
              >
                SUCURSAL: LIMA
              </p>
            </div>
          </div>
        </div>
        <div
          class="rounded border border-dark"
          style="margin: 20px 0 !important; height: 1000px"
        >
          <div
            class="row"
            style="padding: 10px 20px; border-bottom: 1px solid black"
          >
            <div class="col-6">
              <p>CLIENTE: JUANITA</p>
              <p>RUC: 10002588</p>
              <p>DIRECCION: ##################</p>
            </div>
            <div class="col-6" style="left: 22% !important">
              <p>FECHA: 20-12-2020</p>
              <p>ESTADO: CONTADO</p>
              <p>VENDEDOR: ANONIMO</p>
            </div>
          </div>
          <div
            class="row"
            style="padding: 10px 20px; border-bottom: 1px solid black"
          >
            <div class="col-6">
              <p>CLIENTE: JUANITA</p>
              <p>RUC: 10002588</p>
              <p>DIRECCION: ##################</p>
            </div>
            <div class="col-6" style="left: 22% !important">
              <p>FECHA: 20-12-2020</p>
              <p>ESTADO: CONTADO</p>
              <p>VENDEDOR: ANONIMO</p>
            </div>
          </div>
          <div class="row">
            <div class="col-12" style="padding: 10px 50px !important">
              <table class="table">
                <thead>
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">producto</th>
                    <th scope="col">precio</th>
                    <th scope="col">cantidad</th>
                    <th scope="col">descuento</th>
                    <th scope="col">importe</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <th scope="row">1</th>
                    <td>ivermectina</td>
                    <td>30</td>
                    <td>2</td>
                    <td>2.50</td>
                    <td>65</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="container">
          <div class="row">
            <div class="col-sm">One of three colum m ns</div>
            <div class="col-sm">One of three columns</div>
            <div class="col-sm" style="left:15%">
              <ul class="list-group">
                <li class="list-group-item" style="border:1px solid black">Subtotal: S/.300</li>
                <li class="list-group-item" style="border:1px solid black">Descuento: S/.15</li>
                <li class="list-group-item" style="border:1px solid black">Total: S/.285</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
      <!-- OUTPUT -->
    </div>
  </div>
</template>
<script>
export default {
  data() {
    return {};
  },
  methods: {
    print() {
      // Pass the element id here
      this.$htmlToPaper("printMe");
    },
  },
};
</script>
<style>
.content {
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  margin: auto !important;
}
</style>
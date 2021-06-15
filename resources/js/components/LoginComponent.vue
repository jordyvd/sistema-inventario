<template>
  <div>
    <div class="limiter">
      <div class="spinner-border text-danger" v-if="spinner" role="status">
        <span class="sr-only">Loading...</span>
      </div>
      <div class="container-login100">
        <div class="wrap-login100">
          <div class="login100-pic js-tilt" data-tilt>
            <img src="images/img-01.png" alt="IMG" />
          </div>
          <form
            class="login100-form validate-form"
            @submit.prevent="ingresar()"
          >
            <span class="login100-form-title" style="color: white"
              >Iniciar sesión</span
            >
            <div class="wrap-input100 validate-input">
              <input
                class="input100"
                type="text"
                v-model="user.dni"
                placeholder="DNI"
              />
              <span class="focus-input100"></span>
              <span class="symbol-input100">
                <i class="fa fa-user" aria-hidden="true"></i>
              </span>
            </div>

            <div class="wrap-input100 validate-input">
              <input
                class="input100"
                type="password"
                v-model="user.password"
                placeholder="Contraseña"
              />
              <span class="focus-input100"></span>
              <span class="symbol-input100">
                <i class="fa fa-lock" aria-hidden="true"></i>
              </span>
            </div>
            <div class="container-login100-form-btn">
              <button type="submit" class="login100-form-btn btn btn-success">
                Ingresar
              </button>
            </div>
            <div class="text-center p-t-136">
              <!-- SISTEMA DE ADMINISTRACION -->
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  data() {
    return {
      user: { dni: "", password: "", sucursal: "seleccion" },
      spinner: false,
      caja_local: [],
      sucursal: [],
    };
  },
  created() {
    axios.get("/api/listsucursal").then((res) => {
      this.sucursal = res.data.sucursal.data;
    });
  },
  methods: {
    efectivo_caja() {
      swal("efectivo en caja:", {
        content: "input",
        buttons: "aceptar",
      }).then((value) => {
        swal({
          text: "¿estás seguro?",
          icon: "error",
          buttons: ["no", "si"],
          dangerMode: true,
        }).then((willDelete) => {
          if (willDelete) {
            if (value === "") {
              this.cerrar_sesion();
            } else {
              if (value < 1) {
                alert("valor 0");
                location.reload();
              } else {
                const params = {
                  cantidad: value,
                  sucursal: this.user.sucursal,
                };
                axios
                  .post("/ingresarcaja", params)
                  .then((res) => {
                    location.reload();
                  })
                  .catch((error) => {
                    this.cerrar_sesion();
                  });
              }
            }
          } else {
            this.cerrar_sesion();
          }
        });
      });
    },
    ingresar() {
      this.spinner = true;
      axios
        .post("Error", {
          dni: this.user.dni,
          password: this.user.password,
        })
        .then((response) => {
          // this.efectivo_caja();
          location.reload();
        })
        .catch((error) => {
           this.spinner = false;
          let er = error.response.data.errors;
          let mensaje = "comunicarse con el programador";
          if (er.hasOwnProperty("dni")) {
            mensaje = er.dni[0];
          } else if (er.hasOwnProperty("password")) {
            mensaje = er.password[0];
          } else if (er.hasOwnProperty("login")) {
            mensaje = er.login[0];
          }
          swal({
            text: mensaje,
            icon: "error",
            button: "aceptar",
             dangerMode: true,
          });
        });
    },
    cerrar_sesion() {
      axios.post("/cerrar").then((response) => {
        Vue.$toast.error("vuelve a intentarlo");
        location.reload();
      });
    },
  },
};
</script>
<script>
export default {
  data() {
    return {
      sucursal: [],
    };
  },
  created(){
     //this.listar_sucursal();
  },
  methods: {
    listar_sucursal() {
      axios.get("/api/listsucursal").then((res) => {
        this.sucursal = res.data.sucursal.data;
       // localStorage.setItem("sucursalStorage", JSON.stringify(res.data.sucursal.data));
      });
    },
  },
};
</script>
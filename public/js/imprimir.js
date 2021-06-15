const print = {}; 

function printSection ( el, binding ){
   // insertar css especial para la impresión, esto mostrar un contenedor especial y ocultara el resto de contenido
   let style = document.createElement('style');
   style.type = 'text/css';
   style.appendChild(document.createTextNode("@media screen{#printSection{display:none}}@media print{#printSection,body{overflow:visible!important}body *{visibility:hidden}#printSection,#printSection *{visibility:visible}#printSection{position:absolute;left:0;top:0;right:0}#printSection table{width:100%!important}}"));
   document.body.appendChild(style);

   // busco si existe mi sección para imprimir
   let printSection = document.getElementById('printSection');

   // si no existe la creo y la inserto en el body
   if (!printSection) {
      printSection = document.createElement('div');
      printSection.id = 'printSection';
      document.body.appendChild(printSection);
  }

   // creo el evento del click para cada que se de click en imprimir
   el.addEventListener('click', () => {

      // busco el contenido a imprimir, esto ya que como parametro tenia el id
      var elemToPrint = document.getElementById(binding.value);

      //si existe el contenido entonces lo imprimiré
      if (elemToPrint) {
         printSection.innerHTML = ''; // limpio el contenido anterior de mi sección a imprimir
         printElement(elemToPrint);
      }
   });

   // funcion para imprimir
   function printElement( elem ){

      // creo un nodo y lo inserto dentro de mi sección a imprimir
      let domClone = elem.cloneNode(true);
      printSection.appendChild(domClone);
      window.print(); //mando a imprimir
   }
}


// registro la directiva
print.install = function( Vue ){
   Vue.directive('print',{
      bind( el, binding){
         printSection( el, binding )
      }
   })
}

// exporto mi directiva
export default print
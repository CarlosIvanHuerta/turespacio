// Llamadas de Inicialización...
$(() => {
   Promise.all([
      inizializeCounterPost()
   ]).then(( responsePromise ) => {
      if ( responsePromise[0] )
         applyCounterPost()
   }).catch(error => {
      console.error('Error al aplicar conteo a post', error)
   })
})

const inizializeCounterPost = () => {

}

const applyCounterPost = () => {
}

TODO:
/*
<script>
  const TIME_TO_WAIT = 10000; // milisegundos = 10 segundos (ajustable)
  const COOKIE_DURATION_MIN = 10;

  function esperar(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
  }

  function guardarCookie(nombre, valor, minutos) {
    const expira = new Date();
    expira.setTime(expira.getTime() + (minutos * 60 * 1000));
    document.cookie = `${nombre}=${valor}; expires=${expira.toUTCString()}; path=/`;
  }

  function obtenerCookie(nombre) {
    const cookies = document.cookie.split(';');
    for (let c of cookies) {
      c = c.trim();
      if (c.startsWith(nombre + '=')) {
        return c.substring(nombre.length + 1);
      }
    }
    return null;
  }

  // Paso 1: Lógica de espera y cookie
  async function inizializeCounterPost() {
    const slug = document.body.dataset.slug || "sin-slug";
    const cookieNombre = `visitado_${slug}`;
    const yaVisitado = obtenerCookie(cookieNombre);

    if (yaVisitado) {
      console.log("⏳ Visita ya registrada recientemente");
      return false;
    }

    // Esperar el tiempo configurado
    await esperar(TIME_TO_WAIT);
    return true;
  }

  // Paso 2: Enviar conteo al servidor
  async function applyCounterPost() {
    const slug = document.body.dataset.slug || "sin-slug";

    try {
      const respuesta = await fetch("/visita.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json"
        },
        body: JSON.stringify({ slug: slug })
      });

      const resultado = await respuesta.json();

      if (resultado.ok) {
        console.log("✅ Visita registrada en el servidor");
        guardarCookie(`visitado_${slug}`, "1", COOKIE_DURATION_MIN);
      } else {
        console.warn("⚠️ El servidor no aceptó la visita");
      }

    } catch (error) {
      console.error("❌ Error al enviar conteo:", error);
    }
  }

  // Inicializador global
  $(() => {
    Promise.all([
      inizializeCounterPost()
    ]).then(([permitirContar]) => {
      if (permitirContar) {
        applyCounterPost();
      }
    }).catch(error => {
      console.error("Error al procesar el contador del post:", error);
    });
  });
</script>

*/
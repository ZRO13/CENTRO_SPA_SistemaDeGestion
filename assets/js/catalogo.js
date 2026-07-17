//autor: Marvin Castro
const txtBuscar = document.getElementById("buscarProducto");
const botones = document.querySelectorAll(".btnCategoria");

let categoriaSeleccionada = 0;

// ==========================
// CARGAR PRODUCTOS
// ==========================

function cargarProductos() {

    fetch(
        "index.php?controller=clienteProd&action=buscarAjax"
        + "&texto=" + encodeURIComponent(txtBuscar.value)
        + "&categoria=" + categoriaSeleccionada
    )
    .then(response => response.json())
    .then(data => {
        let html = "";
        data.forEach(p => {
            html += `
            <div class="producto-card">
                <img src="${p.imagen}">
                <div class="producto-info">
                    <span class="categoria">
                        ${p.nombre_categoria}
                    </span>
                    <h3>
                        ${p.nombre}
                    </h3>
                    <p>
                        ${p.descripcion}
                    </p>
                    <div class="precio">
                        $${parseFloat(p.precio).toFixed(2)}
                    </div>
                    <div class="acciones-producto">
                        <a
                        class="btn btn-secondary"
                        href="index.php?controller=clienteProd&action=detalle&id=${p.id_producto}">
                            Ver detalles
                        </a>
                        <button
                        class="btn btn-primary btnAgregarCarrito"
                        data-id="${p.id_producto}">
                            Agregar
                        </button>
                    </div>
                </div>
            </div>
            `;
        });
        if(data.length === 0){
            html = `
            <h3 style="grid-column:1/-1;text-align:center;">
                No se encontraron productos.
            </h3>
            `;
        }
        document
        .getElementById("catalogoGrid")
        .innerHTML = html;
    });
}

// BUSQUEDA
txtBuscar.addEventListener(
    "keyup",
    cargarProductos
);

// FILTROS
botones.forEach(btn=>{
    btn.addEventListener("click",function(){
        categoriaSeleccionada=this.dataset.id;
        botones.forEach(b=>
            b.classList.remove("activo")
        );
        this.classList.add("activo");
        cargarProductos();
    });
});

// ==========================
// AGREGAR AL CARRITO (delegación de eventos)
// ==========================
// Usamos delegación en el contenedor #catalogoGrid, que SIEMPRE existe,
// en vez de poner el listener en cada botón. Así funciona tanto con los
// productos que PHP renderiza al inicio como con los que llegan por AJAX.

document
.getElementById("catalogoGrid")
.addEventListener("click", function(e){

    const btn = e.target.closest(".btnAgregarCarrito");
    if(!btn) return; // el clic no fue sobre un botón "Agregar"

    let id = btn.dataset.id;

    fetch(
        "index.php?controller=carrito&action=agregar&id=" + id
    )
    .then(response => response.json())
    .then(data => {
        console.log("Respuesta servidor:", data);

        if(data.error){
            alert(data.error);
            return;
        }

        cargarCarrito();

        document
        .getElementById("carritoPanel")
        .classList.add("activo");
    })
    .catch(err => console.error("Error al agregar:", err));
});

// ==========================
// ABRIR / CERRAR CARRITO
// ==========================

document
.getElementById("btnCarrito")
.addEventListener("click", () => {
    document
    .getElementById("carritoPanel")
    .classList.add("activo");
    cargarCarrito();
});

document
.getElementById("cerrarCarrito")
.addEventListener("click", () => {
    document
    .getElementById("carritoPanel")
    .classList.remove("activo");
});

// ==========================
// CARGAR CARRITO
// ==========================

function cargarCarrito(){
    fetch(
        "index.php?controller=carrito&action=obtener"
    )
    .then(r => r.json())
    .then(data => {
        let html = "";
        let total = 0;
        let cantidadTotal = 0;

        data.forEach(p => {
            let cantidad = parseInt(p.cantidad);
            let subtotal = p.precio * p.cantidad;
            total += subtotal;
            cantidadTotal += parseInt(p.cantidad);

            html += `
            <div class="item-carrito">
                <img src="${p.imagen}">
                <div>
                    <h4>
                        ${p.nombre}
                    </h4>
                    <p>
                        $${parseFloat(p.precio).toFixed(2)}
                    </p>
                    <button
                    onclick="cambiarCantidad(${p.id_producto},${p.cantidad-1})">
                        -
                    </button>
                    ${p.cantidad}
                    <button
                    onclick="cambiarCantidad(${p.id_producto},${p.cantidad+1})">
                        +
                    </button>
                    <button class="btn-eliminar-item" onclick="eliminarDelCarrito(${p.id_producto})" title="Eliminar producto">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </div>
            </div>
            `;
        });

        if(data.length === 0){
            html = "<p>Tu carrito está vacío</p>";
        }

        document
        .getElementById("contenidoCarrito")
        .innerHTML = html;

        document
        .getElementById("totalCarrito")
        .innerHTML = "$" + total.toFixed(2);

        document
        .getElementById("contadorCarrito")
        .innerHTML = cantidadTotal;
    });
}

// ==========================
// CAMBIAR CANTIDAD
// ==========================

function cambiarCantidad(id, cantidad){
    if(cantidad <= 0){
        cantidad = 0;
    }
    fetch(
        "index.php?controller=carrito&action=actualizar"
        + "&id=" + id
        + "&cantidad=" + cantidad
    )
    .then(() => {
        cargarCarrito();
    });
}

// ==========================
// ELIMINAR PRODUCTO DEL CARRITO
// ==========================

function eliminarDelCarrito(id){
    fetch(
        "index.php?controller=carrito&action=eliminar&id=" + id
    )
    .then(response => response.json())
    .then(data => {
        cargarCarrito();
    })
    .catch(err => console.error("Error al eliminar:", err));
}

document.getElementById("btnFinalizarCompra")?.addEventListener("click", function(){

    this.disabled = true;
    this.textContent = "Procesando...";

    fetch("index.php?controller=carrito&action=confirmar")
    .then(response => response.json())
    .then(data => {
        if(data.error){
            Swal.fire({
                icon: "warning",
                title: "Stock insuficiente",
                text: data.error,
                confirmButtonText: "Entendido",
                confirmButtonColor: "#355C7D"
            })
            this.disabled = false;
            this.innerHTML = '<i class="fa-solid fa-credit-card"></i> Finalizar compra';
            return;
        }

        // Cierra el panel del carrito y muestra el modal de éxito
        document
        .getElementById("carritoPanel")
        .classList.remove("activo");

        document
        .getElementById("modalCompraExitosa")
        .classList.add("activo");

        this.disabled = false;
        this.innerHTML = '<i class="fa-solid fa-credit-card"></i> Finalizar compra';
    })
    .catch(err => {
        console.error("Error al confirmar compra:", err);
        Swal.fire({
            icon: "error",
            title: "Error de conexión",
            text: "No se pudo procesar tu compra. Intenta de nuevo.",
            confirmButtonText: "Cerrar",
            confirmButtonColor: "#355C7D"
        });
        this.disabled = false;
        this.innerHTML = '<i class="fa-solid fa-credit-card"></i> Finalizar compra';
    });
});

// Botón del modal: cierra el modal y redirige
document
.getElementById("btnCerrarModalCompra")
?.addEventListener("click", () => {
    window.location.href = "index.php?controller=area-cliente&action=inicio";
});

// ==========================
// AL CARGAR LA PÁGINA
// ==========================
cargarCarrito();
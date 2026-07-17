// Autor: Alexander Calle
// Búsqueda en vivo del listado de Ventas (por cliente, correo o número de venta).

const inputBuscarVenta = document.getElementById("buscarVenta");
const btnBuscarVenta = document.getElementById("btnBuscarVenta");
const tablaVentas = document.getElementById("tablaVentas");

function renderFilaVacia(mensaje) {
    return `<tr><td colspan="7" class="ventas-vacio">${mensaje}</td></tr>`;
}

function claseEstado(estado) {
    switch ((estado || "").toLowerCase()) {
        case "pagada":
            return "badge-success ventas-estado-pagada";
        case "pendiente":
            return "badge-warning ventas-estado-pendiente";
        case "cancelada":
            return "badge-danger ventas-estado-cancelada";
        default:
            return "badge-info";
    }
}

function buscarVentas() {
    if (!tablaVentas) return;

    const texto = inputBuscarVenta ? inputBuscarVenta.value.trim() : "";

    fetch("index.php?controller=venta&action=buscarAjax&texto=" + encodeURIComponent(texto))
        .then(response => response.json())
        .then(ventas => {
            if (!ventas || ventas.length === 0) {
                tablaVentas.innerHTML = renderFilaVacia("No existen ventas registradas.");
                return;
            }

            let html = "";
            ventas.forEach(venta => {
                const fecha = new Date(venta.fecha_venta).toLocaleString("es-EC", {
                    day: "2-digit", month: "2-digit", year: "numeric",
                    hour: "2-digit", minute: "2-digit"
                });

                html += `
                <tr>
                    <td class="ventas-id">#${venta.id_venta}</td>
                    <td class="ventas-cliente">${venta.cliente}</td>
                    <td class="ventas-correo">${venta.correo}</td>
                    <td class="ventas-fecha">${fecha}</td>
                    <td class="ventas-total">$${parseFloat(venta.total).toFixed(2)}</td>
                    <td>
                        <span class="badge ventas-estado ${claseEstado(venta.estado)}">${venta.estado}</span>
                    </td>
                    <td>
                        <div class="ventas-acciones">
                            <a href="index.php?controller=venta&action=detalle&id=${venta.id_venta}"
                                class="btn btn-primary" title="Ver detalle">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                        </div>
                    </td>
                </tr>`;
            });

            tablaVentas.innerHTML = html;
        })
        .catch(() => {
            tablaVentas.innerHTML = renderFilaVacia("Ocurrió un error al buscar las ventas.");
        });
}

if (btnBuscarVenta) {
    btnBuscarVenta.addEventListener("click", buscarVentas);
}

if (inputBuscarVenta) {
    inputBuscarVenta.addEventListener("keyup", (e) => {
        if (e.key === "Enter") buscarVentas();
    });
}

const form = document.querySelector("form");
const imagen = document.getElementById("imagen");
const preview = document.getElementById("preview");
const txtBuscar = document.getElementById("buscarProducto");
if (imagen && preview) {
    imagen.addEventListener("input", () => {
        preview.src = imagen.value;
    });

}
if (form) {
    form.addEventListener("submit", function (e) {
        const nombre = form.nombre.value.trim();
        const precio = parseFloat(form.precio.value);
        const stock = parseInt(form.stock.value);

        if (nombre.length < 3) {
            alert("Ingrese un nombre válido.");
            e.preventDefault();
            return;
        }
        if (precio <= 0) {
            alert("El precio debe ser mayor a cero.");
            e.preventDefault();
            return;
        }
        if (stock < 0) {
            alert("El stock no puede ser negativo.");
            e.preventDefault();
        }
    });
}

if (txtBuscar) {
    txtBuscar.addEventListener("input", function () {
        fetch(
            "index.php?controller=producto&action=buscarAjax&texto="
            + encodeURIComponent(this.value)
        )
            .then(response => response.json())
            .then(data => {
                let html = "";
                data.forEach(p => {
                    html += `
                <tr>
                    <td>${p.id_producto}</td>
                    <td>
                        ${p.imagen
                            ? `<img src="${p.imagen}" width="70" style="border-radius:8px;">`
                            : "Sin imagen"
                        }
                    </td>
                    <td>${p.nombre}</td>
                    <td>${p.nombre_categoria}</td>
                    <td>$${parseFloat(p.precio).toFixed(2)}</td>
                    <td>${p.stock}</td>
                    <td>
                        ${p.disponibilidad == 1
                            ? '<span class="badge bg-success">Disponible</span>'
                            : '<span class="badge bg-danger">No disponible</span>'
                        }
                    </td>
                    <td>
                        <a
                            class="btn btn-warning"
                            href="index.php?controller=producto&action=editar&id=${p.id_producto}">
                            <i class="fa-solid fa-pen"></i>
                        </a>
                        <a
                            class="btn btn-danger"
                            onclick="return confirm('¿Eliminar producto?')"
                            href="index.php?controller=producto&action=eliminar&id=${p.id_producto}">
                            <i class="fa-solid fa-trash"></i>
                        </a>
                    </td>
                </tr>
                `;
                });
                if (data.length === 0) {
                    html = `
                    <tr>
                        <td colspan="8">
                            No se encontraron productos.
                        </td>
                    </tr>
                `;
                }
                document.getElementById("tablaProductos").innerHTML = html;
            });
    });
}

document.querySelectorAll(".btnEliminar").forEach(btn => {
    btn.addEventListener("click", function () {
        const id = this.dataset.id;
        Swal.fire({
            title: "¿Eliminar producto?",
            text: "Esta acción no se puede deshacer.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Sí, eliminar",
            cancelButtonText: "Cancelar"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location =
                    "index.php?controller=producto&action=eliminar&id=" + id;
            }
        });
    });
});
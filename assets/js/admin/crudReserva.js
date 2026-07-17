// autor: Maria Belen Cassiaux Guerrero
document.addEventListener("DOMContentLoaded", () => {
    renderTabla();
    cargarEmpleados();
});

// --- RENDERIZA TABLA CON FILTROS ---
async function renderTabla() {
    const fecha = document.getElementById("filtroFecha").value;
    const buscar = encodeURIComponent(document.getElementById("buscadorGeneral").value);
    const estado = document.getElementById("filtroEstado").value;

    const url = `index.php?controller=citas&action=obtenerCitas&fecha=${fecha}&buscar=${buscar}&estado=${estado}`;
    const cuerpo = document.getElementById("cuerpoTabla");

    try {
        cuerpo.innerHTML = "<tr><td colspan='8'>Cargando datos...</td></tr>";
        const response = await fetch(url);
        
        if (!response.ok) throw new Error("Error en el servidor");
        
        const citas = await response.json();
        cuerpo.innerHTML = "";

        if (!citas || citas.length === 0) {
            cuerpo.innerHTML = "<tr><td colspan='8'>No hay citas registradas.</td></tr>";
            return;
        }

        const citasAgrupadas = {};

        citas.forEach(c => {
            const key = c.id_cita;
            if (!citasAgrupadas[key]) {
                citasAgrupadas[key] = { ...c, lista_servicios: [c.nombre_servicio] };
            } else {
                if (!citasAgrupadas[key].lista_servicios.includes(c.nombre_servicio)) {
                    citasAgrupadas[key].lista_servicios.push(c.nombre_servicio);
                }
            }
        });

        Object.values(citasAgrupadas).forEach(c => {
            const row = document.createElement("tr");
            row.setAttribute("data-id", c.id_cita);
            
            row.innerHTML = `
                <td>${c.nombre_cliente || 'N/A'}</td>
                <td>${Array.from(c.lista_servicios).join(", ")}</td> <!-- Servicios concatenados -->
                <td>
                    ${c.nombre_empleado 
                        ? `${c.nombre_empleado} ${c.apellido_empleado} (${c.cargo_empleado})` 
                        : '<em>Sin asignar</em>'}
                </td>
                <td>${c.fecha}</td>
                <td>${c.hora.substring(0, 5)}</td>
                <td>${c.estado}</td>
                <td>${c.observacion || ''}</td>
                <td>
                    <button class="btn btn-warning-soft" onclick="abrirModal(${c.id_cita})"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-danger" onclick="eliminarCita(${c.id_cita})"><i class="fas fa-trash"></i></button>
                </td>
            `;
            cuerpo.appendChild(row);
        });
    } catch (error) {
        console.error("Error al cargar la tabla:", error);
        cuerpo.innerHTML = "<tr><td colspan='8'>Error al cargar los datos.</td></tr>";
    }
}

// --- MODAL Y EDICIÓN ---
function abrirModal(idCita) {
    const fila = document.querySelector(`tr[data-id="${idCita}"]`);
    if (!fila) return alert("Error: No se pudieron cargar los datos.");

    const celdas = fila.getElementsByTagName("td");
    document.getElementById("editId").value = idCita;
    document.getElementById("editEstado").value = celdas[5].innerText.trim();
    document.getElementById("editObservacion").value = celdas[6].innerText.trim() === 'null' ? '' : celdas[6].innerText.trim();
    document.getElementById("editEmpleado").value = fila.getAttribute("data-id-empleado") || "";

    document.getElementById("modalEdicion").style.display = "block";
}

function cerrarModal() {
    document.getElementById("modalEdicion").style.display = "none";
    document.getElementById("formEdicion").reset();
}

// --- GUARDAR EDICIÓN CON VALIDACIÓN ---
async function guardarEdicion() {
    const estado = document.getElementById("editEstado").value;
    
    // Validación simple de cliente
    if (!estado) {
        alert("Por favor, seleccione un estado válido.");
        return;
    }

    const formData = new FormData(document.getElementById("formEdicion"));
    try {
        const response = await fetch('index.php?controller=citas&action=actualizar', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();
        
        if (result.success) {
            alert("Cita actualizada correctamente.");
            cerrarModal();
            renderTabla();
        } else {
            alert("Error al actualizar: " + (result.message || "Intente nuevamente."));
        }
    } catch (error) {
        console.error("Error:", error);
        alert("Error de conexión con el servidor.");
    }
}

// --- ELIMINAR ---
async function eliminarCita(id) {
    if (!confirm("¿Estás seguro de eliminar esta cita?")) return;

    try {
        const response = await fetch(`index.php?controller=citas&action=eliminar&id=${encodeURIComponent(id)}`);
        const result = await response.json(); 
        
        if (result.success) {
            alert("Cita eliminada correctamente");
            renderTabla();
        } else {
            alert("Error al eliminar: " + (result.message || "Error desconocido"));
        }
    } catch (error) {
        console.error("Error en eliminarCita:", error);
    }
}

// --- EMPLEADOS ---
async function cargarEmpleados() {
    try {
        const response = await fetch('index.php?controller=citas&action=obtenerEmpleados');
        const empleados = await response.json();
        const select = document.getElementById('editEmpleado');
        select.innerHTML = '<option value="">-- Sin asignar --</option>';
        empleados.forEach(emp => {
            // Concatenamos nombre, apellido y cargo
            const nombreCompleto = `${emp.nombre} ${emp.apellido} - ${emp.cargo}`;
            select.innerHTML += `<option value="${emp.id_empleado}">${nombreCompleto}</option>`;
        });
    } catch (err) {
        console.error("Error cargando empleados", err);
    }
}

function limpiarFiltros() {
    document.getElementById("filtroFecha").value = "";
    document.getElementById("buscadorGeneral").value = "";
    document.getElementById("filtroEstado").value = "todos";
    renderTabla();
}
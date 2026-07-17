// autor: Maria Belen Cassiaux Guerrero
document.addEventListener("DOMContentLoaded", actualizarTabla);

/**
 * Función que actualiza la tabla de citas obteniendo datos del servidor.
 * Construye la URL con parámetros para que el servidor filtre correctamente.
 */
async function actualizarTabla() {
    const cuerpoCita = document.getElementById("cuerpoCita");
    if (!cuerpoCita) return;
    
    // Obtener valores de los filtros desde el DOM
    const fecha = document.getElementById("filtroFecha")?.value || "";
    const estado = document.getElementById("filtroEstado")?.value || "todos";
    const buscar = document.getElementById("buscadorGeneral")?.value || "";

    const url = `index.php?controller=citas&action=obtenerCitasCliente&fecha=${fecha}&buscar=${buscar}&estado=${estado}`;

    try {
        cuerpoCita.innerHTML = "<tr><td colspan='7'>Cargando datos...</td></tr>";
        const response = await fetch(url);
        
        if (!response.ok) throw new Error("Error en la conexión con el servidor");
        
        const citas = await response.json();

        cuerpoCita.innerHTML = ""; 

        if (!citas || citas.length === 0) {
            cuerpoCita.innerHTML = "<tr><td colspan='7' style='text-align:center;'>No se encontraron citas.</td></tr>";
            return;
        }
        
        // --- LÓGICA DE AGRUPACIÓN ROBUSTA ---
        const citasAgrupadas = {};

        citas.forEach(c => {
            const key = `${c.fecha}_${c.hora}_${c.id_cliente}`;
            const nombreS = c.nombre_servicio || 'Sin servicio';
            
            if (!citasAgrupadas[key]) {
                citasAgrupadas[key] = { 
                    ...c, 
                    serviciosSet: new Set() 
                };
            }

            if (nombreS !== 'Sin servicio') {
                citasAgrupadas[key].serviciosSet.add(nombreS);
            }
        });

        // --- DIBUJAR LA TABLA ---
        Object.values(citasAgrupadas).forEach(c => {
            const row = document.createElement("tr");
            
            const listaUnica = c.serviciosSet.size > 0 
                ? Array.from(c.serviciosSet).join(", ") 
                : 'N/A';
            
            row.innerHTML = `
                <td>${c.nombre_cliente || 'N/A'}</td>
                <td>${listaUnica}</td> 
                <td>${c.nombre_empleado || '<em>No asignado</em>'}</td>
                <td>${c.fecha || 'N/A'}</td>
                <td>${c.hora ? c.hora.substring(0, 5) : 'N/A'}</td>
                <td>${c.estado || 'N/A'}</td>
                <td>${c.observacion || ''}</td>
            `;
            cuerpoCita.appendChild(row);    
        });
    } catch (error) {
        console.error("Error al cargar la tabla:", error);
        cuerpoCita.innerHTML = 
        "<tr><td colspan='7' style='color:red; text-align:center;'>Error al cargar los datos: " + error.message + "</td></tr>";
    }
}

/**
 * Limpia los filtros y recarga la tabla.
 */
function limpiarFiltros() {
    document.getElementById("filtroFecha").value = "";
    document.getElementById("buscadorGeneral").value = "";
    document.getElementById("filtroEstado").value = "todos";
    actualizarTabla();
}
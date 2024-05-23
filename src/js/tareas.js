/** IFFE ~ Es una forma de proteger variables y funciones, cuando tenemos varios archivo de JS 
para que estas no se mesclen con otras. */

// Esto es un IFFE
(function () {
    obtenerTareas();
    let tareas = [];
    let filtradas = [];

    // Btn para mostrar el modal al agg una tarea
    const nuevaTareaBtn = document.querySelector('#agregar-tarea');
    nuevaTareaBtn.addEventListener('click', function() {
        mostrarFormulario();
    });

    // Filtros de búsqueda
    const filtros = document.querySelectorAll('#filtros input[type="radio');
    filtros.forEach( radio => {
        radio.addEventListener('input', filtrarTareas);
    });

    function filtrarTareas(e) {
        const filtro = e.target.value;

        if(filtro !== '') {
            filtradas = tareas.filter(tarea => tarea.estado === filtro);
        } else {
            filtradas = [];
        }

        mostrarTareas();
    }

    async function obtenerTareas() {
        try {
            const id = obtenerDatosProyecto();
            const url = `/api/tareas?id=${id}`;
            const respuesta = await fetch(url);
            const resultado = await respuesta.json();
            
            tareas = resultado.tareas
            mostrarTareas();
        } catch (error) {
            console.log(error);
        }
    }

    function mostrarTareas() {
        limpiarTareas();
        totalPendientes();
        totalCompletas();

        const arrayTareas = filtradas.length ? filtradas : tareas;

        if(arrayTareas.length === 0) {
        const contenedorTareas = document.querySelector('#listado-tareas');

        const textoNoTareas = document.createElement('LI');
        textoNoTareas.textContent = 'No hay tareas';
        textoNoTareas.classList.add('no-tareas');

        contenedorTareas.appendChild(textoNoTareas);
        return;
        }

        const estados = {
            0: 'Pendiente',
            1: 'Completa'
        }

        arrayTareas.forEach(tarea => {
            const contenedorTarea = document.createElement('LI');
            contenedorTarea.dataset.tareaId = tarea.id;
            contenedorTarea.classList.add('tarea');

            const nombreTarea = document.createElement('P');
            nombreTarea.textContent = tarea.nombre;
            nombreTarea.ondblclick = function() {
                mostrarFormulario(true, tarea, {...tarea});
            };

            const opcionesDiv = document.createElement('DIV');
            opcionesDiv.classList.add('opciones');

            const btnEstadoTarea = document.createElement('BUTTON');
            btnEstadoTarea.classList.add('estado-tarea');
            btnEstadoTarea.classList.add(`${estados[tarea.estado].toLowerCase()}`);
            btnEstadoTarea.textContent = estados[tarea.estado];
            btnEstadoTarea.dataset.estadoTarea = tarea.estado;
            btnEstadoTarea.onclick = function() {
                cambiarEstadoTarea({...tarea});
            };
            
            const btnEliminarTarea = document.createElement('BUTTON');
            btnEliminarTarea.classList.add('eliminar-tarea');
            btnEliminarTarea.dataset.idTarea = tarea.id;
            btnEliminarTarea.textContent = 'Eliminar';
            btnEliminarTarea.onclick = function() {
                confirmarEliminarTarea({...tarea});
            };

            opcionesDiv.appendChild(btnEstadoTarea);
            opcionesDiv.appendChild(btnEliminarTarea);

            contenedorTarea.appendChild(nombreTarea);
            contenedorTarea.appendChild(opcionesDiv);

            const listadoTareas = document.querySelector('#listado-tareas');
            listadoTareas.appendChild(contenedorTarea);
        });
    }

    function totalPendientes() {
        const totalPendientes = tareas.filter(tarea => tarea.estado === "0");
        const pendientesRadio = document.querySelector('#pendientes');

        if(totalPendientes.length === 0) {
            pendientesRadio.disabled = true;
        } else {
            pendientesRadio.disabled = false;
        }   
    }
    function totalCompletas() {
        const totalCompletas = tareas.filter(tarea => tarea.estado === "1");
        const completasRadio = document.querySelector('#completadas');

        if(totalCompletas.length === 0) {
            completasRadio.disabled = true;
        } else {
            completasRadio.disabled = false;
        }   
    }

    function mostrarFormulario( editar = false, tarea = {} ) {
        const modal = document.createElement('DIV');
        modal.classList.add('modal');
        modal.innerHTML = `
        <form class="formulario nueva-tarea">
                <legend>${editar ? 'Editar Tarea' : 'Añade una nueva tarea'}</legend>
                <div class="campo">
                    <label>Tarea</label>
                    <input 
                        type="text"
                        name="tarea"
                        placeholder="${tarea.nombre ? 'Edita la Tarea' : 'Añadir Tarea al Proyecto Actual'}"
                        id="tarea"
                        value="${tarea.nombre ? tarea.nombre : ''}"
                    />
                </div>
                <div class="opciones">
                    <input 
                        type="submit" 
                        class="submit-nueva-tarea" 
                        value="${tarea.nombre ? 'Guardar Cambios' : 'Añadir Tarea'} " 
                    />
                    <button type="button" class="cerrar-modal">Cancelar</button>
                </div>
            </form>
        `;

        setTimeout(() => {
            const formulario = document.querySelector('.formulario');
            formulario.classList.add('animar');
        }, 100)

        // Delegation
        modal.addEventListener('click', function(e) {
            // Prevenir acción por defecto
            e.preventDefault();

            if(e.target.classList.contains('cerrar-modal')) {
                const formulario = document.querySelector('.formulario');
                formulario.classList.add('cerrar');

                setTimeout(() => {
                    modal.remove();
                }, 500);

            }

            if(e.target.classList.contains('submit-nueva-tarea')) {
                const nombreTarea = document.querySelector('#tarea').value.trim();

                if(nombreTarea === '') {
                    // Mostrar una alerta de error
                    mostrarAlerta('El nombre de la tarea es obligatorio','error', document.querySelector('.formulario legend'));
                    return;
                }
                if(editar) {
                    tarea.nombre = nombreTarea;
                    actualizarTarea(tarea);
                } else {
                    agregarTarea(nombreTarea);
                }
            }
        })
        document.querySelector('.dashboard').appendChild(modal);
    }

    function mostrarAlerta(mensaje, tipo, referencia) {
        // Eliminar alerta previa
        const alertaPrevia = document.querySelector('.alerta');
        if(alertaPrevia) {
            alertaPrevia.remove();
        }

        const alerta = document.createElement('DIV');
        alerta.classList.add('alerta', tipo);
        alerta.textContent = mensaje;

        // Inserta la alerta antes del legend
        referencia.parentElement.insertBefore(alerta, referencia.nextElementSibling);

        // Quitar alerta despues de 1seg
        setTimeout(() => {
            alerta.remove();
        }, 1000);
    }

    // Consultar el servidor para añadir una nueva tarea al proyecto actual
    async function agregarTarea(tarea) {
        // Contruir la petición ~ Siempre que se quieren enviar datos se hace con FromData
        const datos = new FormData();
        datos.append('nombre', tarea);
        datos.append('proyectoId', obtenerDatosProyecto());

        // Try Cath ~ En caso de que no se conecte al servidor no detiene el resto del programa, Ayuda con el manejo de errores
        try {
            // URL a la cual se le enviara la petición
            const url = 'http://localhost:3000/api/tarea';
            // Con este Await se reliza la conexión a la API
            const respuesta = await fetch(url, { // Fetch por defecto es methodo GET por eso se especifica si se cambia a POST
                method:'POST',
                // Despues pasamos el FromDate y siempre se llamara body
                body: datos // Esto enviara los datos via fetch a la URL  
            })

            const resultado = await respuesta.json();

            mostrarAlerta(resultado.mensaje ,resultado.tipo , document.querySelector('.formulario legend'));

            if(resultado.tipo === 'exito') {
                const modal = document.querySelector('.modal');

                setTimeout(() => {
                    modal.remove();
                }, 2000);

                // Agregar el Objeto de tarea al global de tareas
                const tareaObj = {
                    id: String(resultado.id),
                    nombre: tarea,
                    estado: "0",
                    proyectoId: resultado.proyectoId
                }

                tareas = [...tareas, tareaObj];
                mostrarTareas();
            }

        } catch (error) {
            console.log(error);
        }
    }

    function cambiarEstadoTarea(tarea) {
        const nuevoEstadoTarea = tarea.estado === "1" ? "0" : "1";
        tarea.estado = nuevoEstadoTarea;
        actualizarTarea(tarea);
    }

    async function actualizarTarea(tarea) {
        const { estado, id, nombre, proyectoId } = tarea;
        const datos = new FormData();
        datos.append('id', id),
            datos.append('nombre', nombre),
            datos.append('estado', estado),
            datos.append('proyectoId', obtenerDatosProyecto());

        try {
            const url = 'http://localhost:3000/api/tarea/actualizar';
            const respuesta = await fetch(url, {
                method: 'POST',
                body: datos,
            });
            const resultado = await respuesta.json();

            if (resultado.respuesta.tipo === 'exito') {
                Swal.fire(
                    resultado.respuesta.mensaje,
                    resultado.mensaje,
                    'success'
                );

                const modal = document.querySelector('.modal');
                if(modal) {
                modal.remove();
                }

                tareas = tareas.map(tareaMemoria => {
                    if (tareaMemoria.id === id) {
                        tareaMemoria.estado = estado;
                        tareaMemoria.nombre = nombre;
                    }
                    return tareaMemoria;
                });

                mostrarTareas();
            }
        } catch (error) {
            console.log(error);
        }
    }

    function confirmarEliminarTarea(tarea) {
        Swal.fire({
            title: "Eliminar Tarea?",
            showCancelButton: true,
            confirmButtonText: "Si",
            cancelButtonText: "No"
        }).then((result) => {

            if (result.isConfirmed) {
                eliminarTarea(tarea);
            }
        });
    }

    async function eliminarTarea(tarea) {
        const { estado, id, nombre } = tarea;
        const datos = new FormData();
        datos.append('id', id),
            datos.append('nombre', nombre),
            datos.append('estado', estado),
            datos.append('proyectoId', obtenerDatosProyecto());

        try {
            const url = 'http://localhost:3000/api/tarea/eliminar';
            const respuesta = await fetch(url, {
                method: 'POST',
                body: datos,
            });

            const resultado = await respuesta.json();
            if (resultado.resultado) {

                Swal.fire('Eliminado!', resultado.mensaje, 'success');

                tareas = tareas.filter( tareaMemoria => tareaMemoria.id !== tarea.id);
                mostrarTareas();
            }


        } catch (error) {
            console.log(error)
        }
    }

    function obtenerDatosProyecto() {
        // Obtener URL única del proyecto
        const proyectoParams = new URLSearchParams(window.location.search);
        // Iterar sobre el obejeto y traer la información
        const proyecto = Object.fromEntries(proyectoParams.entries());
        return proyecto.id;
    }

    function limpiarTareas() {
        const listadoTareas = document.querySelector('#listado-tareas');

        while (listadoTareas.firstChild) {
            listadoTareas.removeChild(listadoTareas.firstChild);
        }
    }
})();
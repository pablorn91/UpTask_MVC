(function(){

    obtenerTareas();

    let tareas = [];

    //Boton para mostrar el Modal de Agregar Tarea
    const nuevaTareaBtn = document.querySelector('#agregar-tarea');
    nuevaTareaBtn.addEventListener('click', mostrarFormulario);

    async function obtenerTareas() {
        try {
            id = obtenerProyecto();
            const url = `/api/tareas?id=${id}`;
            const respuesta = await fetch(url);
            const resultado = await respuesta.json();

            tareas = resultado.tareas;
            mostrarTareas();
            
        } catch (error) {
            console.log(error);
        }
    }

    function mostrarTareas () {
        limpiarTareas();
        if (tareas.length === 0 ) {
            const contenedorTareas = document.querySelector('#listado-tareas');

            const textoNotareas = document.createElement('LI');
            textoNotareas.textContent = 'No Hay Tareas';
            textoNotareas.classList.add('no-tareas');

            contenedorTareas.appendChild(textoNotareas);
            return;
        }

        const estados = {
            0: 'Pendiente',
            1: 'Completa'
        };

        tareas.forEach(tarea => {
            const contenedorTarea = document.createElement('LI');
            contenedorTarea.dataset.tareaId = tarea.id;
            contenedorTarea.classList.add('tarea');

            const nombreTarea = document.createElement('P');
            nombreTarea.textContent = tarea.nombre;

            const opcionesDiv = document.createElement('DIV');
            opcionesDiv.classList.add('opciones');

            //Botones
            const btnEstadoTarea = document.createElement('BUTTON');
            btnEstadoTarea.classList.add('estado-tarea');
            btnEstadoTarea.classList.add(`${estados[tarea.estado].toLowerCase()}`);
            btnEstadoTarea.textContent = estados[tarea.estado];
            btnEstadoTarea.dataset.estadoTarea = tarea.estado;

            const btnEliminarTarea = document.createElement('BUTTON');
            btnEliminarTarea.classList.add('eliminar-tarea');
            btnEliminarTarea.dataset.idTarea = tarea.id;
            btnEliminarTarea.textContent = 'Eliminar';

            opcionesDiv.appendChild(btnEstadoTarea);
            opcionesDiv.appendChild(btnEliminarTarea);

            contenedorTarea.appendChild(nombreTarea);
            contenedorTarea.appendChild(opcionesDiv);

            const listadoTareas = document.querySelector('#listado-tareas');
            listadoTareas.appendChild(contenedorTarea);
        });
    }

    function mostrarFormulario() {
        const modal = document.createElement('DIV');
        modal.classList.add('modal');
        
        modal.innerHTML = `
            <form class="formulario nueva-tarea"> 
                <legend>Añade una nueva Tarea</legend>
                <div class="campo">
                    <label for="tarea">Tarea</label>
                    <input
                        type="text"
                        name="tarea"
                        placeholder="Añadir tarea al Proyecto Actual"
                        id="tarea"
                    />
                </div>
                <div class="opciones">
                    <input type="submit" class="submit-nueva-tarea" value="Añadir Tarea" />
                    <button type="button" class="cerrar-modal">Cancelar</button>
                </div>
            </form>
        `;

        setTimeout(() => {
            const formulario = document.querySelector('.formulario');
            formulario.classList.add('animar');
        }, 0);

        modal.addEventListener('click', function(e){
            e.preventDefault();

            if (e.target.classList.contains('cerrar-modal') || e.target.classList.contains('modal') ) {
                const formulario = document.querySelector('.formulario');
                formulario.classList.add('cerrar');
                setTimeout(() => {
                    modal.remove();
                }, 500);
            } 

            if (e.target.classList.contains('submit-nueva-tarea') ) {
                submitFormularioNuevaTarea();
            }
          
        });

        document.querySelector('.dashboard').appendChild(modal);
    }

    function submitFormularioNuevaTarea() {
        const tarea = document.querySelector('#tarea').value.trim();
        if (tarea === '') {
            //mostrar una alerta de error
            mostrarAlerta('El nombre de la tarea es Obligatorio', 'error', document.querySelector('.formulario legend'));
            return;
        } 

        agregarTarea(tarea);
    }
    //Muestra un mensaje en la interfaz
    function mostrarAlerta(mensaje, tipo, referencia) {

        //Previene la creacion de multiples alertas
        const alertaPrev = document.querySelector('.alerta');
        if (alertaPrev) {
            alertaPrev.remove();
        }

        const alerta = document.createElement('DIV');
        alerta.classList.add('alerta', tipo);
        alerta.textContent = mensaje;
        
        //inserta la alerta antes del legend
        referencia.parentElement.insertBefore(alerta,referencia.nextElementSibling);

        //Eliminar la alerta despues de 5seg
        setTimeout(() => {
            alerta.remove();
        }, 5000);
    }

    //Consultar el servidor para añadir una nueva tarea al proyecto actual
    async function agregarTarea(tarea) {
        //Construir la petición
        const datos = new FormData();
        datos.append('nombre', tarea);
        datos.append('proyectoId', obtenerProyecto());  

        try {
            const url = 'http://localhost:3000/api/tarea';
            const respuesta = await fetch( url, {
                method: 'POST',
                body: datos
            });
             const resultado = await respuesta.json();
             mostrarAlerta(resultado.mensaje, resultado.tipo, document.querySelector('.formulario legend'));

             if (resultado.tipo === 'exito') {
                 const modal = document.querySelector('.modal');
                 setTimeout(() => {
                    modal.remove();
                 }, 1000);
                 
                 //Agregar el Objeto de tarea al global de tareas
                 const tareaObj = {
                     id: String(resultado.id),
                     nombre: tarea,
                     estado: '0',
                     proyectoId: resultado.proyectoId
                 }

                 tareas = [...tareas, tareaObj];
                 mostrarTareas();

                 console.log(tareaObj);
             }

        } catch (error) {
            console.log(error);
        }
    }

    function obtenerProyecto() {
        const proyectoParams = new URLSearchParams(window.location.search);
        const proyecto = Object.fromEntries(proyectoParams.entries());
        return proyecto.id;
    }

    function limpiarTareas(){
        const listadoTareas = document.querySelector('#listado-tareas');

        while(listadoTareas.firstChild) {
            listadoTareas.removeChild(listadoTareas.firstChild);
        }
    }
}) ();
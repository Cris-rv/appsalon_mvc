// La variable paso se asigna en 1 para que muestre el tab de servicios y esta atado a cuantas secciones tengamos como data-paso y paso ya que si colocamos un numero que no existe el codigo no funciona
let paso = 1;
const pasoInicial = 1;
const pasoFinal = 3;

const cita = {
    id: '',
    nombre: '',
    fecha: '',
    hora: '',
    servicios: []
};

document.addEventListener('DOMContentLoaded', function() {
    iniciarApp();
});

function iniciarApp() {

    mostrarSeccion(); // Muestra y oculta las secciones
    tabs(); // Cambia la seccion cuando se presionen los tabs
    botonesPaginador(); //Agrega o quita los botones del paginador
    paginaAnterior();
    paginaSiguiente();
    
    consultarAPI(); // Consulta la API en el backend de php

    idCliente(); // Añade el id del cliente al objeto de cita
    nombreCliente(); // Añade el nombre del cliente al objeto de cita
    seleccionarFecha(); // Añade la fecha de la cita en el objeto
    seleccionarHora(); // Añade la hora de la cita en el objeto

    mostrarResumen(); // Muestra el resumen de la cita
}

function mostrarSeccion() {

    // Remover la clase mostrar de la seccion actual
    const seccionAnterior = document.querySelector('.mostrar');

    if(seccionAnterior) {
    seccionAnterior.classList.remove('mostrar');
    }

    // Seleccionar la seccion con el paso...
    const pasoSelector = `#paso-${paso}`;
    const seccion = document.querySelector(pasoSelector);
    seccion.classList.add('mostrar');

    
    // Remover el tab anterior
    const tabAnterior = document.querySelector('.actual');

    if(tabAnterior) {
        tabAnterior.classList.remove('actual');
    }

    // Resalta el tab actual
    const tab = document.querySelector(`[data-paso="${paso}"]`);
    tab.classList.add('actual');
}

function tabs() {
    const botones = document.querySelectorAll('.tabs button') 

    botones.forEach( boton => {
        boton.addEventListener('click', function(e) {
            paso = parseInt(e.target.dataset.paso);
            mostrarSeccion();

            botonesPaginador();
        })
    } )
}

function botonesPaginador() {

    const paginaAnterior = document.querySelector('#anterior');
    const paginaSiguiente = document.querySelector('#siguiente');

    if(paso === 1) {
        paginaAnterior.classList.add('ocultar');
        paginaSiguiente.classList.remove('ocultar')
    } else if(paso === 3) {
        paginaAnterior.classList.remove('ocultar');
        paginaSiguiente.classList.add('ocultar');

        mostrarResumen();
    } else if(paso === 2) {
        paginaAnterior.classList.remove('ocultar');
        paginaSiguiente.classList.remove('ocultar');
    }
}

function paginaAnterior() {
    const paginaAnterior = document.querySelector('#anterior')
    paginaAnterior.addEventListener('click', function() {
        
        if(paso <= pasoInicial) return;

        // Reescribimos la variable let para mostrarSeccion
        paso--;
        botonesPaginador();
        mostrarSeccion();
    })
}

function paginaSiguiente() {
    const paginaSiguiente = document.querySelector('#siguiente')
    paginaSiguiente.addEventListener('click', function() {
        
        if(paso >= pasoFinal) return;

        paso++;
        botonesPaginador();
        mostrarSeccion();
    })
}

async function consultarAPI() {

    try {
        // #1 - Traemos la url que vamos a consumir para obtener la informacion como JSON
        const url = `${location.origin}/api/servicios`;
        // #2 - Consumimos la url que nos retorna el status de respuesta
        const resultado = await fetch(url)
        // #3 - Treamos la informacion de la url que consumimos a manera de JSON de las muchas formas que nos provee fetch()
        const servicios = await resultado.json();
        mostrarServicios(servicios);


    } catch (error) {
        console.log(error)
    }
}

function mostrarServicios(servicios) {
    servicios.forEach( servicio => {
        // Creamos la variable y la asignamos al mismo tiempo
        const {id, nombre, precio} = servicio;

        const nombreServicio = document.createElement('P');
        nombreServicio.classList.add('nombre-servicio');
        nombreServicio.textContent = nombre;

        const precioServicio = document.createElement('P');
        precioServicio.classList.add('precio-servicio');
        precioServicio.textContent = `$${precio}`;

        const servicioDiv = document.createElement('DIV');
        servicioDiv.classList.add('servicio');
        servicioDiv.dataset.idServicio = id;
        servicioDiv.onclick = function() {
            seleccionarServicio(servicio)
        };

        servicioDiv.appendChild(nombreServicio);
        servicioDiv.appendChild(precioServicio);

        document.querySelector('#servicios').appendChild(servicioDiv);

    });
};

function seleccionarServicio(servicio) {
    const { id } = servicio; // Extraemos el id del servicio en que se le da click
    const { servicios } = cita; // Extraemos el arreglo de servicios de cita

    // Identificamos el elemento al que le damos click
    const divServicio = document.querySelector(`[data-id-servicio="${id}"]`);

    // Comprobar si un servicio ya fue agregado o quitarlo iterando sobre el arreglo de servicios en el objeto de cita - cont {servicios} = cita;
    if( servicios.some( agregado => agregado.id === servicio.id ) ) { // Retorna True o False si el id ya esta agregado
        // Eliminamos el seleccionado
        cita.servicios = servicios.filter( agregado => agregado.id !== servicio.id );
        divServicio.classList.remove('seleccionado');

    } else {
        // Agregarlo
        cita.servicios = [...servicios, servicio]; // Creamos una copia del arreglo y agregamos el servicio al final del arreglo (... - realiza una copia del arreglo)
        divServicio.classList.add('seleccionado');
    }
};

function idCliente() {
    cita.id = document.querySelector('#id').value;
}

function nombreCliente() {
    cita.nombre = document.querySelector('#nombre').value;
};

function seleccionarFecha() {
    const inputFecha = document.querySelector('#fecha');
    inputFecha.addEventListener('input', function(e) {

        const dia = new Date(e.target.value).getUTCDay();

        if( [6, 0].includes(dia) ) {
            e.target.value = '';
            mostrarAlerta('Fines de semana no permitidos', 'error', '.formulario');
        } else {
            cita.fecha = e.target.value;
        }
    })
};

function seleccionarHora() {
    const inputHora = document.querySelector('#hora');
    inputHora.addEventListener('input', function (e) {

        const horaCita = e.target.value;
        const hora = horaCita.split(":")[0];

        if(hora < 10 || hora > 18) {
            e.target.value = '';
            mostrarAlerta('Abrimos desde las 10hrs hasta 18hrs', 'error', '.formulario');
        } else {
            cita.hora = e.target.value;
        }
    })
};

function mostrarAlerta(mensaje, tipo, elemento, desaparece = true) {
    // Previene que se genere mas de una alerta
    const alertaPrevia = document.querySelector('.alertas');
    if(alertaPrevia) {
        alertaPrevia.remove();
    };

    // Scripting para crear la alerta
    const alerta = document.createElement('DIV');
    alerta.textContent = mensaje;
    alerta.classList.add('alertas');
    alerta.classList.add(tipo);

    const referencia = document.querySelector(elemento)
    referencia.appendChild(alerta); 

    if(desaparece) {
        // Eliminar la alerta
        setTimeout(() => {
            alerta.remove();
        }, 5000) 
    }

};

function mostrarResumen() {
    const resumen = document.querySelector('.contenido-resumen');

    while(resumen.firstChild) {
        resumen.removeChild(resumen.firstChild);
    }

    if(Object.values(cita).includes('') || cita.servicios.length === 0) {
        mostrarAlerta('Faltan Datos de Servicios, Fecha u Hora', 'error', '.contenido-resumen', false);
        return;
    }; 

    const { nombre, fecha, hora, servicios } = cita;

    // Headin para servicios en resumen
    const headingServicios = document.createElement('H3');
    headingServicios.textContent = "Resumen de los Servicios";
    resumen.appendChild(headingServicios);

    // Iterando y mostrando los servicios
    servicios.forEach(servicio => {
        const { id, nombre, precio } = servicio;
        const contenedorServicio = document.createElement('DIV');
        contenedorServicio.classList.add('contenedor-servicio');

        const nombreServicio = document.createElement('P');
        const precioServicio = document.createElement('P');

        nombreServicio.textContent = nombre;
        precioServicio.innerHTML = `<span>Precio:</span> $${precio}`;

        contenedorServicio.appendChild(nombreServicio);
        contenedorServicio.appendChild(precioServicio);

        resumen.appendChild(contenedorServicio);
    });

    // Heading de la informacion de cita
    const headingCita = document.createElement('H3');
    headingCita.textContent = "Informacion de la Cita";
    resumen.appendChild(headingCita);

    // Formatear el Div de resumen
    const nomberCliente = document.createElement('P');
    nomberCliente.innerHTML = `<span>Nombre:</span> ${nombre}`;

    const fechaObj = new Date(fecha);
    const mes = fechaObj.getMonth();
    const dia = fechaObj.getDate() + 2;
    const year = fechaObj.getFullYear();

    const fechaUTC = new Date( Date.UTC(year, mes, dia) );

    const opciones = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    const fechaFormateada = fechaUTC.toLocaleDateString('es-MX', opciones);

    const fechaCliente = document.createElement('P');
    fechaCliente.innerHTML = `<span>Fecha:</span> ${fechaFormateada}`;

    const horaCliente = document.createElement('P');
    horaCliente.innerHTML = `<span>Hora:</span> ${hora}`;

    // Boton para crear una cita
    const botonReservar = document.createElement('BUTTON');
    botonReservar.classList.add('boton');
    botonReservar.textContent = "Reservar Cita";
    botonReservar.onclick = reservarCita;

    resumen.appendChild(nomberCliente);
    resumen.appendChild(fechaCliente);
    resumen.appendChild(horaCliente);

    resumen.appendChild(botonReservar);

}

async function reservarCita() {

    const { nombre, fecha, hora, id, servicios } = cita;
   
    // ForEach itera y map recopila coincidencias
    const idServicios = servicios.map( servicio => servicio.id )
    const datos = new FormData();
    datos.append('fecha', fecha);
    datos.append('hora', hora);
    datos.append('usuarioId', id);
    datos.append('servicios', idServicios);

    try {
        // Peticion hacia la API
        const url = `${location.origin}/api/citas`;
        const respuesta = await fetch(url, {
            method: 'POST',
            body: datos
        });

        const resultado = await respuesta.json();
        if(resultado.resultado) {
            Swal.fire({
            icon: "success",
            title: "Cita Creada",
            text: "Tu cita fue creada Correctamente",
            button: "OK"
            }).then( () => {
                window.location.reload();
            } );
        }; 
    } catch (error) {
        Swal.fire({
        icon: "error",
        title: "Error",
        text: "Hubo un error al guardar la cita"
        });
    }
    // console.log([...datos]);
}
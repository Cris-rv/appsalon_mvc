
<div class="campo">
    <label for="nombre">Nombre</label>
    <input 
        type="text"
        id="nombre"
        name="nombre"
        value="<?php echo s($servicio->nombre); ?>"
        placeholder="Nombre del servicio"
    />
</div>

<div class="campo">
    <label for="precio">Precio</label>
    <input 
        type="number"
        id="precio"
        name="precio"
        value="<?php echo s($servicio->precio); ?>"
        placeholder="Precio del servicio"
    />
</div>
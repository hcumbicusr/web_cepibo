<?php 
    require_once 'alumno.entidad.php';
    require_once 'alumno.model.php';

    // Logica
    $alm = new Alumno();
    $model = new AlumnoModel();
?>
    
<h1>Mi primer reporte</h1>
<p>Hemos creado nuestro reporte usando PHP y HTML :).</p>

<table>
    <thead>
        <tr>
            <th style="text-align:left;">Nombre</th>
            <th style="text-align:left;">Apellido</th>
            <th style="text-align:left;">Sexo</th>
            <th style="text-align:left;">Nacimiento</th>
        </tr>
    </thead>
    <?php foreach($model->Listar() as $r): ?>
        <tr>
            <td><?php echo $r->__GET('Nombre'); ?></td>
            <td><?php echo $r->__GET('Apellido'); ?></td>
            <td><?php echo $r->__GET('Sexo') == 1 ? 'H' : 'F'; ?></td>
            <td><?php echo $r->__GET('FechaNacimiento'); ?></td>
        </tr>
    <?php endforeach; ?>
</table>   
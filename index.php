<?php
 	include("conexion.php");
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title> Productos de tienda </title>
	<link rel="stylesheet" type="text/css" href="http://localhost/Emilio_proyecto/style.css">
</head>
<body>

	<form method="post">
		<div>
			<label for="id" class="etiqueta"> ID </label>
			<input type="text" name="id" id="" placeholder="Escriba el ID"   minlength="1" maxlength="25" size="18" class="dato">
		</div>
		<div>
			<label class="etiqueta" for="producto"> Producto </label>
			<input class="dato" type="text" name="descripcion" id="" placeholder="Escriba el producto" size="18">
		</div>
		<div>
			<label class="etiqueta" for="precio"> Precio </label>
			<input class="dato" type="text" name="precio" id="" placeholder="Escriba el precio"  minlength="1" maxlength="9" size="18">
		</div>
		<div>
			<label class="etiqueta" for="cantidad"> Cantidad </label>
			<input class="dato" type="text" name="cantidad" id="" placeholder="Escriba la cantidad" size="18">
		</div>
			<input class="button" type="submit" name="mostrar" value="Mostrar" >
			<input class="button" type="submit" name="insertar" value="insertar" >
			<input class="button" type="submit" name="eliminar" value="Eliminar" >
			<input class="button" type="submit" name="vaciar" value="Vaciar Todo" >
	</form>

<?php

if (isset($_POST['mostrar'])) 
{
?>

Datos de Tabla

<table class="tabla">
	<tr class="title">
		<td> ID </td>
		<td> Producto </td>
		<td> Precio </td>
		<td> Cantidad </td>
	</tr>

		<?php
		$sql="SELECT * from productos";
		$resultado=mysqli_query($conexion,$sql);
		while ($mostrar=mysqli_fetch_array($resultado)) 
		{
	?>
	<tr>
		 <td> 
			<?php
				echo $mostrar['id'];
				?>

		</td>
		<td> 
			<?php
				echo $mostrar['descripcion'];
			?>
		</td>
		<td> 
		  <?php
				echo $mostrar['precio'];
			?>
		</td>
		<td> 
		  <?php
				echo $mostrar['cantidad'];
			?>
		</td>
	</tr>

	<?php	
}
	?>

</table>


<?php
}


	
	if (isset($_POST['insertar'])) 
	{
		if (strlen($_POST['id']) >= 1) 
		{
		 $id= trim($_POST['id']);
		 $producto= trim($_POST['descripcion']);
		 $precio= trim($_POST['precio']);
		 $cantidad= trim($_POST['cantidad']);
		 $consulta="INSERT INTO productos(id,descripcion,precio,cantidad) VALUES ('$id','$producto','$precio', '$cantidad')";
		 $resultado=mysqli_query($conexion,$consulta);
		 if ($resultado) {
		 	echo "dato agregado con éxito";
		 }
		} 
		
	}


	if (isset($_POST['eliminar']))
	{
		if (strlen($_POST['id']) >= 1) 
		{
		 $id= trim($_POST['id']);
		 $consulta="DELETE FROM productos WHERE id='$id' LIMIT 1";
		 $resultado=mysqli_query($conexion,$consulta);
		 if ($resultado) {
		 	echo "dato Eliminado con éxito";
		 }
		} 
	}

	if (isset($_POST['vaciar'])) 
	{
		$consulta="TRUNCATE productos";
		 $resultado=mysqli_query($conexion,$consulta);
		 if ($resultado) {
		 	echo "Tabla vacia";
		 }
	}
	?> 
</body>
</html>
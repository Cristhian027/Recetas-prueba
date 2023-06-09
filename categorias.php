<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "1234";
$dbname = "recetas_prueba";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Error de conexión: " . mysqli_connect_error());
}

function generateSlug($conn, $titulo)
{
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $titulo)));
    $slug_exists = true;
    $suffix = 1;

    while ($slug_exists) {

        $check_sql = "SELECT COUNT(*) FROM recetasOne WHERE slug = '$slug'";
        $check_result = mysqli_query($conn, $check_sql);
        $slug_count = mysqli_fetch_row($check_result)[0];

        if ($slug_count > 0) {
            $slug = $slug . '-' . $suffix;
            $suffix++;
        } else {
            $slug_exists = false;
        }
    }

    return $slug;
}

// Envío del formulario para categorías
if (isset($_POST['submit_categoria'])) {
    $categoria_id = $_POST['categoria_id'];
    $categoria_titulo = $_POST['categoria_titulo'];
    $categoria_slug = generateSlug($conn, $categoria_titulo);

    if ($categoria_id == 0) {

        $sql_insert_categoria = "INSERT INTO categoriasOne (titulo, slug) VALUES ('$categoria_titulo', '$categoria_slug')";
        $result_insert_categoria = mysqli_query($conn, $sql_insert_categoria);

        if ($result_insert_categoria) {
            echo "La categoría se ha creado correctamente.";
        } else {
            echo "Error al crear la categoría: " . mysqli_error($conn);
        }
    } else {
        // Actualizar una categoría existente
        $sql_update_categoria = "UPDATE categoriasOne SET titulo='$categoria_titulo', slug='$categoria_slug' WHERE id=$categoria_id";
        $result_update_categoria = mysqli_query($conn, $sql_update_categoria);

        if ($result_update_categoria) {
            echo "La categoría se ha actualizado correctamente.";
        } else {
            echo "Error al actualizar la categoría: " . mysqli_error($conn);
        }
    }
}

// Procesar el envío del formulario para ocultar categorías
if (isset($_POST['submit_ocultar'])) {
    $categoria_id = $_POST['categoria_id'];
    $categoria_ocultar = $_POST['categoria_ocultar'];

    $sql_ocultar_categoria = "UPDATE categoriasOne SET ocultar=$categoria_ocultar WHERE id=$categoria_id";
    $result_ocultar_categoria = mysqli_query($conn, $sql_ocultar_categoria);

    if ($result_ocultar_categoria) {
        echo "La categoría se ha actualizado correctamente.";
    } else {
        echo "Error al actualizar la categoría: " . mysqli_error($conn);
    }
}

// Procesar el envío del formulario para borrar categorías
if (isset($_POST['submit_borrar'])) {
    $categoria_id = $_POST['categoria_id'];

    $sql_check_recetas = "SELECT COUNT(*) FROM recetasOne WHERE categoria=$categoria_id";
    $result_check_recetas = mysqli_query($conn, $sql_check_recetas);
    $recetas_count = mysqli_fetch_row($result_check_recetas)[0];

    if ($recetas_count > 0) {
        echo "No se puede borrar la categoría porque tiene recetas asociadas.";
    } else {
        $sql_borrar_categoria = "DELETE FROM categoriasOne WHERE id=$categoria_id";
        $result_borrar_categoria = mysqli_query($conn, $sql_borrar_categoria);

        if ($result_borrar_categoria) {
            echo "La categoría se ha borrado correctamente.";
        } else {
            echo "Error al borrar la categoría: " . mysqli_error($conn);
        }
    }
}

$sql_categorias = "SELECT * FROM categoriasOne";
$result_categorias = mysqli_query($conn, $sql_categorias);

mysqli_close($conn);
?>

<link rel="stylesheet" type="text/css" href="styles.css">

<div class="categorias">
    <div class="return" >
        <a class="return-admin" href="admin.php">Volver al admin</a>
        <h2>Gestión de categorías de reecetas</h2>
    </div>

    <h3>Crear/Editar Categoría</h3>
    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <input type="hidden" name="categoria_id" value="0"> 
        <label for="categoria_titulo">Título:</label>
        <input type="text" name="categoria_titulo" id="categoria_titulo" required><br><br>
        <button type="submit" name="submit_categoria">Guardar</button>
    </form>


    <h3>Ocultar Categoría</h3>
    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <label for="categoria_id_ocultar">Categoría:</label>
        <select name="categoria_id" id="categoria_id_ocultar" required>
            <option value="">Seleccionar categoría</option>
            <?php while ($row = mysqli_fetch_assoc($result_categorias)) { ?>
                <option value="<?php echo $row['id']; ?>"><?php echo $row['titulo']; ?></option>
            <?php } ?>
        </select>
        <label for="categoria_ocultar">Ocultar:</label>
        <select name="categoria_ocultar" id="categoria_ocultar" required>
            <option value="">Seleccionar opción</option>
            <option value="0">No</option>
            <option value="1">Sí</option>
        </select>
        <button type="submit" name="submit_ocultar">Guardar</button>
    </form>

    <h3>Borrar Categoría</h3>
    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <label for="categoria_id_borrar">Categoría:</label>
        <select name="categoria_id" id="categoria_id_borrar" required>
            <option value="">Seleccionar categoría</option>
            <?php mysqli_data_seek($result_categorias, 0);  ?>
            <?php while ($row = mysqli_fetch_assoc($result_categorias)) { ?>
                <option value="<?php echo $row['id']; ?>"><?php echo $row['titulo']; ?></option>
            <?php } ?>
        </select>
        <button type="submit" name="submit_borrar">Borrar</button>
    </form>
</div>
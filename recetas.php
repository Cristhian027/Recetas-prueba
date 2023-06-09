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

// Envío del formulario para crear recetas
if (isset($_POST['submit'])) {
    $titulo = $_POST['titulo'];
    $imagen = $_POST['imagen'];
    $tiempo_preparacion = $_POST['tiempo_preparacion'];
    $num_raciones = $_POST['num_raciones'];
    $ingredientes = $_POST['ingredientes'];
    $procedimiento = $_POST['procedimiento'];
    $categoria = $_POST['categoria'];

    $slug = generateSlug($conn, $titulo);

    $sql_recetas = "INSERT INTO recetasOne (titulo, imagen, tiempo_preparacion, num_raciones, ingredientes, procedimiento, categoria, slug) VALUES ('$titulo', '$imagen', '$tiempo_preparacion', '$num_raciones', '$ingredientes', '$procedimiento', '$categoria', '$slug')";
    $result_recetas = mysqli_query($conn, $sql_recetas);

    if ($result_recetas) {
        echo "Los datos se han insertado correctamente.";
    } else {
        echo "Error al insertar los datos en la tabla recetasOne: " . mysqli_error($conn);
    }
}

// Solicitud de mostrar recetas ocultas
if (isset($_GET['mostrar_ocultas'])) {
    $receta_id = $_GET['receta_id'];

    $sql_mostrar_receta = "UPDATE recetasOne SET oculto='0' WHERE id='$receta_id'";
    $result_mostrar_receta = mysqli_query($conn, $sql_mostrar_receta);

    if ($result_mostrar_receta) {
        echo "La receta se ha mostrado correctamente.";
    } else {
        echo "Error al mostrar la receta: " . mysqli_error($conn);
    }
}


$sql_recetas = "SELECT * FROM recetasOne";
$result_recetas = mysqli_query($conn, $sql_recetas);

$sql_categorias = "SELECT * FROM categoriasOne";
$result_categorias = mysqli_query($conn, $sql_categorias);


mysqli_close($conn);

?>

<link rel="stylesheet" type="text/css" href="styles.css">

<div class="recetas">

    <div class="return">
        <a class="return-admin" href="admin.php">Volver al admin</a>
        <h2>Gestionar Recetas</h2>
    </div>

    <h3>Crear Nueva Receta</h3>

    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <label for="titulo">Título:</label>
        <input type="text" name="titulo" id="titulo" required><br><br>

        <label for="imagen">Imagen:</label>
        <input type="text" name="imagen" id="imagen" required><br><br>

        <label for="tiempo_preparacion">Tiempo de Preparación:</label>
        <input type="text" name="tiempo_preparacion" id="tiempo_preparacion"><br><br>

        <label for="num_raciones">Número de Raciones:</label>
        <input type="number" name="num_raciones" id="num_raciones" required><br><br>

        <label for="ingredientes">Ingredientes:</label><br>
        <textarea name="ingredientes" id="ingredientes" rows="4" cols="50" required></textarea><br><br>

        <label for="procedimiento">Procedimiento:</label><br>
        <textarea name="procedimiento" id="procedimiento" rows="4" cols="50" required></textarea><br><br>

        <label for="categoria">Categoría:</label>
        <select name="categoria" id="categoria">
            <option>Elige una categoría</option>
            <option value="1">1</option>
            <option value="2">2</option>
        </select>
        <br><br>

        <button type="submit" name="submit">Crear Receta</button>

    </form>


    <h2>Listado de Recetas</h2>

    <button onclick="showRecetas()">Ver Recetas</button>
    <div id="recetasContainer" style="display: none;">
        <h2>Listado de Recetas</h2>
        <?php
        if (mysqli_num_rows($result_recetas) > 0) {
            while ($row = mysqli_fetch_assoc($result_recetas)) {
                echo "<h3>" . $row['titulo'] . "</h3>";
                echo "<img src='" . $row['imagen'] . "' alt='Imagen de la receta' style='max-width: 300px;'>";
                echo "<p>Tiempo de Preparación: " . $row['tiempo_preparacion'] . "</p>";
                echo "<p>Número de Raciones: " . $row['num_raciones'] . "</p>";
                echo "<p>Ingredientes: " . $row['ingredientes'] . "</p>";
                echo "<p>Procedimiento: " . $row['procedimiento'] . "</p>";
                echo "<p>Categoria: " . $row['categoria'] . "</p>";
                echo "<hr>";
            }
        } else {
            echo "No hay recetas disponibles.";
        }
        ?>
    </div>

</div>

<script>
    function showRecetas() {
        var recetasContainer = document.getElementById("recetasContainer");
        recetasContainer.style.display = "block";
    }
</script>
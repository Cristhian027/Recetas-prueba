CREATE DATABASE recetas_prueba;

USE recetas_prueba;

-- Crear la tabla recetasOne
CREATE TABLE recetasOne (
  id INT PRIMARY KEY AUTO_INCREMENT,
  titulo VARCHAR(250) NOT NULL UNIQUE,
  imagen VARCHAR(255) NOT NULL,
  tiempo_preparacion VARCHAR(50),
  num_raciones INT UNSIGNED,
  ingredientes TEXT,
  procedimiento TEXT,
  categoria INT,
  slug VARCHAR(250) NOT NULL DEFAULT '',
  FOREIGN KEY (categoria) REFERENCES categoriasOne(id)
);

-- Crear la tabla categoriasOne
CREATE TABLE categoriasOne (
  id INT PRIMARY KEY AUTO_INCREMENT,
  titulo VARCHAR(250) NOT NULL UNIQUE,
  slug VARCHAR(250) NOT NULL DEFAULT ''
);


SELECT * FROM recetasOne;
SELECT * FROM categoriasOne;


ALTER TABLE recetasOne DROP FOREIGN KEY recetasone_ibfk_1;
DROP TABLE categoriasOne;

DROP TABLE recetasOne;







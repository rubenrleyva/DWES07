# DWES07
Desarrollo Web Entorno Servidor: Tarea 7
Se trata de desarrollar una aplicación híbrida para la gestión de recorridos de carreras ciclistas utilizando las nuevas APIs de Google, POO, Ajax, PHP y JavaScript. Para ello vamos a hacer uso de la base de datos ciclistas7.sql y seguiremos las siguientes premisas:

Las carreras están formadas por un título, una fecha, un código de carrera, y por al menos 2 puntos de paso: punto de inicio y punto de fin.
Los puntos de paso, además de identificarse por un código que genera la BD de forma automática, estarán asociados a una carrera, y tendrán las coordenadas de posicionamiento (longitud, latitud y altura). Las coordenadas de longitud y latitud podrás obtenerlas a través de Google Geocoding. Utilizando el servicio webGoogle Elevation, incluido dentro de Google Maps, obtendrás también la altitud.
La aplicación tendrá la siguiente estructura:

Una única página donde se visualizará la tabla con todas las carreras ciclistas registradas. Dicha tabla se ordenará por la columna 1 y estará compuesta por las siguiente columnas:
Columna 1: Fecha de la carrera.
Columna 2: Nombre de la carrera.
Columna 3: Punto de inicio/salida de la carrera.
Columna 4: Punto de fin/llegada de la carrera.
Columna 5: Botón con el texto “Ver mapa”. Al clicarlo se visualizará el mapa de la carrera en GoogleMaps, con los puntos de salida y llegada, y todos los puntos de paso asociados a dicha carrera.
Columna 6: Botón con el texto “Editar carrera”, que nos llevará al formulario de edición de la carrera.
Formulario “Edición carrera”:

En la misma u otra página (lo dejo a vuestra elección), mostraréis para su edición la fecha de la carrera, su nombre y el listado de todos los puntos de paso (incluídos los puntos de inicio y fin, que han de mostrarse diferenciados). Dichos puntos de paso podrán modificarse y eliminarse (si permitís la eliminación de los puntos de inicio y fin (opcional), deberéis substituírlos por otro punto).
Al final del formulario de edición de carrera deberá aparecer un botón de “Agregar punto de paso”, que os llevará a otro formulario para la creación de un nuevo punto de paso (podéis reutilizar dicho formulario para la edición de un punto de paso).
Formulario “Nuevo punto de paso”:

Habrá 4 input text:
Dirección: Introduciremos la dirección sobre la que queramos detectar sus coordenadas.
Latitud.
Longitud.
Altitud.
Las coordenadas podrán generarse automáticamente a partir de la dirección pulsando el botón “Obtener coordenadas” o simplemente escribiendo los valores.
El botón “Agregar punto de paso” sólo funcionará cuando todas las coordenadas estén generadas o escritas correctamente.

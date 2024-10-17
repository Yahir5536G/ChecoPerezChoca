<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulación de Carros en la Cancha</title>
    <link rel="stylesheet" type="text/css" href="http://localhost/ChechoPerezChoca/style.css">

</head>
<body>
    <h2 style="text-align: center;">Simulación de Carros en la Cancha</h2>
    <canvas id="juegoCanvas" width="800" height="500"></canvas>
    <button id="startBtn">Iniciar</button>
    <audio src="music.mp3" loop id="music"></audio>

    <script>
        const imagenCarro1 = new Image();
        imagenCarro1.src = 'images/rojo.png';

        const imagenCarro2 = new Image();
        imagenCarro2.src = 'images/azul.png';

        const canvas = document.getElementById('juegoCanvas');
        const ctx = canvas.getContext('2d');

        
        let carro1 = {
            x: 50, // Posición inicial dentro de la cancha
            y: 250, // Posición inicial dentro de la cancha
            ancho: 100,
            alto: 50,
            velocidad: 0,
            angulo: 0,
            color: 'red',
            posicionAnterior: { x: 100, y: 250 }, // Almacenar posición anterior
        };

        // Parámetros del segundo carro (azul)
        let carro2 = {
            x: 750, // Posición inicial dentro de la cancha
            y: 250, // Posición inicial dentro de la cancha
            ancho: 100,
            alto: 50,
            velocidad: 0,
            angulo: Math.PI,
            color: 'blue',
            posicionAnterior: { x: 300, y: 250 }, // Almacenar posición anterior
        };

        // Parámetros de la pelota
        let pelota = {
            x: canvas.width / 2,
            y: canvas.height / 2,
            radio: 15,
            velocidadX: 0,
            velocidadY: 0,
            color: 'yellow',
        };

        // Variables para controlar la aceleración y giros
        let acelerando1 = false;
        let retrocediendo1 = false;
        let girandoIzquierda1 = false;
        let girandoDerecha1 = false;
        let tirando1 = false;
        let tirando2 = false;

        let acelerando2 = false;
        let retrocediendo2 = false;
        let girandoIzquierda2 = false;
        let girandoDerecha2 = false;


        // Variables para los goles
        let goles1 = 0;
        let goles2 = 0;

        function dibujarCarro(carro, imagen) {
            ctx.save();
            ctx.translate(carro.x, carro.y);
            ctx.rotate(carro.angulo);
            ctx.drawImage(imagen, -carro.ancho / 2, -carro.alto / 2, carro.ancho, carro.alto);
            ctx.restore();
        }

        function dibujarPelota() {
            ctx.beginPath();
            ctx.arc(pelota.x, pelota.y, pelota.radio, 0, Math.PI * 2);
            ctx.fillStyle = pelota.color;
             ctx.filter = 'drop-shadow(0 0 20px black)';
            ctx.fill();
            ctx.closePath();

        }

        function dibujarCancha() {
                // Fondo con patrón de césped
    const patronCesped = ctx.createLinearGradient(0, 0, canvas.width, 0);
    patronCesped.addColorStop(0, '#4CAF50');
    patronCesped.addColorStop(0.25, '#388E3C');
    patronCesped.addColorStop(0.5, '#4CAF50');
    patronCesped.addColorStop(0.75, '#388E3C');
    patronCesped.addColorStop(1, '#4CAF50');
    ctx.fillStyle = patronCesped;
    ctx.fillRect(0, 0, canvas.width, canvas.height);

    // Dibujar las porterías
   // Configuración del estilo para las porterías
    ctx.strokeStyle = 'white';
    ctx.lineWidth = 4;
    ctx.fillStyle = 'rgba(255, 255, 255, 0.7)'; // Color blanco semitransparente para simular la red

    // Portería izquierda
    ctx.fillRect(0, canvas.height / 2 - 40, 10, 80); // Relleno semitransparente
    ctx.strokeRect(0, canvas.height / 2 - 40, 10, 80); // Borde blanco

    // Portería derecha
    ctx.fillRect(canvas.width - 10, canvas.height / 2 - 40, 10, 80); // Relleno semitransparente
    ctx.strokeRect(canvas.width - 10, canvas.height / 2 - 40, 10, 80); // Borde blanco

    // Dibujar la red dentro de las porterías (líneas diagonales)
    ctx.strokeStyle = 'white';
    ctx.lineWidth = 1;
    for (let i = 0; i < 80; i += 10) {
        // Líneas diagonales en la portería izquierda
        ctx.beginPath();
        ctx.moveTo(0, canvas.height / 2 - 40 + i);
        ctx.lineTo(10, canvas.height / 2 - 40 + i + 10);
        ctx.stroke();

        // Líneas diagonales en la portería derecha
        ctx.beginPath();
        ctx.moveTo(canvas.width - 10, canvas.height / 2 - 40 + i);
        ctx.lineTo(canvas.width, canvas.height / 2 - 40 + i + 10);
        ctx.stroke();
    }

    // Dibujar líneas de la cancha
    ctx.strokeStyle = 'white';
    ctx.lineWidth = 3;

    // Línea central
    ctx.beginPath();
    ctx.moveTo(canvas.width / 2, 0);
    ctx.lineTo(canvas.width / 2, canvas.height);
    ctx.stroke();

    // Círculo central
    ctx.beginPath();
    ctx.arc(canvas.width / 2, canvas.height / 2, 70, 0, Math.PI * 2);
    ctx.stroke();
        }

        function actualizarCarro(carro, acelerar, retroceder, girarIzquierda, girarDerecha, tirando) {
            // Guardar la posición anterior
    carro.posicionAnterior.x = carro.x;
    carro.posicionAnterior.y = carro.y;

    // Control de velocidad
    const aceleracion = 0.8; // Aumento de la aceleración
    if (acelerar) {
        carro.velocidad += aceleracion; // Acelerar
    }
    if (retroceder) {
        carro.velocidad -= aceleracion; // Retroceder
    }
    carro.velocidad *= 0.9; // Fricción ajustada para ser más lenta
    pelota.velocidadX *= 0.997;
    pelota.velocidadY *= 0.997;

    // Limitar velocidad máxima
    carro.velocidad = Math.min(carro.velocidad, 10); // Velocidad máxima

    // Control de dirección
    if (girarIzquierda) {
        carro.angulo -= 0.11; // Girar a la izquierda
    }
    if (girarDerecha) {
        carro.angulo += 0.11; // Girar a la derecha
    }




    // Actualizar posición
    carro.x += Math.cos(carro.angulo) * carro.velocidad;
    carro.y += Math.sin(carro.angulo) * carro.velocidad;

    // Limitar movimiento dentro de la cancha
    if (carro.x < 25) carro.x = 25; // Limitar a la izquierda
    if (carro.x > canvas.width - 25) carro.x = canvas.width - 25; // Limitar a la derecha
    if (carro.y < 25) carro.y = 25; // Limitar arriba
    if (carro.y > canvas.height - 25) carro.y = canvas.height - 25; // Limitar abajo
        }

        function verificarColision(carro1, carro2) {
            const margen = 2.2;
            return !(carro1.x + carro1.ancho / margen < carro2.x - carro2.ancho / margen ||
                     carro1.x - carro1.ancho / margen > carro2.x + carro2.ancho / margen ||
                     carro1.y + carro1.alto / margen < carro2.y - carro2.alto / margen ||
                     carro1.y - carro1.alto / margen > carro2.y + carro2.alto / margen);
        }

        function manejarColision(carro1, carro2) {
            // Regresar a la posición anterior después de la colisión
            // Calcular la diferencia en posiciones
    const dx = carro2.x - carro1.x;
    const dy = carro2.y - carro1.y;
    const distancia = Math.sqrt(dx * dx + dy * dy);

    // Si hay colisión (distancia menor que la suma de los radios)
    if (distancia < carro1.ancho / 2.2 + carro2.ancho / 2.2) {
        // Calcular el ángulo de colisión
        const angulo = Math.atan2(dy, dx);

        // Separar los carros para evitar que se queden pegados
        const overlap = (carro1.ancho / 2.2 + carro2.ancho / 2.2) - distancia;
        carro1.x -= Math.cos(angulo) * overlap / 2.2;
        carro1.y -= Math.sin(angulo) * overlap / 2.2;
        carro2.x += Math.cos(angulo) * overlap / 2.2;
        carro2.y += Math.sin(angulo) * overlap / 2.2;

        // Calcular la nueva velocidad para el rebote
        const rebote = 2; // Factor de rebote para ajustar la fuerza
        const velocidad1 = Math.sqrt(carro1.velocidad * carro1.velocidad);
        const velocidad2 = Math.sqrt(carro2.velocidad * carro2.velocidad);

        // Actualizar la velocidad de los carros
        carro1.velocidadX = -Math.cos(angulo) * velocidad2 * rebote;
        carro1.velocidadY = -Math.sin(angulo) * velocidad2 * rebote;
        carro2.velocidadX = Math.cos(angulo) * velocidad1 * rebote;
        carro2.velocidadY = Math.sin(angulo) * velocidad1 * rebote;
    }
        }

        function verificarColisionPelota(carro) {
            const dx = pelota.x - carro.x;
            const dy = pelota.y - carro.y;
            const distancia = Math.sqrt(dx * dx + dy * dy);

            return distancia < pelota.radio + (carro.ancho / 2 - 8); // Colisión entre el carro y la pelota
        }

        function manejarColisionPelota(carro, tirando) {
            const dx = pelota.x - carro.x;
            const dy = pelota.y - carro.y;
            const angulo = Math.atan2(dy, dx);
    
            // Calcular el rebote de la pelota
            rebote = 0;
            if (tirando) {
                rebote = 15;
            } else {
                rebote = 5;
            }
            // Factor de rebote para mayor efecto
            pelota.velocidadX = Math.cos(angulo) * rebote;
            pelota.velocidadY = Math.sin(angulo) * rebote;
            
            // Ajustar la posición de la pelota para evitar que se quede dentro del carro
            const distanciaMinima = pelota.radio + carro.ancho / 2 - 8;
            const distanciaActual = Math.sqrt(dx * dx + dy * dy);
            const diferencia = distanciaMinima - distanciaActual;
            
            pelota.x += Math.cos(angulo) * diferencia;
            pelota.y += Math.sin(angulo) * diferencia;
}

        function verificarGol() {
            // Gol del carro 1 (izquierda)
            if (pelota.x - pelota.radio <= 10 && (pelota.y >= canvas.height / 2 - 40 && pelota.y <= canvas.height / 2 + 40)) {
                goles1++;
                alert(`¡Gol! El carro rojo ha anotado. Goles: ${goles1} - ${goles2}`);
                resetPelota();
            }

            // Gol del carro 2 (derecha)
            if (pelota.x + pelota.radio >= canvas.width - 10 && (pelota.y >= canvas.height / 2 - 40 && pelota.y <= canvas.height / 2 + 40)) {
                goles2++;
                alert(`¡Gol! El carro azul ha anotado. Goles: ${goles1} - ${goles2}`);
                resetPelota();
            }
        }

        function resetPelota() {
            pelota.x = canvas.width / 2;
            pelota.y = canvas.height / 2;
            pelota.velocidadX = 0;
            pelota.velocidadY = 0;

            carro1.x = 50;
            carro1.y = 250;
            carro1.angulo = 0;
            carro1.velocidad = 0;

            carro2.x = 750;
            carro2.y = 250;
            carro2.angulo = Math.PI;
            carro2.velocidad = 0;
        }

       
            
        

        function animar() {
            ctx.clearRect(0, 0, canvas.width, canvas.height); // Limpiar canvas
            dibujarCancha(); // Dibujar la cancha
            dibujarCarro(carro1, imagenCarro1); // Dibujar el carro rojo con su imagen
            dibujarCarro(carro2, imagenCarro2);
            dibujarPelota(); // Dibujar la pelota

            // Actualizar la posición de los carros
            actualizarCarro(carro1, acelerando1, retrocediendo1, girandoIzquierda1, girandoDerecha1, tirando1);
            actualizarCarro(carro2, acelerando2, retrocediendo2, girandoIzquierda2, girandoDerecha2, tirando2);

            // Verificar colisiones entre los carros
            if (verificarColision(carro1, carro2)) {
                manejarColision(carro1, carro2);
            }

            // Verificar colisión con la pelota
            if (verificarColisionPelota(carro1, tirando1)) {
                manejarColisionPelota(carro1, tirando1);
            }
            if (verificarColisionPelota(carro2, tirando2)) {
                manejarColisionPelota(carro2, tirando2);
            }

            // Actualizar posición de la pelota
            pelota.x += pelota.velocidadX;
            pelota.y += pelota.velocidadY;

            // Limitar la pelota dentro de la cancha
            if (pelota.x < pelota.radio) {
                pelota.x = pelota.radio; // Rebotar en la pared izquierda
                pelota.velocidadX *= -1; // Invertir la velocidad
            }
            if (pelota.x > canvas.width - pelota.radio) {
                pelota.x = canvas.width - pelota.radio; // Rebotar en la pared derecha
                pelota.velocidadX *= -1; // Invertir la velocidad
            }
            if (pelota.y < pelota.radio) {
                pelota.y = pelota.radio; // Rebotar en la parte superior
                pelota.velocidadY *= -1; // Invertir la velocidad
            }
            if (pelota.y > canvas.height - pelota.radio) {
                pelota.y = canvas.height - pelota.radio; // Rebotar en la parte inferior
                pelota.velocidadY *= -1; // Invertir la velocidad
            }

            // Verificar goles
            verificarGol();

            requestAnimationFrame(animar); // Solicitar el siguiente frame
        }

        // Control de eventos de teclado
        document.addEventListener('keydown', (event) => {
            if (event.key === 'w') acelerando1 = true;
            if (event.key === 's') retrocediendo1 = true;
            if (event.key === 'a') girandoIzquierda1 = true;
            if (event.key === 'd') girandoDerecha1 = true;
            if (event.key === 'f') tirando1 = true;

            if (event.key === 'ArrowUp') acelerando2 = true;
            if (event.key === 'ArrowDown') retrocediendo2 = true;
            if (event.key === 'ArrowLeft') girandoIzquierda2 = true;
            if (event.key === 'ArrowRight') girandoDerecha2 = true;
            if (event.key === 'l') tirando2 = true;
        });

        document.addEventListener('keyup', (event) => {
            if (event.key === 'w') acelerando1 = false;
            if (event.key === 's') retrocediendo1 = false;
            if (event.key === 'a') girandoIzquierda1 = false;
            if (event.key === 'd') girandoDerecha1 = false;
            if (event.key === 'f') tirando1 = false;

            if (event.key === 'ArrowUp') acelerando2 = false;
            if (event.key === 'ArrowDown') retrocediendo2 = false;
            if (event.key === 'ArrowLeft') girandoIzquierda2 = false;
            if (event.key === 'ArrowRight') girandoDerecha2 = false;
            if (event.key === 'l') tirando2 = false;
        });

        // Iniciar la animación
        document.getElementById('startBtn').addEventListener('click', animar);
        document.getElementById("startBtn").addEventListener("click", function() {
            var audio = document.getElementById("music");
            if (audio.paused) {
                audio.play();

            } 
        });
        
    </script>
</body>
</html>

# Sistema de Gestión de Reservas para Eventos

Proyecto práctico para demostrar habilidades en desarrollo web, soporte técnico y mejora continua.

## Instalación
1. Instala XAMPP y clona este repositorio en `htdocs`.
2. Crea la base de datos `event_reservations` y ejecuta el SQL en `database.sql`.
3. Inicia Apache y MySQL en XAMPP.
4. Accede a `http://localhost/event-reservations`.

## Tecnologías
- PHP: Backend y lógica de reservas.
- MySQL: Almacenamiento de datos.
- Bootstrap: Diseño responsivo.
- JavaScript: Validaciones frontend.

## Decisiones técnicas
- Uso de transacciones en `reserve.php` para evitar sobre-reservas.
- Índice en `events.reserved` para optimizar consultas.

## Mejora continua
- Problema: Consulta lenta en `index.php`.
- Solución: Índice en `reserved`.
- Resultado: Tiempo reducido de X a Y segundos.
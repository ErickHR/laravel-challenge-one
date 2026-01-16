# TEST
http://challenge.erick-rivas.com/


# PREGUNTAS

Optimización de memoria: ¿Cómo manejarías un reporte con millones de registros sin agotar la memoria del servidor?
- Considero que si se usa laravel se deberia evitar usar eloquent, ya que consume memoria.
- Se tendria que usar jobs para generar el reporte en segundo plano, si se descarga instataneamente generaria una demora o error de timeout
- Hacer el proceso por partes(paginacion)
- Estructura en streming, se podria considerar usar archivo CSV
  
Optimización de consultas: ¿Cómo estructurarías las consultas a la base de datos para evitar problemas de rendimiento?
- No llamar todas las columnas innecesarias
- Usar indices

Escalabilidad: ¿Qué estrategias implementarías pensando en que el volumen de datos puede multiplicarse en el futuro?
- Hacer una BD esclavo o seguidor, que sea solo lectura.
- Dividir las tablas, por año o mes
- Crear una tabla que contengan todos los datos del reporte asi evitar los joins.

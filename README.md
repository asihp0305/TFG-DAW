# MVP-Malpartida Dental
<p align="center">
  <img src="Imagenes/logo_minimalista.png" alt="Logo de Malpartida Dental" width="160">
</p>

<h1 align="center">Malpartida Dental</h1>

<p align="center">
  Web pública y software interno de gestión de citas y pacientes para una clínica dental.<br>
  Trabajo de Fin de Grado del ciclo de Desarrollo de Aplicaciones Web (DAW).
</p>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-777BB4?logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/MySQL-4479A1?logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/jQuery-0769AD?logo=jquery&logoColor=white" alt="jQuery">
  <img src="https://img.shields.io/badge/estado-MVP-orange" alt="Estado: MVP">
</p>

## Sobre el proyecto

Malpartida Dental es un MVP pensado para una clínica de Malpartida de Cáceres. Reúne en una sola aplicación la web de presentación de la clínica y dos portales privados: uno para pacientes y otro para el personal.

El recorrido principal es este: el personal da de alta al paciente, el sistema le genera las credenciales y se las envía por correo, y a partir de ahí el paciente consulta sus citas e historial desde su portal. Las citas las asigna siempre la clínica, teniendo en cuenta la duración del tratamiento, el profesional y las salas disponibles.

## Puntos técnicos destacados

- **Reserva por bloques:** las horas disponibles se calculan según la duración del tratamiento, el profesional y la ocupación de las salas ([`clases/clase_citas.php`](clases/clase_citas.php)).
- **Acceso por roles:** la misma entrada carga un portal distinto para paciente, trabajador o administrador ([`perfil_usuario.php`](perfil_usuario.php)).
- **Altas automáticas:** se genera un usuario único y una contraseña aleatoria, que se guarda con hash y se envía por correo ([`clases/clase_user.php`](clases/clase_user.php)).
- **Formulario en cascada:** cada paso de la asignación de citas consulta al servidor por AJAX y desbloquea el siguiente ([`vistas/dar_cita.php`](vistas/dar_cita.php)).

## Funcionalidades

**Web pública**

- Presentación de la clínica, instalaciones, contacto y horario.
- Inicio de sesión en una ventana modal.

**Portal del paciente**

- Consulta de citas pendientes y cancelación.
- Historial médico con los tratamientos completados y sus notas.
- Sección de personas a cargo para tutores legales.
- Cambio de contraseña.

**Portal del empleado**

- Agenda diaria con navegación por fechas y notas médicas en cada cita.
- Alta de pacientes, con usuario y contraseña generados automáticamente.
- Asignación de citas mediante un formulario guiado:
  1. Busca al paciente por DNI.
  2. Filtra los profesionales según el tratamiento.
  3. Calcula las horas disponibles.
  4. Muestra los auxiliares libres a esa hora.

**Administración**

- Todo lo anterior, más el alta de empleados.

### Cálculo de disponibilidad

El horario se divide en bloques de 30 minutos. Para ofrecer una hora, `horas_libres()` comprueba que haya suficientes bloques consecutivos para la duración del tratamiento y descarta:

- los bloques en los que el profesional ya tiene una cita;
- los bloques en los que las dos salas de la clínica están ocupadas;
- las horas ya pasadas del día actual;
- los fines de semana.

## Tecnologías

| Capa | Tecnología |
| --- | --- |
| Backend | PHP (sin framework), con sesiones y `mysqli` |
| Base de datos | MySQL |
| Frontend | HTML, CSS y JavaScript con jQuery 3.7.1 |
| Comunicación | AJAX: las vistas se cargan dentro de cada portal sin recargar la página |
| Seguridad | Consultas preparadas, contraseñas con `password_hash()` y saneado de entradas con `filter_input()` |

## Estructura

La aplicación separa las vistas, los controladores y las clases de acceso a datos:

```text
.
├── index.php              # Web pública
├── perfil_usuario.php     # Carga el portal según el rol de la sesión
├── portales/              # Portal del paciente y del empleado
├── vistas/                # Fragmentos que se cargan por AJAX
├── controladores/         # Reciben las peticiones POST (parámetro opt)
├── clases/                # Lógica de usuarios y citas
├── sec/                   # Login, control de sesión y cierre de sesión
├── css/                   # Estilos de cada zona
└── Imagenes/              # Logo y fotografías
```

## Modelo de datos

| Tabla | Contenido | Campos principales |
| --- | --- | --- |
| `usuarios` | Credenciales y rol | `usr`, `password`, `email`, `rol`, `creator_id` |
| `pacientes` | Ficha del paciente | `usuario_id`, `nombre`, `dni`, `telefono`, `fecha_nacimiento`, `tutor_legal`, `tutor_id` |
| `trabajadores` | Ficha del empleado | `usuario_id`, `nombre`, `dni`, `especialidad` |
| `servicios` | Catálogo de tratamientos | `nombre`, `duracion_minutos` |
| `citas` | Agenda | `paciente_id`, `trabajador_id`, `auxiliar_id`, `tutor_id`, `servicio_id`, `fecha`, `hora_inicio`, `hora_fin`, `estado`, `notas` |

Los roles son `paciente`, `trabajador` y `admin`. Las citas pueden estar en estado `pendiente`, `cancelada` o `completada`.

## Instalación

<details>
<summary>Ver los pasos para ejecutarlo en local</summary>

<br>

> [!IMPORTANT]
> El repositorio no incluye la base de datos ni el envío de correo porque contienen configuración privada (`BBDD/` y `enviar_correo.php` están en `.gitignore`). Hay que crearlos antes de arrancar la aplicación.

**Requisitos:** PHP 7.3 o superior con `mysqli` y `mysqlnd`, y un servidor MySQL.

1. Clona el repositorio:

   ```bash
   git clone https://github.com/asihp0305/TFG-DAW.git
   cd TFG-DAW
   ```

2. Crea las tablas del [modelo de datos](#modelo-de-datos) en una base de datos MySQL.

3. Crea `BBDD/BBDD.php` con la conexión. La aplicación espera una variable `$db`:

   ```php
   <?php
   $db = new mysqli('localhost', 'usuario', 'contraseña', 'nombre_bbdd');
   $db->set_charset('utf8mb4');
   ```

4. Crea `sec/enviar_correo.php` con esta función, que debe devolver `true` si el envío funciona o un texto con el error si falla:

   ```php
   function enviarCredencialesPaciente($email, $nombre, $usuario, $password) { /* ... */ }
   ```

5. Añade los datos iniciales:
   - Los tratamientos en `servicios`. El profesional se elige por el id del servicio: `1` para auxiliares, `4` para pediatras y cualquier otro para cirujanos.
   - Un usuario con rol `admin` en `usuarios`, con su contraseña generada con `password_hash()`, y su ficha en `trabajadores`.

6. Arranca la aplicación desde la raíz del proyecto. Tiene que servirse en la raíz del dominio, no en una subcarpeta:

   ```bash
   php -S localhost:8000
   ```

</details>

## Estado del proyecto

Este proyecto es un MVP en desarrollo y **no está preparado para usarse con datos reales de pacientes**. Antes haría falta reforzar el control de permisos en los controladores, añadir protección CSRF y cumplir el RGPD, ya que la aplicación trata datos de salud.

Trabajo pendiente:

- [ ] Guardar bien la hora de fin de las citas, de la que depende el bloqueo de horas ocupadas.
- [ ] Aplicar el límite de 24 horas para cancelar una cita.
- [ ] Mostrar a cada profesional su propia agenda y confirmar correctamente el guardado de notas.
- [ ] Marcar citas como completadas desde la aplicación para que aparezcan en el historial.
- [ ] Reparar los filtros del historial.
- [ ] Completar la gestión de menores: vincularlos a su tutor y permitir cancelar sus citas.
- [ ] Permitir que los pacientes pidan cita desde su portal.
- [ ] Incluir un script SQL para crear la base de datos.
- [ ] Adaptar la web pública y el portal del paciente a móviles.

## Autor

**Asier Hernández Parra**: [@asihp0305](https://github.com/asihp0305)

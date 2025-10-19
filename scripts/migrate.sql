-- MySQL migration generated from DBML specification
SET NAMES utf8mb4;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;

DROP TABLE IF EXISTS administrador;
DROP TABLE IF EXISTS comentarios;
DROP TABLE IF EXISTS tipo_comentario;
DROP TABLE IF EXISTS movimientos_tokens;
DROP TABLE IF EXISTS promociones;
DROP TABLE IF EXISTS suscripciones_plus;
DROP TABLE IF EXISTS compras;
DROP TABLE IF EXISTS estado_pago;
DROP TABLE IF EXISTS metodo_pago;
DROP TABLE IF EXISTS cancelaciones_cita;
DROP TABLE IF EXISTS citas;
DROP TABLE IF EXISTS estado_cita;
DROP TABLE IF EXISTS dia_horario;
DROP TABLE IF EXISTS dias_semana;
DROP TABLE IF EXISTS conjunto_horario;
DROP TABLE IF EXISTS personal_servicio;
DROP TABLE IF EXISTS servicios;
DROP TABLE IF EXISTS personal;
DROP TABLE IF EXISTS negocios;
DROP TABLE IF EXISTS usuarios;
DROP TABLE IF EXISTS roles;
DROP TABLE IF EXISTS estadisticas;

CREATE TABLE roles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE usuarios (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre_completo VARCHAR(120) NOT NULL,
    correo VARCHAR(190) UNIQUE,
    telefono VARCHAR(20) UNIQUE,
    usuario VARCHAR(50) UNIQUE,
    hash_contrasena VARCHAR(255) NOT NULL,
    rol_id BIGINT UNSIGNED NOT NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_usuarios_roles FOREIGN KEY (rol_id) REFERENCES roles(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE negocios (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(140) NOT NULL,
    tokens INT NOT NULL DEFAULT 0,
    direccion VARCHAR(240),
    latitud DECIMAL(10,7),
    longitud DECIMAL(10,7),
    activo TINYINT(1) NOT NULL DEFAULT 0,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_negocios_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE personal (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    negocio_id BIGINT UNSIGNED NOT NULL,
    usuario_id BIGINT UNSIGNED,
    propietario TINYINT(1) NOT NULL DEFAULT 0,
    nombre_publico VARCHAR(120) NOT NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_personal_negocio_activo (negocio_id, activo),
    INDEX idx_personal_usuario (usuario_id),
    CONSTRAINT fk_personal_negocio FOREIGN KEY (negocio_id) REFERENCES negocios(id),
    CONSTRAINT fk_personal_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE servicios (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    negocio_id BIGINT UNSIGNED NOT NULL,
    nombre VARCHAR(120) NOT NULL,
    duracion_min INT NOT NULL,
    precio_cop DECIMAL(12,2) NOT NULL,
    costo_tokens INT NOT NULL DEFAULT 1,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    orden INT NOT NULL DEFAULT 0,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_servicios_negocio_nombre (negocio_id, nombre),
    INDEX idx_servicios_negocio_activo (negocio_id, activo),
    INDEX idx_servicios_negocio_orden (negocio_id, orden),
    CONSTRAINT fk_servicios_negocio FOREIGN KEY (negocio_id) REFERENCES negocios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE personal_servicio (
    personal_id BIGINT UNSIGNED NOT NULL,
    servicio_id BIGINT UNSIGNED NOT NULL,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (personal_id, servicio_id),
    CONSTRAINT fk_personal_servicio_personal FOREIGN KEY (personal_id) REFERENCES personal(id),
    CONSTRAINT fk_personal_servicio_servicio FOREIGN KEY (servicio_id) REFERENCES servicios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE conjunto_horario (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    negocio_id BIGINT UNSIGNED NOT NULL,
    personal_id BIGINT UNSIGNED NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    creado_por BIGINT UNSIGNED,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_conjunto_personal_rango (personal_id, fecha_inicio, fecha_fin),
    CONSTRAINT fk_conjunto_negocio FOREIGN KEY (negocio_id) REFERENCES negocios(id),
    CONSTRAINT fk_conjunto_personal FOREIGN KEY (personal_id) REFERENCES personal(id),
    CONSTRAINT fk_conjunto_creado_por FOREIGN KEY (creado_por) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE dias_semana (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE dia_horario (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    conjunto_horario_id BIGINT UNSIGNED NOT NULL,
    dia_id BIGINT UNSIGNED NOT NULL,
    trabaja TINYINT(1) NOT NULL DEFAULT 0,
    hora_inicio TIME,
    hora_fin TIME,
    almuerzo_inicio TIME,
    almuerzo_fin TIME,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_dia_conjunto (conjunto_horario_id, dia_id),
    CONSTRAINT fk_dia_conjunto FOREIGN KEY (conjunto_horario_id) REFERENCES conjunto_horario(id) ON DELETE CASCADE,
    CONSTRAINT fk_dia_semana FOREIGN KEY (dia_id) REFERENCES dias_semana(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE estado_cita (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE citas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    negocio_id BIGINT UNSIGNED NOT NULL,
    personal_id BIGINT UNSIGNED NOT NULL,
    servicio_id BIGINT UNSIGNED NOT NULL,
    usuario_cliente_id BIGINT UNSIGNED,
    nombre_invitado VARCHAR(140),
    fecha DATE NOT NULL,
    inicia_en TIMESTAMP NOT NULL,
    termina_en TIMESTAMP NOT NULL,
    estado_id BIGINT UNSIGNED NOT NULL,
    notas TEXT,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_citas_negocio_inicio (negocio_id, inicia_en),
    INDEX idx_citas_personal_inicio (personal_id, inicia_en),
    INDEX idx_citas_usuario (usuario_cliente_id),
    INDEX idx_citas_negocio_personal_inicio (negocio_id, personal_id, inicia_en),
    CONSTRAINT fk_citas_negocio FOREIGN KEY (negocio_id) REFERENCES negocios(id),
    CONSTRAINT fk_citas_personal FOREIGN KEY (personal_id) REFERENCES personal(id),
    CONSTRAINT fk_citas_servicio FOREIGN KEY (servicio_id) REFERENCES servicios(id),
    CONSTRAINT fk_citas_usuario FOREIGN KEY (usuario_cliente_id) REFERENCES usuarios(id),
    CONSTRAINT fk_citas_estado FOREIGN KEY (estado_id) REFERENCES estado_cita(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE cancelaciones_cita (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cita_id BIGINT UNSIGNED NOT NULL,
    usuario_id_cancelo BIGINT UNSIGNED NOT NULL,
    motivo VARCHAR(240),
    cancelado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_cancelaciones_cita FOREIGN KEY (cita_id) REFERENCES citas(id) ON DELETE CASCADE,
    CONSTRAINT fk_cancelaciones_usuario FOREIGN KEY (usuario_id_cancelo) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE metodo_pago (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE estado_pago (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE compras (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    negocio_id BIGINT UNSIGNED NOT NULL,
    metodo_id BIGINT UNSIGNED NOT NULL,
    estado_id BIGINT UNSIGNED NOT NULL,
    tokens INT,
    monto_cop DECIMAL(12,2) NOT NULL,
    ref_externa VARCHAR(120),
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_compras_negocio FOREIGN KEY (negocio_id) REFERENCES negocios(id),
    CONSTRAINT fk_compras_metodo FOREIGN KEY (metodo_id) REFERENCES metodo_pago(id),
    CONSTRAINT fk_compras_estado FOREIGN KEY (estado_id) REFERENCES estado_pago(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE suscripciones_plus (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    negocio_id BIGINT UNSIGNED NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    monto_cop DECIMAL(12,2) NOT NULL,
    activa TINYINT(1) NOT NULL DEFAULT 1,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_suscripciones_negocio_activa (negocio_id, activa),
    CONSTRAINT fk_suscripciones_negocio FOREIGN KEY (negocio_id) REFERENCES negocios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE promociones (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(200) NOT NULL,
    inicia DATE NOT NULL,
    termina DATE NOT NULL,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE movimientos_tokens (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    negocio_id BIGINT UNSIGNED NOT NULL,
    credito INT NOT NULL DEFAULT 0,
    debito INT NOT NULL DEFAULT 0,
    compra_id BIGINT UNSIGNED,
    cita_id BIGINT UNSIGNED,
    promocion_id BIGINT UNSIGNED,
    cancelacion_id BIGINT UNSIGNED,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_movimientos_negocio_creado (negocio_id, creado_en),
    CONSTRAINT fk_movimientos_negocio FOREIGN KEY (negocio_id) REFERENCES negocios(id),
    CONSTRAINT fk_movimientos_compra FOREIGN KEY (compra_id) REFERENCES compras(id),
    CONSTRAINT fk_movimientos_cita FOREIGN KEY (cita_id) REFERENCES citas(id),
    CONSTRAINT fk_movimientos_promocion FOREIGN KEY (promocion_id) REFERENCES promociones(id),
    CONSTRAINT fk_movimientos_cancelacion FOREIGN KEY (cancelacion_id) REFERENCES cancelaciones_cita(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE tipo_comentario (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(40) NOT NULL UNIQUE,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE comentarios (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tipo_comentario_id BIGINT UNSIGNED NOT NULL,
    negocio_id BIGINT UNSIGNED,
    usuario_autor_id BIGINT UNSIGNED,
    nombre_autor VARCHAR(140),
    calificacion INT NOT NULL,
    recomienda TINYINT(1) NOT NULL DEFAULT 0,
    texto TEXT,
    sentimiento ENUM('positivo', 'neutro', 'negativo'),
    visible TINYINT(1) NOT NULL DEFAULT 1,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_comentarios_tipo (tipo_comentario_id),
    INDEX idx_comentarios_negocio (negocio_id),
    INDEX idx_comentarios_calificacion (calificacion),
    INDEX idx_comentarios_sentimiento (sentimiento),
    CONSTRAINT fk_comentarios_tipo FOREIGN KEY (tipo_comentario_id) REFERENCES tipo_comentario(id),
    CONSTRAINT fk_comentarios_negocio FOREIGN KEY (negocio_id) REFERENCES negocios(id),
    CONSTRAINT fk_comentarios_usuario FOREIGN KEY (usuario_autor_id) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE estadisticas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    alcance ENUM('pagina','negocio') NOT NULL,
    audiencia ENUM('solo_admin','publico','negocio') NOT NULL DEFAULT 'solo_admin',
    negocio_id BIGINT UNSIGNED,
    periodo_inicio DATE NOT NULL,
    periodo_fin DATE NOT NULL,
    negocios_activos INT NOT NULL DEFAULT 0,
    barberos_activos INT NOT NULL DEFAULT 0,
    citas_programadas INT NOT NULL DEFAULT 0,
    citas_canceladas INT NOT NULL DEFAULT 0,
    prom_citas_7d DECIMAL(5,2) NOT NULL DEFAULT 0,
    tasa_cancelacion DECIMAL(5,2) NOT NULL DEFAULT 0,
    tokens_comprados INT NOT NULL DEFAULT 0,
    tokens_consumidos INT NOT NULL DEFAULT 0,
    tokens_saldo_total INT NOT NULL DEFAULT 0,
    suscrip_activas INT NOT NULL DEFAULT 0,
    suscrip_nuevas INT NOT NULL DEFAULT 0,
    comentarios_total INT NOT NULL DEFAULT 0,
    calif_promedio DECIMAL(3,2) NOT NULL DEFAULT 0,
    calif_5 INT NOT NULL DEFAULT 0,
    calif_4 INT NOT NULL DEFAULT 0,
    calif_3 INT NOT NULL DEFAULT 0,
    calif_2 INT NOT NULL DEFAULT 0,
    calif_1 INT NOT NULL DEFAULT 0,
    recomiendan_si INT NOT NULL DEFAULT 0,
    recomiendan_no INT NOT NULL DEFAULT 0,
    observaciones TEXT,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX ix_estadisticas_rango (alcance, negocio_id, periodo_inicio, periodo_fin),
    INDEX idx_estadisticas_negocio (negocio_id),
    CONSTRAINT fk_estadisticas_negocio FOREIGN KEY (negocio_id) REFERENCES negocios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE administrador (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id BIGINT UNSIGNED NOT NULL,
    comentarios TEXT,
    estadisticas_id BIGINT UNSIGNED NOT NULL,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_admin_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    CONSTRAINT fk_admin_estadistica FOREIGN KEY (estadisticas_id) REFERENCES estadisticas(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET foreign_key_checks = 1;

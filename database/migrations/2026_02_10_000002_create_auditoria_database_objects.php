<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // ============ FUNCIONES AUDITORIA (PostgreSQL) ============

        DB::unprepared(<<<'SQL'
            CREATE OR REPLACE FUNCTION fn_ultima_auditoria(p_entidad VARCHAR(100), p_recurso_id VARCHAR(100))
            RETURNS BIGINT
            LANGUAGE plpgsql
            AS $$
            DECLARE v_id BIGINT;
            BEGIN
                SELECT id INTO v_id
                FROM auditoria_datos
                WHERE entidad = p_entidad AND recurso_id = p_recurso_id
                ORDER BY timestamp DESC
                LIMIT 1;
                RETURN COALESCE(v_id, 0);
            END;
            $$;
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE OR REPLACE FUNCTION fn_cambios_por_usuario(p_user_id BIGINT, p_fecha DATE)
            RETURNS INT
            LANGUAGE plpgsql
            AS $$
            DECLARE v_count INT;
            BEGIN
                SELECT COUNT(*) INTO v_count
                FROM auditoria_datos
                WHERE user_id = p_user_id AND DATE(timestamp) = p_fecha;
                RETURN COALESCE(v_count, 0);
            END;
            $$;
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE OR REPLACE FUNCTION fn_usuario_activo(p_user_id BIGINT)
            RETURNS BOOLEAN
            LANGUAGE plpgsql
            AS $$
            DECLARE v_active BOOLEAN;
            BEGIN
                SELECT (email_verified_at IS NOT NULL) INTO v_active
                FROM users
                WHERE id = p_user_id;
                RETURN COALESCE(v_active, FALSE);
            END;
            $$;
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE OR REPLACE FUNCTION fn_cambios_criticos_count()
            RETURNS INT
            LANGUAGE plpgsql
            AS $$
            DECLARE v_count INT;
            BEGIN
                SELECT COUNT(*) INTO v_count
                FROM auditoria_datos
                WHERE tipo_operacion IN ('DELETE', 'UPDATE_CRITICO')
                AND DATE(timestamp) = CURRENT_DATE;
                RETURN COALESCE(v_count, 0);
            END;
            $$;
        SQL);

        // ============ PROCEDIMIENTOS / FUNCIONES AUDITORIA ============

        DB::unprepared(<<<'SQL'
            CREATE OR REPLACE PROCEDURE sp_registrar_auditoria(
                p_user_id BIGINT,
                p_tipo_operacion VARCHAR(50),
                p_entidad VARCHAR(100),
                p_recurso_id VARCHAR(100),
                p_campo VARCHAR(100),
                p_valor_viejo TEXT,
                p_valor_nuevo TEXT
            )
            LANGUAGE plpgsql
            AS $$
            BEGIN
                INSERT INTO auditoria_datos (
                    timestamp,
                    user_id,
                    session_id,
                    tipo_operacion,
                    entidad,
                    recurso_id,
                    campo,
                    valor_original,
                    valor_nuevo,
                    created_at,
                    updated_at
                ) VALUES (
                    NOW(),
                    p_user_id,
                    substr(md5(random()::text), 1, 32),
                    p_tipo_operacion,
                    p_entidad,
                    p_recurso_id,
                    p_campo,
                    p_valor_viejo,
                    p_valor_nuevo,
                    NOW(),
                    NOW()
                );
            END;
            $$;
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE OR REPLACE PROCEDURE sp_limpiar_logs_antiguos(
                p_dias_retencion INT
            )
            LANGUAGE plpgsql
            AS $$
            BEGIN
                DELETE FROM log_login
                WHERE created_at < NOW() - (p_dias_retencion || ' days')::interval;

                DELETE FROM log_sistema
                WHERE created_at < NOW() - (p_dias_retencion || ' days')::interval;

                DELETE FROM auditoria_datos
                WHERE created_at < NOW() - (p_dias_retencion || ' days')::interval
                AND tipo_operacion NOT IN ('DELETE', 'UPDATE_CRITICO');
            END;
            $$;
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE OR REPLACE FUNCTION sp_reporte_auditoria(p_fecha_inicio DATE, p_fecha_fin DATE)
            RETURNS TABLE(
                id BIGINT,
                fecha TIMESTAMP,
                usuario VARCHAR,
                tipo_operacion VARCHAR,
                entidad VARCHAR,
                recurso_id VARCHAR,
                campo VARCHAR,
                valor_original TEXT,
                valor_nuevo TEXT
            )
            LANGUAGE plpgsql
            AS $$
            BEGIN
                RETURN QUERY
                SELECT
                    ad.id,
                    ad.timestamp as fecha,
                    u.name as usuario,
                    ad.tipo_operacion,
                    ad.entidad,
                    ad.recurso_id,
                    ad.campo,
                    ad.valor_original,
                    ad.valor_nuevo
                FROM auditoria_datos ad
                JOIN users u ON ad.user_id = u.id
                WHERE DATE(ad.timestamp) BETWEEN p_fecha_inicio AND p_fecha_fin
                ORDER BY ad.timestamp DESC;
            END;
            $$;
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE OR REPLACE FUNCTION sp_historial_cambios(p_entidad VARCHAR(100), p_recurso_id VARCHAR(100))
            RETURNS TABLE(
                id BIGINT,
                fecha TIMESTAMP,
                usuario VARCHAR,
                tipo_operacion VARCHAR,
                campo VARCHAR,
                valor_original TEXT,
                valor_nuevo TEXT
            )
            LANGUAGE plpgsql
            AS $$
            BEGIN
                RETURN QUERY
                SELECT
                    ad.id,
                    ad.timestamp as fecha,
                    u.name as usuario,
                    ad.tipo_operacion,
                    ad.campo,
                    ad.valor_original,
                    ad.valor_nuevo
                FROM auditoria_datos ad
                JOIN users u ON ad.user_id = u.id
                WHERE ad.entidad = p_entidad AND ad.recurso_id = p_recurso_id
                ORDER BY ad.timestamp DESC;
            END;
            $$;
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE OR REPLACE FUNCTION sp_validar_usuario(p_user_id BIGINT)
            RETURNS TABLE(
                id BIGINT,
                name VARCHAR,
                email VARCHAR,
                email_verified_at TIMESTAMP,
                total_cambios BIGINT,
                ultimo_cambio TIMESTAMP
            )
            LANGUAGE plpgsql
            AS $$
            BEGIN
                RETURN QUERY
                SELECT
                    u.id,
                    u.name,
                    u.email,
                    u.email_verified_at,
                    COUNT(ad.id) as total_cambios,
                    MAX(ad.timestamp) as ultimo_cambio
                FROM users u
                LEFT JOIN auditoria_datos ad ON u.id = ad.user_id
                WHERE u.id = p_user_id
                GROUP BY u.id;
            END;
            $$;
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE OR REPLACE FUNCTION sp_cambios_criticos()
            RETURNS TABLE(
                fecha TIMESTAMP,
                usuario VARCHAR,
                tipo_operacion VARCHAR,
                entidad VARCHAR,
                recurso_id VARCHAR,
                valor_original TEXT,
                valor_nuevo TEXT
            )
            LANGUAGE plpgsql
            AS $$
            BEGIN
                RETURN QUERY
                SELECT
                    ad.timestamp as fecha,
                    u.name as usuario,
                    ad.tipo_operacion,
                    ad.entidad,
                    ad.recurso_id,
                    ad.valor_original,
                    ad.valor_nuevo
                FROM auditoria_datos ad
                JOIN users u ON ad.user_id = u.id
                WHERE ad.tipo_operacion IN ('DELETE', 'UPDATE_CRITICO')
                AND DATE(ad.timestamp) = CURRENT_DATE
                ORDER BY ad.timestamp DESC;
            END;
            $$;
        SQL);
    }

    public function down(): void
    {
        DB::unprepared('DROP FUNCTION IF EXISTS fn_ultima_auditoria(VARCHAR, VARCHAR)');
        DB::unprepared('DROP FUNCTION IF EXISTS fn_cambios_por_usuario(BIGINT, DATE)');
        DB::unprepared('DROP FUNCTION IF EXISTS fn_usuario_activo(BIGINT)');
        DB::unprepared('DROP FUNCTION IF EXISTS fn_cambios_criticos_count()');

        DB::unprepared('DROP PROCEDURE IF EXISTS sp_registrar_auditoria(BIGINT, VARCHAR, VARCHAR, VARCHAR, VARCHAR, TEXT, TEXT)');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_limpiar_logs_antiguos(INT)');
        DB::unprepared('DROP FUNCTION IF EXISTS sp_reporte_auditoria(DATE, DATE)');
        DB::unprepared('DROP FUNCTION IF EXISTS sp_historial_cambios(VARCHAR, VARCHAR)');
        DB::unprepared('DROP FUNCTION IF EXISTS sp_validar_usuario(BIGINT)');
        DB::unprepared('DROP FUNCTION IF EXISTS sp_cambios_criticos()');
    }
};

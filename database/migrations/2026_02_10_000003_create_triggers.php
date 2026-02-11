<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // ============ TRIGGER: PRODUCTOS (PostgreSQL) ============

        DB::unprepared(<<<'SQL'
            CREATE OR REPLACE FUNCTION tr_audit_producto_insert_fn()
            RETURNS TRIGGER
            LANGUAGE plpgsql
            AS $$
            BEGIN
                INSERT INTO auditoria_datos (
                    timestamp, user_id, session_id, tipo_operacion,
                    entidad, recurso_id, campo, valor_original, valor_nuevo,
                    created_at, updated_at
                ) VALUES (
                    NOW(),
                    COALESCE((SELECT id FROM users ORDER BY created_at DESC LIMIT 1), 1),
                    substr(md5(random()::text), 1, 32),
                    'INSERT',
                    'productos',
                    NEW.id,
                    'nombre',
                    NULL,
                    NEW.nombre,
                    NOW(),
                    NOW()
                );
                RETURN NEW;
            END;
            $$;
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE TRIGGER tr_audit_producto_insert
            AFTER INSERT ON productos
            FOR EACH ROW
            EXECUTE FUNCTION tr_audit_producto_insert_fn();
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE OR REPLACE FUNCTION tr_audit_producto_update_fn()
            RETURNS TRIGGER
            LANGUAGE plpgsql
            AS $$
            BEGIN
                IF NEW.precio_unitario IS DISTINCT FROM OLD.precio_unitario THEN
                    INSERT INTO auditoria_datos (
                        timestamp, user_id, session_id, tipo_operacion,
                        entidad, recurso_id, campo, valor_original, valor_nuevo,
                        created_at, updated_at
                    ) VALUES (
                        NOW(),
                        COALESCE((SELECT id FROM users ORDER BY created_at DESC LIMIT 1), 1),
                        substr(md5(random()::text), 1, 32),
                        'UPDATE',
                        'productos',
                        NEW.id,
                        'precio_unitario',
                        OLD.precio_unitario::text,
                        NEW.precio_unitario::text,
                        NOW(),
                        NOW()
                    );
                END IF;

                IF NEW.cantidad_stock IS DISTINCT FROM OLD.cantidad_stock THEN
                    INSERT INTO auditoria_datos (
                        timestamp, user_id, session_id, tipo_operacion,
                        entidad, recurso_id, campo, valor_original, valor_nuevo,
                        created_at, updated_at
                    ) VALUES (
                        NOW(),
                        COALESCE((SELECT id FROM users ORDER BY created_at DESC LIMIT 1), 1),
                        substr(md5(random()::text), 1, 32),
                        'UPDATE',
                        'productos',
                        NEW.id,
                        'cantidad_stock',
                        OLD.cantidad_stock::text,
                        NEW.cantidad_stock::text,
                        NOW(),
                        NOW()
                    );
                END IF;

                RETURN NEW;
            END;
            $$;
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE TRIGGER tr_audit_producto_update
            AFTER UPDATE ON productos
            FOR EACH ROW
            EXECUTE FUNCTION tr_audit_producto_update_fn();
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE OR REPLACE FUNCTION tr_validar_stock_producto_fn()
            RETURNS TRIGGER
            LANGUAGE plpgsql
            AS $$
            BEGIN
                IF NEW.cantidad_stock < 0 THEN
                    RAISE EXCEPTION 'No se puede establecer stock negativo';
                END IF;

                IF NEW.cantidad_stock < NEW.stock_minimo AND OLD.cantidad_stock >= OLD.stock_minimo THEN
                    INSERT INTO log_sistema (timestamp, nivel_log_id, mensaje, created_at, updated_at)
                    SELECT NOW(), id, 'Alerta: Producto ' || NEW.nombre || ' por debajo de stock minimo', NOW(), NOW()
                    FROM log_nivel WHERE nombre = 'WARNING';
                END IF;

                RETURN NEW;
            END;
            $$;
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE TRIGGER tr_validar_stock_producto
            BEFORE UPDATE ON productos
            FOR EACH ROW
            EXECUTE FUNCTION tr_validar_stock_producto_fn();
        SQL);

        // ============ TRIGGER: PROVEEDORES ============

        DB::unprepared(<<<'SQL'
            CREATE OR REPLACE FUNCTION tr_audit_proveedor_insert_fn()
            RETURNS TRIGGER
            LANGUAGE plpgsql
            AS $$
            BEGIN
                INSERT INTO auditoria_datos (
                    timestamp, user_id, session_id, tipo_operacion,
                    entidad, recurso_id, campo, valor_original, valor_nuevo,
                    created_at, updated_at
                ) VALUES (
                    NOW(),
                    COALESCE((SELECT id FROM users ORDER BY created_at DESC LIMIT 1), 1),
                    substr(md5(random()::text), 1, 32),
                    'INSERT',
                    'proveedores',
                    NEW.ruc,
                    'nombre',
                    NULL,
                    NEW.nombre,
                    NOW(),
                    NOW()
                );
                RETURN NEW;
            END;
            $$;
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE TRIGGER tr_audit_proveedor_insert
            AFTER INSERT ON proveedores
            FOR EACH ROW
            EXECUTE FUNCTION tr_audit_proveedor_insert_fn();
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE OR REPLACE FUNCTION tr_audit_proveedor_update_fn()
            RETURNS TRIGGER
            LANGUAGE plpgsql
            AS $$
            BEGIN
                IF NEW.email IS DISTINCT FROM OLD.email THEN
                    INSERT INTO auditoria_datos (
                        timestamp, user_id, session_id, tipo_operacion,
                        entidad, recurso_id, campo, valor_original, valor_nuevo,
                        created_at, updated_at
                    ) VALUES (
                        NOW(),
                        COALESCE((SELECT id FROM users ORDER BY created_at DESC LIMIT 1), 1),
                        substr(md5(random()::text), 1, 32),
                        'UPDATE',
                        'proveedores',
                        NEW.ruc,
                        'email',
                        OLD.email,
                        NEW.email,
                        NOW(),
                        NOW()
                    );
                END IF;

                RETURN NEW;
            END;
            $$;
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE TRIGGER tr_audit_proveedor_update
            AFTER UPDATE ON proveedores
            FOR EACH ROW
            EXECUTE FUNCTION tr_audit_proveedor_update_fn();
        SQL);

        // ============ TRIGGER: CATEGORIAS ============

        DB::unprepared(<<<'SQL'
            CREATE OR REPLACE FUNCTION tr_audit_categoria_insert_fn()
            RETURNS TRIGGER
            LANGUAGE plpgsql
            AS $$
            BEGIN
                INSERT INTO auditoria_datos (
                    timestamp, user_id, session_id, tipo_operacion,
                    entidad, recurso_id, campo, valor_original, valor_nuevo,
                    created_at, updated_at
                ) VALUES (
                    NOW(),
                    COALESCE((SELECT id FROM users ORDER BY created_at DESC LIMIT 1), 1),
                    substr(md5(random()::text), 1, 32),
                    'INSERT',
                    'categorias',
                    NEW.id,
                    'nombre',
                    NULL,
                    NEW.nombre,
                    NOW(),
                    NOW()
                );
                RETURN NEW;
            END;
            $$;
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE TRIGGER tr_audit_categoria_insert
            AFTER INSERT ON categorias
            FOR EACH ROW
            EXECUTE FUNCTION tr_audit_categoria_insert_fn();
        SQL);
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS tr_audit_producto_insert ON productos');
        DB::unprepared('DROP TRIGGER IF EXISTS tr_audit_producto_update ON productos');
        DB::unprepared('DROP TRIGGER IF EXISTS tr_validar_stock_producto ON productos');
        DB::unprepared('DROP TRIGGER IF EXISTS tr_audit_proveedor_insert ON proveedores');
        DB::unprepared('DROP TRIGGER IF EXISTS tr_audit_proveedor_update ON proveedores');
        DB::unprepared('DROP TRIGGER IF EXISTS tr_audit_categoria_insert ON categorias');

        DB::unprepared('DROP FUNCTION IF EXISTS tr_audit_producto_insert_fn()');
        DB::unprepared('DROP FUNCTION IF EXISTS tr_audit_producto_update_fn()');
        DB::unprepared('DROP FUNCTION IF EXISTS tr_validar_stock_producto_fn()');
        DB::unprepared('DROP FUNCTION IF EXISTS tr_audit_proveedor_insert_fn()');
        DB::unprepared('DROP FUNCTION IF EXISTS tr_audit_proveedor_update_fn()');
        DB::unprepared('DROP FUNCTION IF EXISTS tr_audit_categoria_insert_fn()');
    }
};

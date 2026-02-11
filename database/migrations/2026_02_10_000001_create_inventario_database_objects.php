<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // ============ FUNCIONES INVENTARIO (PostgreSQL) ============

        DB::unprepared(<<<'SQL'
            CREATE OR REPLACE FUNCTION fn_stock_disponible(p_producto_id INT)
            RETURNS INT
            LANGUAGE plpgsql
            AS $$
            DECLARE v_stock INT;
            BEGIN
                SELECT cantidad_stock INTO v_stock
                FROM productos
                WHERE id = p_producto_id AND deleted_at IS NULL;
                RETURN COALESCE(v_stock, 0);
            END;
            $$;
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE OR REPLACE FUNCTION fn_obtener_precio_final(p_producto_id INT)
            RETURNS NUMERIC(10,2)
            LANGUAGE plpgsql
            AS $$
            DECLARE v_precio NUMERIC(10,2);
            DECLARE v_en_oferta BOOLEAN;
            DECLARE v_precio_oferta NUMERIC(10,2);
            BEGIN
                SELECT en_oferta, precio_unitario, precio_oferta
                INTO v_en_oferta, v_precio, v_precio_oferta
                FROM productos
                WHERE id = p_producto_id AND deleted_at IS NULL;

                IF v_en_oferta IS TRUE AND v_precio_oferta IS NOT NULL THEN
                    RETURN v_precio_oferta;
                END IF;

                RETURN COALESCE(v_precio, 0);
            END;
            $$;
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE OR REPLACE FUNCTION fn_contar_productos_categoria(p_categoria_id INT)
            RETURNS INT
            LANGUAGE plpgsql
            AS $$
            DECLARE v_count INT;
            BEGIN
                SELECT COUNT(*) INTO v_count
                FROM productos
                WHERE categoria_id = p_categoria_id AND deleted_at IS NULL;
                RETURN COALESCE(v_count, 0);
            END;
            $$;
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE OR REPLACE FUNCTION fn_valor_inventario_total()
            RETURNS NUMERIC(15,2)
            LANGUAGE plpgsql
            AS $$
            DECLARE v_total NUMERIC(15,2);
            BEGIN
                SELECT COALESCE(SUM(cantidad_stock * precio_unitario), 0) INTO v_total
                FROM productos
                WHERE deleted_at IS NULL;
                RETURN v_total;
            END;
            $$;
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE OR REPLACE FUNCTION fn_margen_ganancia(p_precio_costo NUMERIC(10,2), p_precio_venta NUMERIC(10,2))
            RETURNS NUMERIC(5,2)
            LANGUAGE plpgsql
            AS $$
            BEGIN
                RETURN COALESCE(((p_precio_venta - p_precio_costo) / NULLIF(p_precio_costo, 0)) * 100, 0);
            END;
            $$;
        SQL);

        // ============ FUNCIONES / PROCEDIMIENTOS INVENTARIO ============

        DB::unprepared(<<<'SQL'
            CREATE OR REPLACE FUNCTION sp_productos_bajo_stock()
            RETURNS INT
            LANGUAGE plpgsql
            AS $$
            DECLARE v_count INT;
            BEGIN
                SELECT COUNT(*) INTO v_count
                FROM productos
                WHERE cantidad_stock < stock_minimo
                AND deleted_at IS NULL;
                RETURN COALESCE(v_count, 0);
            END;
            $$;
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE OR REPLACE FUNCTION sp_valor_inventario_por_categoria()
            RETURNS TABLE(
                id INT,
                nombre VARCHAR,
                cantidad_productos BIGINT,
                total_unidades BIGINT,
                valor_total NUMERIC(15,2)
            )
            LANGUAGE plpgsql
            AS $$
            BEGIN
                RETURN QUERY
                SELECT
                    c.id,
                    c.nombre,
                    COUNT(p.id) as cantidad_productos,
                    COALESCE(SUM(p.cantidad_stock), 0) as total_unidades,
                    COALESCE(SUM(p.cantidad_stock * p.precio_unitario), 0) as valor_total
                FROM categorias c
                LEFT JOIN productos p ON c.id = p.categoria_id AND p.deleted_at IS NULL
                GROUP BY c.id, c.nombre;
            END;
            $$;
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE OR REPLACE PROCEDURE sp_actualizar_stock(
                p_producto_id INT,
                p_cantidad INT,
                p_tipo TEXT,
                p_razon VARCHAR,
                p_user_id BIGINT
            )
            LANGUAGE plpgsql
            AS $$
            DECLARE v_stock_actual INT;
            DECLARE v_nuevo_stock INT;
            BEGIN
                SELECT cantidad_stock INTO v_stock_actual
                FROM productos
                WHERE id = p_producto_id AND deleted_at IS NULL
                FOR UPDATE;

                IF v_stock_actual IS NULL THEN
                    RAISE EXCEPTION 'Producto no encontrado';
                END IF;

                IF p_tipo = 'entrada' THEN
                    v_nuevo_stock := v_stock_actual + p_cantidad;
                ELSE
                    v_nuevo_stock := v_stock_actual - p_cantidad;
                END IF;

                IF v_nuevo_stock < 0 THEN
                    RAISE EXCEPTION 'Stock insuficiente';
                END IF;

                UPDATE productos SET cantidad_stock = v_nuevo_stock WHERE id = p_producto_id;

                INSERT INTO log_movimiento_inventario (
                    producto_id, tipo_movimiento, cantidad, razon, user_id, created_at, updated_at
                ) VALUES (
                    p_producto_id, p_tipo, p_cantidad, p_razon, p_user_id, NOW(), NOW()
                );
            END;
            $$;
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE OR REPLACE PROCEDURE sp_actualizar_precio_masivo(
                p_porcentaje NUMERIC(5,2),
                p_categoria_id INT
            )
            LANGUAGE plpgsql
            AS $$
            BEGIN
                UPDATE productos
                SET precio_unitario = precio_unitario * (1 + (p_porcentaje / 100))
                WHERE deleted_at IS NULL
                AND (p_categoria_id IS NULL OR categoria_id = p_categoria_id);
            END;
            $$;
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE OR REPLACE FUNCTION sp_productos_por_proveedor()
            RETURNS TABLE(
                ruc VARCHAR,
                nombre VARCHAR,
                cantidad_productos BIGINT,
                precio_promedio NUMERIC(10,2),
                precio_minimo NUMERIC(10,2),
                precio_maximo NUMERIC(10,2)
            )
            LANGUAGE plpgsql
            AS $$
            BEGIN
                RETURN QUERY
                SELECT
                    pr.ruc,
                    pr.nombre,
                    COUNT(p.id) as cantidad_productos,
                    AVG(pp.precio_costo) as precio_promedio,
                    MIN(pp.precio_costo) as precio_minimo,
                    MAX(pp.precio_costo) as precio_maximo
                FROM proveedores pr
                LEFT JOIN producto_proveedores pp ON pr.ruc = pp.proveedor_ruc
                LEFT JOIN productos p ON pp.producto_id = p.id AND p.deleted_at IS NULL
                WHERE pr.deleted_at IS NULL
                GROUP BY pr.ruc, pr.nombre;
            END;
            $$;
        SQL);
    }

    public function down(): void
    {
        DB::unprepared('DROP FUNCTION IF EXISTS fn_stock_disponible(INT)');
        DB::unprepared('DROP FUNCTION IF EXISTS fn_obtener_precio_final(INT)');
        DB::unprepared('DROP FUNCTION IF EXISTS fn_contar_productos_categoria(INT)');
        DB::unprepared('DROP FUNCTION IF EXISTS fn_valor_inventario_total()');
        DB::unprepared('DROP FUNCTION IF EXISTS fn_margen_ganancia(NUMERIC, NUMERIC)');

        DB::unprepared('DROP FUNCTION IF EXISTS sp_productos_bajo_stock()');
        DB::unprepared('DROP FUNCTION IF EXISTS sp_valor_inventario_por_categoria()');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_actualizar_stock(INT, INT, TEXT, VARCHAR, BIGINT)');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_actualizar_precio_masivo(NUMERIC, INT)');
        DB::unprepared('DROP FUNCTION IF EXISTS sp_productos_por_proveedor()');
    }
};

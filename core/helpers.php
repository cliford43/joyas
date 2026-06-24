<?php

/**
 * VILUNA — Funciones de ayuda globales
 */

if (!function_exists('slugify')) {
    /**
     * Convierte un string a slug URL-friendly.
     * Asegura unicidad con sufijo numérico si se pasa tabla y columna.
     */
    function slugify(string $text, string $table = '', string $column = 'slug', int $excludeId = 0): string
    {
        // Transliterar caracteres especiales al equivalente ASCII
        $text = transliterator_transliterate('Any-Latin; Latin-ASCII; Lower()', $text);

        if ($text === false || $text === '') {
            $text = 'item';
        }

        // Solo letras, números y guiones
        $text = preg_replace('/[^a-z0-9\s-]/', '', strtolower($text));
        $text = preg_replace('/[\s-]+/', '-', trim($text));
        $text = trim($text, '-');

        if ($text === '') {
            $text = 'item';
        }

        // Si se pasa tabla, verificar unicidad
        if ($table !== '') {
            $slug = $text;
            $i    = 1;
            while (slugExists($table, $column, $slug, $excludeId)) {
                $slug = $text . '-' . $i;
                $i++;
            }
            return $slug;
        }

        return $text;
    }
}

if (!function_exists('slugExists')) {
    /**
     * Verifica si un slug ya existe en una tabla (excluyendo un ID).
     */
    function slugExists(string $table, string $column, string $slug, int $excludeId = 0): bool
    {
        $db = \Core\Model::getDbPublic();
        $sql = "SELECT COUNT(*) FROM `{$table}` WHERE `{$column}` = :slug";
        $params = [':slug' => $slug];

        if ($excludeId > 0) {
            $sql .= ' AND id != :id';
            $params[':id'] = $excludeId;
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn() > 0;
    }
}

if (!function_exists('e')) {
    /**
     * Escapa un string para salida HTML segura.
     */
    function e(mixed $value): string
    {
        if ($value === null) {
            return '';
        }
        return htmlspecialchars((string)$value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}

if (!function_exists('asset')) {
    /**
     * Genera la URL de un asset público.
     */
    function asset(string $path): string
    {
        $base = defined('APP_URL') ? APP_URL : '';
        return $base . '/assets/' . ltrim($path, '/');
    }
}

if (!function_exists('url')) {
    /**
     * Genera una URL absoluta del sistema.
     */
    function url(string $path = ''): string
    {
        $base = defined('APP_URL') ? APP_URL : '';
        return $base . '/' . ltrim($path, '/');
    }
}

if (!function_exists('mediaUrl')) {
    /**
     * Genera una URL pública para medios guardados en assets o uploads.
     */
    function mediaUrl(?string $path): string
    {
        if (empty($path)) {
            return asset('images/placeholder-joya.jpg');
        }

        $normalized = ltrim(str_replace('\\', '/', $path), '/');

        if (str_starts_with($normalized, 'assets/')) {
            return (defined('APP_URL') ? APP_URL : '') . '/' . $normalized;
        }

        if (str_starts_with($normalized, 'uploads/')) {
            return url($normalized);
        }

        return (defined('APP_URL') ? APP_URL : '') . '/' . $normalized;
    }
}

if (!function_exists('normalizeHexColor')) {
    /**
     * Normaliza colores HEX a formato #RRGGBB o retorna un valor por defecto.
     */
    function normalizeHexColor(mixed $value, string $default): string
    {
        $candidate = strtoupper(trim((string)$value));
        if ($candidate === '') {
            return strtoupper($default);
        }

        if (preg_match('/^#?[0-9A-F]{6}$/', $candidate) !== 1) {
            return strtoupper($default);
        }

        if ($candidate[0] !== '#') {
            $candidate = '#' . $candidate;
        }

        return $candidate;
    }
}

if (!function_exists('themeCssVariables')) {
    /**
     * Genera un bloque de variables CSS con paleta configurable.
     */
    function themeCssVariables(array $config = []): string
    {
        $map = [
            '--color-gold'              => normalizeHexColor($config['theme_brand_primary'] ?? '', '#D4AF37'),
            '--color-gold-light'        => normalizeHexColor($config['theme_brand_primary_light'] ?? '', '#F5D87A'),
            '--color-gold-dark'         => normalizeHexColor($config['theme_brand_primary_dark'] ?? '', '#B8961E'),
            '--color-black'             => normalizeHexColor($config['theme_base_text'] ?? '', '#111111'),
            '--color-white'             => normalizeHexColor($config['theme_base_bg'] ?? '', '#FFFFFF'),
            '--color-gray'              => normalizeHexColor($config['theme_base_muted'] ?? '', '#6C757D'),
            '--menu-bg'                 => normalizeHexColor($config['theme_menu_bg'] ?? '', '#111111'),
            '--menu-text'               => normalizeHexColor($config['theme_menu_text'] ?? '', '#FFFFFF'),
            '--menu-hover'              => normalizeHexColor($config['theme_menu_hover'] ?? '', '#D4AF37'),
            '--btn-primary-bg'          => normalizeHexColor($config['theme_btn_primary_bg'] ?? '', '#D4AF37'),
            '--btn-primary-text'        => normalizeHexColor($config['theme_btn_primary_text'] ?? '', '#111111'),
            '--btn-primary-hover-bg'    => normalizeHexColor($config['theme_btn_primary_hover_bg'] ?? '', '#B8961E'),
            '--btn-primary-hover-text'  => normalizeHexColor($config['theme_btn_primary_hover_text'] ?? '', '#FFFFFF'),
            '--btn-outline-border'      => normalizeHexColor($config['theme_btn_outline_border'] ?? '', '#D4AF37'),
            '--btn-outline-text'        => normalizeHexColor($config['theme_btn_outline_text'] ?? '', '#D4AF37'),
            '--btn-outline-hover-bg'    => normalizeHexColor($config['theme_btn_outline_hover_bg'] ?? '', '#D4AF37'),
            '--btn-outline-hover-text'  => normalizeHexColor($config['theme_btn_outline_hover_text'] ?? '', '#111111'),
        ];

        $pairs = [];
        foreach ($map as $varName => $value) {
            $pairs[] = $varName . ': ' . $value;
        }

        return implode('; ', $pairs) . ';';
    }
}

if (!function_exists('formatPrice')) {
    /**
     * Formatea un precio como moneda.
     */
    function formatPrice(float $price, string $symbol = 'Q '): string
    {
        return $symbol . number_format($price, 2, '.', ',');
    }
}

if (!function_exists('timeAgo')) {
    /**
     * Retorna texto relativo de tiempo.
     */
    function timeAgo(string $datetime): string
    {
        $diff = time() - strtotime($datetime);

        if ($diff < 60) return 'hace ' . $diff . ' segundos';
        if ($diff < 3600) return 'hace ' . floor($diff / 60) . ' minutos';
        if ($diff < 86400) return 'hace ' . floor($diff / 3600) . ' horas';
        if ($diff < 2592000) return 'hace ' . floor($diff / 86400) . ' días';
        if ($diff < 31536000) return 'hace ' . floor($diff / 2592000) . ' meses';
        return 'hace ' . floor($diff / 31536000) . ' años';
    }
}

if (!function_exists('truncate')) {
    /**
     * Trunca un texto a N caracteres con elipsis.
     */
    function truncate(string $text, int $length = 100, string $ellipsis = '...'): string
    {
        if (mb_strlen($text) <= $length) {
            return $text;
        }
        return mb_substr($text, 0, $length - mb_strlen($ellipsis)) . $ellipsis;
    }
}

if (!function_exists('sanitizeEmail')) {
    /**
     * Sanitiza y valida un correo electrónico.
     * Retorna el correo limpio o null si es inválido.
     */
    function sanitizeEmail(string $email): ?string
    {
        $clean = filter_var(trim($email), FILTER_SANITIZE_EMAIL);
        return filter_var($clean, FILTER_VALIDATE_EMAIL) ? $clean : null;
    }
}

if (!function_exists('cardColumnClass')) {
    /**
     * Retorna clases de columna Bootstrap para tarjetas de producto
     * según el valor de configuración cards_por_fila.
     */
    function cardColumnClass(string|int $cardsPerRow = 4): string
    {
        return match ((string)$cardsPerRow) {
            '2' => 'col-6',
            '3' => 'col-6 col-md-4',
            '4' => 'col-6 col-md-4 col-xl-3',
            '6' => 'col-6 col-md-4 col-lg-2',
            default => 'col-6 col-md-4 col-xl-3',
        };
    }
}

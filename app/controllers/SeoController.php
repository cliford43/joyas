<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\ProductModel;
use App\Models\CategoryModel;
use App\Models\ConfigModel;

/**
 * SeoController — Sitemap.xml y robots.txt dinámicos.
 */
class SeoController extends Controller
{
    /** GET /sitemap.xml */
    public function sitemap(): void
    {
        $productos  = ProductModel::findAll(500);
        $categorias = CategoryModel::findActive();
        $baseUrl    = defined('APP_URL') ? APP_URL : '';

        header('Content-Type: application/xml; charset=UTF-8');

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        // Página principal
        echo $this->sitemapUrl($baseUrl . '/', '1.0', 'daily');

        // Catálogo
        echo $this->sitemapUrl($baseUrl . '/catalogo', '0.9', 'daily');

        // Categorías activas — /catalogo/{slug}
        foreach ($categorias as $cat) {
            $url = $baseUrl . '/catalogo/' . rawurlencode($cat['slug']);
            echo $this->sitemapUrl($url, '0.8', 'weekly');
        }

        // Productos activos — /producto/{slug}
        foreach ($productos as $prod) {
            if (!(int)$prod['activo']) continue;
            $url      = $baseUrl . '/producto/' . rawurlencode($prod['slug']);
            $lastmod  = date('Y-m-d', strtotime($prod['fecha_creacion']));
            echo $this->sitemapUrl($url, '0.7', 'weekly', $lastmod);
        }

        echo '</urlset>';
        exit;
    }

    /** GET /robots.txt */
    public function robots(): void
    {
        $baseUrl = defined('APP_URL') ? APP_URL : '';

        header('Content-Type: text/plain; charset=UTF-8');

        echo "User-agent: *\n";
        echo "Allow: /\n";
        echo "\n";
        echo "# Bloquear rutas privadas\n";
        echo "Disallow: /admin/\n";
        echo "Disallow: /mi-cuenta/\n";
        echo "Disallow: /checkout/\n";
        echo "Disallow: /carrito\n";
        echo "Disallow: /login\n";
        echo "Disallow: /registro\n";
        echo "Disallow: /recuperar\n";
        echo "Disallow: /restablecer/\n";
        echo "Disallow: /auth/\n";
        echo "\n";
        echo "Sitemap: {$baseUrl}/sitemap.xml\n";

        exit;
    }

    private function sitemapUrl(string $loc, string $priority = '0.5', string $changefreq = 'weekly', string $lastmod = ''): string
    {
        $xml  = "  <url>\n";
        $xml .= "    <loc>" . htmlspecialchars($loc, ENT_XML1, 'UTF-8') . "</loc>\n";
        if ($lastmod) {
            $xml .= "    <lastmod>{$lastmod}</lastmod>\n";
        }
        $xml .= "    <changefreq>{$changefreq}</changefreq>\n";
        $xml .= "    <priority>{$priority}</priority>\n";
        $xml .= "  </url>\n";
        return $xml;
    }
}

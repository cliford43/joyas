<?php

namespace App\Models;

use Core\Model;

/**
 * EmailTemplateModel — Gestión de plantillas de correo electrónico.
 */
class EmailTemplateModel extends Model
{
    /**
     * Busca una plantilla por su slug.
     */
    public static function findBySlug(string $slug): ?array
    {
        return static::fetchOne(
            'SELECT * FROM plantillas_correo WHERE slug = :slug LIMIT 1',
            [':slug' => $slug]
        );
    }

    /**
     * Retorna todas las plantillas ordenadas por nombre.
     */
    public static function findAll(): array
    {
        return static::fetchAll(
            'SELECT * FROM plantillas_correo ORDER BY nombre ASC'
        );
    }

    /**
     * Busca una plantilla por ID.
     */
    public static function findById(int $id): ?array
    {
        return static::fetchOne(
            'SELECT * FROM plantillas_correo WHERE id = :id LIMIT 1',
            [':id' => $id]
        );
    }

    /**
     * Actualiza una plantilla existente.
     */
    public static function update(int $id, array $data): void
    {
        static::execute(
            'UPDATE plantillas_correo SET asunto = :asunto, contenido = :contenido WHERE id = :id',
            [
                ':asunto'    => $data['asunto'],
                ':contenido' => $data['contenido'],
                ':id'        => $id,
            ]
        );
    }

    /**
     * Renderiza una plantilla reemplazando {variable_name} markers con valores reales.
     *
     * @param string $slug      Slug de la plantilla
     * @param array  $variables Mapa de variable_name => valor
     * @return array{subject: string, body: string}|null  null si la plantilla no existe
     */
    public static function render(string $slug, array $variables): ?array
    {
        $template = static::findBySlug($slug);

        if (!$template) {
            return null;
        }

        $subject = static::replaceVariables($template['asunto'], $variables);
        $body    = static::replaceVariables($template['contenido'], $variables);

        return [
            'subject' => $subject,
            'body'    => $body,
        ];
    }

    /**
     * Reemplaza todas las ocurrencias de {variable_name} en el texto.
     * Variables sin valor proporcionado se reemplazan con string vacío.
     */
    public static function replaceVariables(string $text, array $variables): string
    {
        // Replace provided variables
        foreach ($variables as $key => $value) {
            $text = str_replace('{' . $key . '}', (string)$value, $text);
        }

        // Replace any remaining unresolved markers with empty string
        $text = preg_replace('/\{[a-zA-Z_][a-zA-Z0-9_]*\}/', '', $text);

        return $text;
    }
}

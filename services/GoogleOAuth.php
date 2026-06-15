<?php

namespace Services;

use League\OAuth2\Client\Provider\Google;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;

/**
 * Services\GoogleOAuth — Gestión de autenticación con Google OAuth 2.0.
 * Usa league/oauth2-google.
 */
class GoogleOAuth
{
    private Google $provider;

    public function __construct()
    {
        $this->provider = new Google([
            'clientId'     => GOOGLE_CLIENT_ID,
            'clientSecret' => GOOGLE_CLIENT_SECRET,
            'redirectUri'  => GOOGLE_REDIRECT_URI,
        ]);
    }

    /**
     * Retorna la URL de autorización de Google y guarda el state en sesión.
     */
    public function getAuthorizationUrl(): string
    {
        $url = $this->provider->getAuthorizationUrl([
            'scope' => ['email', 'profile'],
        ]);

        // Guardar state para verificación CSRF de OAuth
        $_SESSION['oauth2_state'] = $this->provider->getState();

        return $url;
    }

    /**
     * Verifica el state recibido contra el guardado en sesión.
     */
    public function validateState(string $state): bool
    {
        $saved = $_SESSION['oauth2_state'] ?? null;
        unset($_SESSION['oauth2_state']);
        return $saved !== null && hash_equals($saved, $state);
    }

    /**
     * Intercambia el código de autorización por un token de acceso
     * y retorna los datos del perfil del usuario.
     *
     * @return array{id: string, email: string, nombre: string, apellido: string, avatar: string}
     * @throws \RuntimeException si el intercambio falla
     */
    public function getUserFromCode(string $code): array
    {
        try {
            $token = $this->provider->getAccessToken('authorization_code', [
                'code' => $code,
            ]);

            /** @var \League\OAuth2\Client\Provider\GoogleUser $googleUser */
            $googleUser = $this->provider->getResourceOwner($token);

            return [
                'id'       => $googleUser->getId(),
                'email'    => strtolower($googleUser->getEmail() ?? ''),
                'nombre'   => $googleUser->getFirstName() ?? $googleUser->getName() ?? 'Usuario',
                'apellido' => $googleUser->getLastName() ?? '',
                'avatar'   => $googleUser->getAvatar() ?? '',
            ];
        } catch (IdentityProviderException $e) {
            throw new \RuntimeException('Error al autenticar con Google: ' . $e->getMessage());
        }
    }
}

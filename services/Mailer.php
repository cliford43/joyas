<?php

namespace Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as MailException;

/**
 * Services\Mailer — Servicio de correo transaccional usando PHPMailer.
 * Todos los fallos son capturados, registrados en log y NO interrumpen el flujo principal.
 */
class Mailer
{
    private PHPMailer $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
        $this->mailer->isSMTP();
        $this->mailer->Host       = MAIL_HOST;
        $this->mailer->SMTPAuth   = true;
        $this->mailer->Username   = MAIL_USERNAME;
        $this->mailer->Password   = MAIL_PASSWORD;
        $this->mailer->SMTPSecure = MAIL_ENCRYPTION === 'ssl'
            ? PHPMailer::ENCRYPTION_SMTPS
            : PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->Port       = MAIL_PORT;
        $this->mailer->CharSet    = 'UTF-8';
        $this->mailer->setFrom(MAIL_FROM_EMAIL, MAIL_FROM_NAME);
    }

    /**
     * Envía un correo usando una plantilla PHP.
     *
     * @param string $to       Correo destinatario
     * @param string $subject  Asunto
     * @param string $template Nombre de plantilla en app/views/emails/ (sin .php)
     * @param array  $data     Variables a exponer en la plantilla
     * @return bool  true si se envió, false si falló
     */
    public function send(string $to, string $subject, string $template, array $data = []): bool
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($to);
            $this->mailer->isHTML(true);
            $this->mailer->Subject = $subject;
            $this->mailer->Body    = $this->renderTemplate($template, $data);
            $this->mailer->AltBody = strip_tags($this->mailer->Body);
            $this->mailer->send();
            return true;
        } catch (MailException $e) {
            $this->logError($to, $subject, $e->getMessage());
            return false;
        } catch (\Throwable $e) {
            $this->logError($to, $subject, $e->getMessage());
            return false;
        }
    }

    /**
     * Renderiza una plantilla de correo y retorna el HTML.
     */
    private function renderTemplate(string $template, array $data): string
    {
        extract($data, EXTR_SKIP);
        $path = (defined('APP_PATH') ? APP_PATH : dirname(__DIR__) . '/app')
              . '/views/emails/' . $template . '.php';

        if (!file_exists($path)) {
            return '<p>Correo de VILUNA Joyería</p>';
        }

        ob_start();
        include $path;
        return ob_get_clean();
    }

    /**
     * Registra el error en storage/logs/mail.log sin interrumpir el flujo.
     */
    private function logError(string $to, string $subject, string $error): void
    {
        $logPath = defined('LOG_PATH') ? LOG_PATH : dirname(__DIR__) . '/storage/logs';
        $file    = $logPath . '/mail.log';
        $line    = sprintf(
            "[%s] TO:%s SUBJECT:%s ERROR:%s\n",
            date('Y-m-d H:i:s'),
            $to,
            $subject,
            $error
        );

        // Silenciar error de escritura para no romper el flujo principal
        @file_put_contents($file, $line, FILE_APPEND | LOCK_EX);
    }
}

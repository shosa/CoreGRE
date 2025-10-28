<?php
/**
 * Error Controller
 * Gestisce le pagine di errore dell'applicazione
 */

class ErrorController extends BaseController
{
    /**
     * Pagina 404 - Non trovato
     */
    public function notFound()
    {
        http_response_code(404);

        $data = [
            'pageTitle' => '404 - Pagina non trovata',
            'errorCode' => 404,
            'errorMessage' => 'La pagina che stai cercando non è stata trovata.',
            'backUrl' => $this->url('/')
        ];

        $this->render('error.404', $data);
    }

    /**
     * Pagina 403 - Accesso negato
     */
    public function forbidden()
    {
        http_response_code(403);

        $data = [
            'pageTitle' => '403 - Accesso negato',
            'errorCode' => 403,
            'errorMessage' => 'Non hai i permessi per accedere a questa risorsa.',
            'backUrl' => $this->url('/')
        ];

        $this->render('error.403', $data);
    }

    /**
     * Pagina 500 - Errore interno
     */
    public function serverError()
    {
        http_response_code(500);

        $data = [
            'pageTitle' => '500 - Errore del server',
            'errorCode' => 500,
            'errorMessage' => 'Si è verificato un errore interno del server.',
            'backUrl' => $this->url('/')
        ];

        $this->render('error.500', $data);
    }
}
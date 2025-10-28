<?php
/**
 * SCM Proxy Controller
 * Proxy controller per gestire il routing verso SCMPublicController
 * URL: /scm -> ScmController -> SCMPublicController
 */

class SCMController extends BaseController
{
    private $scmPublicController;
    
    public function __construct()
    {
        parent::__construct();
        $this->scmPublicController = new SCMPublicController();
    }
    
    /**
     * Index - Proxy to SCMPublicController::index()
     */
    public function index()
    {
        return $this->scmPublicController->index();
    }
    
    /**
     * Login - Proxy to SCMPublicController::login()
     */
    public function login()
    {
        return $this->scmPublicController->login();
    }
    
    /**
     * Dashboard - Proxy to SCMPublicController::dashboard()
     */
    public function dashboard()
    {
        return $this->scmPublicController->dashboard();
    }
    
    /**
     * Lavora - Proxy to SCMPublicController::lavora()
     */
    public function lavora($launchId = null)
    {
        return $this->scmPublicController->lavora($launchId);
    }
    
    /**
     * Update Progress - Proxy to SCMPublicController::updateProgress()
     */
    public function updateProgress($launchId = null)
    {
        return $this->scmPublicController->updateProgressSequence($launchId);
    }

    /**
     * Update Progress Sequence - Proxy to SCMPublicController::updateProgressSequence()
     */
    public function updateProgressSequence($launchId = null)
    {
        return $this->scmPublicController->updateProgressSequence($launchId);
    }

    /**
     * Test Endpoint - Proxy to SCMPublicController::testEndpoint()
     */
    public function testEndpoint($launchId = null)
    {
        return $this->scmPublicController->testEndpoint($launchId);
    }

    /**
     * Add Note - Proxy to SCMPublicController::addNote()
     */
    public function addNote($launchId = null)
    {
        return $this->scmPublicController->addNote($launchId);
    }
    
    /**
     * Logout - Proxy to SCMPublicController::logout()
     */
    public function logout()
    {
        return $this->scmPublicController->logout();
    }
}
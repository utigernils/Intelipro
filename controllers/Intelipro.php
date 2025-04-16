<?php
require_once 'utils/response.php';

class consoleController {

    private $session;
    private $consoleModell; 
    private $response;

    public function __construct($session, $dataModell) {
        $this->session = $session;
        $this->consoleModell = $dataModell;
        $this->response = new Response();
    }

    public function getConsole($consoleId) {
        $console = $this->consoleModell->get($consoleId);

        if ($console === false) {
            $this->response->error(message:'Your request was blocked due to invalid credentials');
        }
        
        if (!empty($console)) {
            $this->response->setHeader(200);

            $response = json_encode($console[0]);
            echo $response;
            exit();

        } else {
            $this->response->error(message:'Console not found', responseCode:404);
        }
    }

    public function updateConsole($consoleId) {
        $console = $this->consoleModell->get($consoleId);

        if (empty($console)) {
            $this->response->error(message:'Console not found', responseCode:404);
        }

        $jsonData = json_decode(file_get_contents('php://input'), true);

        if (empty($jsonData)) {
            $this->response->error(message:'No data provided', responseCode:400);
        }

        foreach ($jsonData as $field => $value) {
            $console = $this->consoleModell->set($consoleId, $field, $value);
            
            if ($console === false) {
                $this->response->error(message:'Your request was blocked due to invalid credentials');
            }
        }

        $this->response->message(message:'Console updated successfully', responseCode:200);

    }

    public function unlinkConsole($consoleId) {
        $console = $this->consoleModell->get($consoleId);

        if (empty($console)) {
            $this->response->error(message:'Console not found', responseCode:404);
        }

        $this->consoleModell->delete($consoleId);
        $this->response->message(message:'Console deleted successfully', responseCode:200);
    }

    public function getAllConsoles() {
        $orderBy = isset($_GET['orderBy']) ? $_GET['orderBy'] : null;
        $direction = isset($_GET['desc']) ? $_GET['desc'] : null;

        $console = $this->consoleModell->get(orderBy: $orderBy, desc: $direction);

        if ($console === false) {
            $this->response->error(message:'Your request was blocked due to invalid credentials');
        }
        
        if (!empty($console)) {
            $this->response->setHeader(200);

            $response = json_encode($console);
            echo $response;
            exit();

        } else {
            $this->response->error(message:'No Consoles found', responseCode:404);
        }
    }

    public function registerConsole() {
        $jsonData = json_decode(file_get_contents('php://input'), true);

        if (empty($jsonData)) {
            $this->response->error(message:'No data provided', responseCode:400);
        }
        
        if (!isset($jsonData['name'])) {
            $this->response->error(message:'Name is required', responseCode:400);
        }

        $name = $jsonData['name'];

        $result = $this->consoleModell->create($name, true);

        if ($result === false) {
            $this->response->error(message:'Console could not be created', responseCode:500);
        } else {
            $this->response->message(message:'Console created', responseCode:201);
        }
    }
}
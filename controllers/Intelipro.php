<?php
require_once 'utils/response.php';
require_once 'utils/deepseek.php';

class Intelipro
{
    private $projectModell;
    private $response;

    private $deepseek;

    public function __construct($dataModell, $deepseekKey)
    {
        $this->projectModell = $dataModell;
        $this->response = new Response();

        $this->deepseek = new deepseek($deepseekKey);
    }

    public function handleQuery()
    {
        $query = json_decode(file_get_contents('php://input'), true)['query'] ?? null;

        if (is_null($query)) {
            $this->response->error(message: 'Request does not contain query', responseCode: 400);
            exit();
        }

        $projects = array_map(function ($project) {
            return [$project['id'], $project['title']];
        }, $this->projectModell->get());

        if (empty($projects)) {
            $this->response->error(message: 'No projects loaded', responseCode: 500);
            exit();
        }

        $this->response->setHeader(200);

        $determinedProjectId = $this->determineProjectId($query, $projects);

        if ($determinedProjectId === 'none') {
            $this->response->error(message: 'You are not talking about any Project in my Database', responseCode: 404);
            exit();
        }

        $answer = $this->answerQuery($query, $determinedProjectId);

        $response = json_encode(['answer' => $answer, 'projectId' => $determinedProjectId]);
        echo $response;
        exit();
    }

    private function determineProjectId($query, $projects)
    {
        $sysPrompt = "Determine the project wich is being talked about from the following query. The possible projects are: " . json_encode($projects) . ".\n\n If none of them match, return 'none'. ONLY RETURN THE ID, DO NOT RETURN ANY OTHER TEXT.";

        $response = $this->deepseek->callDeepSeek(sysprompt: $sysPrompt, prompt: $query);
        return $response['choices'][0]['message']['content'];
    }

    private function answerQuery($query, $projectId, $anserSenteceCount = 4)
    {
        $projectDescription = $this->projectModell->get($projectId)[0]['long_description'] ?? null;

        if (is_null($projectDescription)) {
            $this->response->error(message: 'Project not found', responseCode: 500);
            exit();
        }

        $sysPrompt = "Answer the following question in about " . $anserSenteceCount . " sentences based on this project description: " . $projectDescription . " YOU CAN MAKE ASSUMPTIONS BUT DONT SOUND UNSURE. DONT USE 'probally' AND SIMILAR WORDS";

        $response = $this->deepseek->callDeepSeek(sysprompt: $sysPrompt, prompt: $query);
        return $response['choices'][0]['message']['content'];
    }

}



<?php

class ChatGPT
{
    private $API_KEY;
    private $textURL = "https://api.openai.com/v1/chat/completions";
    private $imageURL = "https://api.openai.com/v1/images/generations";

    public $curl;

    public function __construct()
    {
        $this->API_KEY = getenv('OPENAI_API_KEY');
        $this->curl = curl_init();
    }

    public function initialize($requestType = "text" || "image")
    {
        $this->curl = curl_init();

        if ($requestType === 'image')
            curl_setopt($this->curl, CURLOPT_URL, $this->imageURL);
        if ($requestType === 'text')
            curl_setopt($this->curl, CURLOPT_URL, $this->textURL);

        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_POST, true);

        $headers = array(
            "Content-Type: application/json",
            "Authorization: Bearer $this->API_KEY"
        );

        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $headers);
    }

    /**
     * Generates a text response based on the given prompt using the specified parameters.
     *
     * @param string $prompt The prompt for generating the text response.
     * @param string $model The GPT-3 model to use for text generation.
     * @param float $temperature The temperature parameter for controlling randomness (default: 0.7).
     * @param int $maxTokens The maximum number of tokens in the generated text (default: 1000).
     * @return array An array containing 'data' and 'error' keys, representing the generated text and any errors.
     */
    public function createTextRequest($prompt, $model = 'gpt-4', $temperature = 0.7, $maxTokens = 500)
    {
        curl_reset($this->curl);
        $this->initialize('text');

        $data = [
            "model" => $model,
            "messages" => [
                [
                    "role" => "user",
                    "content" => $prompt
                ]
            ],
            "temperature" => $temperature,
            "max_tokens" => $maxTokens
        ];

        curl_setopt($this->curl, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($this->curl);
        $response = json_decode($response, true);

        $output['data'] = $response['choices'][0]['message']['content'] ?? null;
        $output['error'] = $response['error']['code'] ?? null;
        return $output;
    }

    public function generateActivityDescription($prompt, $uuid)
    {
        $question = "Vytvoř krátký popisek z JSON aktivity pro školy která je zde: " . $prompt . "Tento popisek bude následně použit pro vyhledávání které taky dělá AI a uživatel díky tomu najde svoji aktivitu jednodušeji. Následně k tomu taky připiš věkovou skupinu pro kterou to celkově je. Měj napaměti že tvoje odpověď bude uložena do databáze a další AI ji bude číst a proto je důležité aby obsahovala jen relevantní informace a jednala se o čistý popisek. MAX. povolená délka popisku je 50 slov";
        // Insert the description into the database
        $response = $this->createTextRequest($question);
        
        // Create connection
        global $conn;
        // Prepare an insert statement
        $stmt = $conn->prepare("UPDATE activities SET aiDescription = ? WHERE uuid = ?");
        $stmt->bind_param("ss", $response['data'], $uuid);
        $stmt->execute();
        $stmt->close();
    }

    public function retrieveMostRelevant($prompt)
    {
        // Retrieve all activity descriptions from the database
        global $conn;
        $stmt = $conn->prepare("SELECT uuid, aiDescription FROM activities");
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        $descriptions = [];
        while ($row = $result->fetch_assoc()) {
            if ($row['aiDescription'] !== null) {
                $descriptions[] = [
                    'uuid' => $row['uuid'],
                    'aiDescription' => $row['aiDescription'],
                ];
            }
        }
        // Generate the most relevant activity description based on the prompt
        $jsonExample = '[{"uuid":"<uuid>","relevant":"70%","reason":"Fotbal je vhodný pro větší skupiny a vyžaduje minimální vybavení"},{"uuid":"<uuid>","relevant":"20%","reason":"Tato hra je vhodná pro menší skupiny a může být zábavným způsobem, jak zapojit žáky do aktivního učení."},{"uuid":"<uuid>","relevant":"10%","reason":"Tato hra může být vhodná pro menší skupiny, ale vyžaduje specifické znalosti v oblasti chemie, které by mohly být omezující pro všechny žáky ve třídě."}]';
        $question = "Here is list of descriptions for school subject in JSON: " . json_encode($descriptions) . "Please return three most relevant activities for following text: " . $prompt . " List them in the order from most relevant to the least relevant. For each result, add relevance in percent and short reason for selection in Czech language. Provide results in and put them into JSON format. The final result will look like this: " . $jsonExample . " Make sure that the response will be raw JSON data AND DO NOT CHANGE THE UUID, KEEP IT STILL THE SAME.";

        $response = $this->createTextRequest($question, 'gpt-3.5-turbo', 0.7, 500);

        // Get the uuid and reason of activities from the response
        $activities = json_decode($response['data'], true);

        return $activities;
    }

    /**
     * Generates an image URL based on the given prompt and parameters.
     *
     * @param string $prompt The prompt for generating the image URL.
     * @param string $imageSize The desired image size (default: '512x512').
     * @param int $numberOfImages The number of images to generate (default: 1).
     * @return array An array containing ['data'] and ['error'] keys, representing the generated image URL and any errors.
     */
    public function generateImage($prompt, $imageSize = '512x512', $numberOfImages = 1)
    {
        curl_reset($this->curl);
        $this->initialize('image');

        $data["prompt"] = $prompt;
        $data["n"] = $numberOfImages;
        $data["size"] = $imageSize;

        curl_setopt($this->curl, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($this->curl);
        $response = json_decode($response, true);

        $output['data'] = $response['data'][0]['url'] ?? null;
        $output['error'] =  $response['error']['code'] ?? null;
        return $output;
    }
}
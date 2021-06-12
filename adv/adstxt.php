<?php

/**
 * AdsTxt feature PV-150
 */
class AdsTxt {

    /**
     * Execute the feature
     *
     * @return void
     */
    public function run() {
        $scans = [];
        if (!$this->isValid()) {
            return $this->doResponse($scans);
        }
        set_time_limit(300);
        $_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        $rows   = $_POST['rows'];
        $record = $_POST['txt'];

        $contents = $this->getSitesContent($rows);
        foreach ($contents as $i => $content) {
            $scans[] = $this->performSiteScanFromContent($rows[$i], $content, $record);
        }

        return $this->doResponse($scans);
    }

    /**
     * Return the details of the content scan
     *
     * @param array $row
     * @param string $content
     * @param string $text
     * @return array
     */
    private function performSiteScanFromContent(array $row, string $content, string $text): array {
        $hasString = $this->contentHasText($content, $text);

        $scan = [
            "url" => $row['url'],
            "id" => $row['index'],
            "total" => 1, // always is one text to find
            "success" => +$hasString,
            "missing" => +!$hasString,
            "scanned_at" => date(DATE_ATOM)
        ];

        if(!$hasString) {
            $scan['missing_lines'] = $text;
        }

        return $scan;
    }

    /**
     * Check if the content has the text.
     *
     * @param string $content
     * @param string $text
     * @return boolean
     */
    private function contentHasText(string $content, string $text): bool {
        $text = $this->cleanRecord($text);

        $records = explode("\n", $content);
        foreach ($records as $record) {
            $record = $this->cleanRecord($record);

            // If it doesn't have the text OR it's a comment
            if (!preg_match("/\b$text\b/", $record) || preg_match("/#$text/", $record)) {
                continue;
            }

            return true;
        }

        return false;
    }

    /**
     * Receives a text string, then:
     * removes whitespace, sets the case to lowercase,
     * and trims a trailing comma if present
     *
     * @param string $text
     * @return string
     */
    private function cleanRecord(string $text): string {
        $text = strtolower($text);
        $text = preg_replace('/\s+/', '', $text);

        return rtrim($text, ',');
    }

    /**
     * Get the site contents based in a list of URL's
     *
     * @param array $rows array of urls ['url' => 'https://domain/ads.txt', 'index' => 1]
     * @return array
     */
    private function getSitesContent(array $rows): array {
        stream_context_set_default([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ]);
        $node_count = count($rows);
        $curl_arr = [];
        $master = curl_multi_init();

        foreach ($rows as $i => $row) {
            $curl_arr[$i] = curl_init($row['url']);
            curl_setopt($curl_arr[$i], CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($curl_arr[$i], CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curl_arr[$i], CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl_arr[$i], CURLOPT_TIMEOUT_MS, 10000);
            curl_setopt($curl_arr[$i], CURLOPT_FOLLOWLOCATION, true);
            curl_multi_add_handle($master, $curl_arr[$i]);
        }

        do {
            curl_multi_exec($master, $running);
        } while ($running > 0);

        $results = [];
        for ($i = 0; $i < $node_count; ++$i) {
            $results[] = curl_multi_getcontent($curl_arr[$i]);
            curl_multi_remove_handle($master, $curl_arr[$i]);
        }

        curl_multi_close($master);

        return $results;
    }

    /**
     * Returns true if is a valid request
     *
     * @return boolean
     */
    private function isValid() : bool {
        return $_SERVER['REQUEST_METHOD'] === 'POST' || isset($_POST['rows'], $_POST['txt']);
    }

    /**
     * Print the json response
     *
     * @param array $result
     * @return void
     */
    private function doResponse(array $result) {
        $http_origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : false;
        if ($http_origin) {
            header("Access-Control-Allow-Origin: $http_origin");
        } else {
            header('Access-Control-Allow-Origin: *');
        }
        header('Content-Type: application/json');
        echo json_encode($result);
    }
}

(new AdsTxt())->run();

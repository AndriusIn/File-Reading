<?php declare(strict_types = 1);

namespace App\Controller;

class FileController
{
    /**
     * Gets associative array from a given XML file.
     * 
     * @param string $filePath Full file path.
     * @return mixed The associative array on success, or boolean false on failure.
     */
    public function getAssociativeArrayFromXml(string $filePath)
    {
        if (file_exists($filePath) && is_file($filePath)) {
            $fileContent = file_get_contents($filePath);

            // Remove empty lines from file content string
            $fileContent = preg_replace('~[\r\n]+~', "\r\n", trim($fileContent));

            // Suppress all XML errors
            libxml_use_internal_errors(true);

            // Try to load XML string
            $xmlObject = simplexml_load_string($fileContent);

            // Convert XML object into JSON string and get associative array on success
            if ($xmlObject !== FALSE) {
                $json = json_encode($xmlObject);
                $associativeArray = json_decode($json, true);
                return $associativeArray;
            }
        }

        // Return false if file doesn't exist
        return false;
    }

    /**
     * Gets associative array from a given JSON file.
     * 
     * @param string $filePath Full file path.
     * @return mixed The associative array on success, or boolean false on failure.
     */
    public function getAssociativeArrayFromJson(string $filePath)
    {
        if (file_exists($filePath) && is_file($filePath)) {
            $fileContent = file_get_contents($filePath);

            // Remove empty lines from file content string
            $fileContent = preg_replace('~[\r\n]+~', "\r\n", trim($fileContent));

            // Try to decode JSON string
            $associativeArray = json_decode($fileContent, true);

            if ($associativeArray !== NULL) {
                return $associativeArray;
            }
        }

        // Return false if file doesn't exist
        return false;
    }

    /**
     * Gets associative array from a given CSV file.
     * 
     * @param string $filePath Full file path.
     * @return mixed The associative array on success, or boolean false on failure.
     */
    public function getAssociativeArrayFromCsv(string $filePath)
    {
        if (file_exists($filePath) && is_file($filePath)) {
            $fileContent = file_get_contents($filePath);

            // Remove empty lines from file content string
            $fileContent = preg_replace('~[\r\n]+~', "\r\n", trim($fileContent));

            $associativeArray = array();

            // Check if file handle is valid
            if (($handle = fopen($filePath, "r")) !== FALSE) {
                // Get associative array keys (first line)
                if (($keys = fgetcsv($handle, 0, ',', "'")) !== FALSE) {
                    // Get data line by line
                    while (($data = fgetcsv($handle, 0, ',', "'")) !== FALSE) {
                        // Check if data field count is valid
                        if (count($data) === count($keys)) {
                            // Push data fields to array
                            $fieldValues = array();
                            for ($i = 0; $i < count($data); $i++) {
                                $fieldValues[$keys[$i]] = $data[$i];
                            }

                            // Append main associative array
                            $associativeArray[] = $fieldValues;
                        } else {
                            fclose($handle);
                            
                            // Return false if CSV file contains invalid line
                            return false;
                        }
                    }
                }
                fclose($handle);
            }

            // Return false if associative array is empty
            if (empty($associativeArray)) {
                return false;
            } else {
                // CSV success
                return $associativeArray;
            }
        }

        // Return false if file doesn't exist
        return false;
    }

    /**
     * Gets associative array from a given file.
     * 
     * @param string $filePath Full file path.
     * @return mixed The associative array on success, or boolean false on failure.
     */
    public function getAssociativeArray(string $filePath)
    {
        if (file_exists($filePath) && is_file($filePath)) {
            // Return associative array on XML success
            $associativeArray = $this->getAssociativeArrayFromXml($filePath);
            if ($associativeArray !== FALSE) {
                return $associativeArray;
            }

            // Return associative array on JSON success
            $associativeArray = $this->getAssociativeArrayFromJson($filePath);
            if ($associativeArray !== FALSE) {
                return $associativeArray;
            }

            // Return associative array on CSV success
            $associativeArray = $this->getAssociativeArrayFromCsv($filePath);
            if ($associativeArray !== FALSE) {
                return $associativeArray;
            }
        }

        // Return false if file doesn't exist 
        return false;
    }
}

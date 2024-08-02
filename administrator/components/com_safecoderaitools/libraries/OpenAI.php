<?php

/**
 * @package     SafeCoder AI Tools
 * @subpackage  com_safecoderaitools
 * 
 * @version     1.0.0
 * 
 * @author      Miron Savan <hello@safecoder.com>
 * @link        https://www.safecoder.com/aitools
 * @copyright   Copyright (C) 2012 SafeCoder Software SRL (RO30786660)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later; see LICENSE.txt
 */

namespace SafeCoderSoftwareAITools\Libraries;

class OpenAI
{
    /**
     * 
     * Process Open AI Request
     *
     * @param [type] $PromptData - Prompt Data Object
     * @param [type] $config - Config Object
     * @return boolean
     * 
     * @since 1.0.0
     * @version 1.0.0
     * 
     */
    public static function processRequest($PromptData, $config)
    {

        try {

            if (!$config instanceof Config) {
                return false;
            }


            if (empty($config->getOpenAIAPIKey())) {
                return false;
            }

            if (!$PromptData instanceof PromptData) {
                return false;
            }

            $promptSize = self::CalculateMaxTokens($PromptData, $PromptData->OpenAIMaxTokens, $PromptData->OpenAIModel);

            $url = 'https://api.openai.com/v1/completions';

            $data = [
                'model' => $PromptData->OpenAIModel,
                'prompt' => $PromptData->FullPrompt,
                'max_tokens' => $promptSize,
                'n' => $PromptData->OpenAIIterations,
                'stop' => null,
                'temperature' => $PromptData->OpenAITemperature,
                'top_p' => $PromptData->OpenAITop_P,
                'frequency_penalty' => $PromptData->OpenAIFrequencyPenalty,
                'presence_penalty' => $PromptData->OpenAIPresencePenalty,
                'echo' => false,
            ];

            $jsonData = json_encode($data);

            $headers = [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $config->getOpenAIAPIKey(),
            ];

            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonData);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

            $response = curl_exec($curl);

            $result = false;

            if ($response === false) {
                $PromptData->RawResponse = curl_error($curl);
                $PromptData->IsProcessed = 1;
                $PromptData->IsOK = 0;
                $PromptData->Save();
            } else {

                $PromptData->RawResponse = $response;

                if (Utility::isJson($response)) {

                    $responseArr = \json_decode($response, true);

                    if (Utility::IsValidNonEmptyArray($responseArr, 'error') == true) {
                        $PromptData->IsProcessed = 1;
                        $PromptData->IsOK = 0;
                    } else {
                        $PromptData->IsProcessed = 1;
                        $PromptData->IsOK = 1;
                        $result = true;
                    }
                } else {
                    $PromptData->IsProcessed = 1;
                    $PromptData->IsOK = 0;
                }

                $PromptData->Save();
            }

            curl_close($curl);

            return $result;
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * 
     * Process Temperature Value for Open AI
     *
     * @param integer $value
     * @return float
     * 
     * @since 1.0.0
     * @version 1.0.1
     * 
     */
    public static function ProcessTemperatureValue($value = 1)
    {

        try {
            if (!is_string($value) && !is_numeric($value)) {
                return 1;
            }

            $value = floatval($value);

            if (!is_float($value) && !is_int($value)) {
                return 1;
            }

            if ($value < -2) {
                $value = -2;
            } else if ($value > 2) {
                $value = 2;
            }

            return $value;
        } catch (\Throwable $th) {
            return 1;
        }
    }

    /**
     * 
     * Check if model is allowed
     *
     * @param string $value
     * @return string
     * 
     * @since 1.0.0
     * @version 1.0.0
     * 
     */
    public static function ProcessModelValue($value = 'text-davinci-003')
    {

        try {

            $AllowedModels = array('text-davinci-003', 'text-davinci-002', 'text-curie-001', 'text-babbage-001', 'text-ada-001');

            if (!\in_array($value, $AllowedModels)) {
                return 'text-davinci-003';
            }

            return $value;
        } catch (\Throwable $th) {
            return 'text-davinci-003';
        }
    }

    /**
     * 
     * Process Max Tokens
     *
     * @param integer $value
     * @return integer
     * 
     * @since 1.0.0
     * @version 1.0.0
     * 
     */
    public static function ProcessMaxTokens($value = 100, $model = 'text-davinci-003')
    {

        $maxValue = 4097;
        if (in_array($model, array('text-curie-001', 'text-babbage-001', 'text-ada-001'))) {
            $maxValue = 2049;
        }

        try {

            $value = \intval($value);

            if ($value < 1) {
                return $maxValue;
            }

            if ($value > $maxValue) {
                return $maxValue;
            }

            return $value;
        } catch (\Throwable $th) {
            return $maxValue;
        }
    }

    /**
     * 
     * Process Top_P Value for Open AI
     *
     * @param integer $value
     * @return float
     * 
     * @since 1.0.0
     * @version 1.0.0
     * 
     */
    public static function ProcessTop_PValue($value = 1)
    {

        try {

            if (!is_string($value) && !is_numeric($value)) {
                return 1;
            }

            $value = floatval($value);

            if (!is_float($value) && !is_int($value)) {
                return 1;
            }

            if ($value < 0) {
                $value = 0;
            } else if ($value > 1) {
                $value = 1;
            }

            return $value;
        } catch (\Throwable $th) {
            return 1;
        }
    }

    /**
     * 
     * Process Top_P Value for Open AI
     *
     * @param integer $value
     * @return float
     * 
     * @since 1.0.0
     * @version 1.0.1
     * 
     */
    public static function ProcessNValue($value = 1)
    {

        try {

            if (!is_string($value) && !is_numeric($value)) {
                return 1;
            }

            $value = \intval(round($value));

            if (!is_float($value) && !is_int($value)) {
                return 1;
            }

            if ($value < 1) {
                $value = 1;
            } else if ($value > 5) {
                $value = 5;
            }

            return (int) $value;
        } catch (\Throwable $th) {
            return 1;
        }
    }

    /**
     * Process Frequency Penalty Value for Open AI
     *
     * @param integer $value
     * @return float
     * 
     * @since 1.0.0
     * @version 1.0.0
     * 
     */
    public static function ProcessFrequencyPenaltyValue($value = 0)
    {

        try {

            if (!is_string($value) && !is_numeric($value)) {
                return 0;
            }

            $value = floatval($value);

            if (!is_float($value) && !is_int($value)) {
                return 0;
            }

            if ($value < -2) {
                $value = -2;
            } else if ($value > 2) {
                $value = 2;
            }

            return $value;
        } catch (\Throwable $th) {
            return 0;
        }
    }

    /**
     * 
     * Process Presence Penalty Value for Open AI
     *
     * @param integer $value
     * @return float
     * 
     * @since 1.0.0
     * @version 1.0.0
     * 
     */
    public static function ProcessPresencePenaltyValue($value = 0)
    {

        try {

            if (!is_string($value) && !is_numeric($value)) {
                return 0;
            }

            $value = floatval($value);

            if (!is_float($value) && !is_int($value)) {
                return 0;
            }

            if ($value < -2) {
                $value = -2;
            } else if ($value > 2) {
                $value = 2;
            }

            return $value;
        } catch (\Throwable $th) {
            return 0;
        }
        
    }

    public static function CalculateMaxTokens(PromptData $PromptData, $maxTokens = 100, $model = 'text-davinci-003') {

        if(empty($PromptData->FullPrompt)) {

            $PromptData->RawResponse = General::BuildErrorArrayString('Your prompt is empty. We cannot make the request', 'Invalid Prompt Content', 500);
            $PromptData->IsProcessed = 1;
            $PromptData->IsOK = 0;
            $PromptData->Save();

            return 0;
        }

        $promptSize = GPTEncoder::GetTokensBasedOnString($PromptData->FullPrompt);
        if(in_array($model, array('text-curie-001', 'text-babbage-001', 'text-ada-001'))) {
            $maxPrompt = 2049;
        }
        else {
            $maxPrompt = 4097;
        }

        $maxMaxTokens = $maxPrompt - $promptSize - 100;

        if($maxTokens > $maxMaxTokens) {
            $maxTokens = $maxMaxTokens;
        }

        if($promptSize > $maxPrompt) {

            $PromptData->RawResponse = General::BuildErrorArrayString('Your prompt size was: ' . $promptSize . ' tokens. Request is limited to: ' . $maxPrompt . ' tokens. You cannot submit a prompt with more tokens than the maximum allowed size.', 'Invalid Prompt Size', 500);
            $PromptData->IsProcessed = 1;
            $PromptData->IsOK = 0;
            $PromptData->Save();

            return 0;
        }

        return $maxTokens;

    }
}

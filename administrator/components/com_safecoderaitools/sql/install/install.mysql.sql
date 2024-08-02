CREATE TABLE IF NOT EXISTS `#__safecoder_ai_tools_prompt` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ArticleID` int(11) NOT NULL DEFAULT 0,
  `CategoryID` int(11) NOT NULL DEFAULT 0,
  `UserID` int(11) NOT NULL DEFAULT 0,
  `FullName` varchar(255) NOT NULL DEFAULT '',
  `UserInput` longtext DEFAULT NULL,
  `RawResponse` longtext DEFAULT NULL,
  `IsProcessed` int(1) NOT NULL DEFAULT 0,
  `IsOK` int(1) NOT NULL DEFAULT 0,
  `CompletionID` varchar(255) NOT NULL DEFAULT '',
  `CompletionModel` varchar(255) NOT NULL DEFAULT '',
  `CompletionPromptTokens` int(11) NOT NULL DEFAULT 0,
  `CompletionTokens` int(11) NOT NULL DEFAULT 0,
  `CompletionTotalTokens` int(11) NOT NULL DEFAULT 0,
  `OpenAIModel` varchar(255) NOT NULL DEFAULT '',
  `OpenAIMaxTokens` int(11) NOT NULL DEFAULT 0,
  `OpenAITemperature` float(11,2) NOT NULL DEFAULT 0.00,
  `OpenAITop_P` float(11,2) NOT NULL DEFAULT 0.00,
  `OpenAIIterations` int(11) NOT NULL DEFAULT 0,
  `OpenAIPresencePenalty` float(11,2) NOT NULL DEFAULT 0.00,
  `OpenAIFrequencyPenalty` float(11,2) NOT NULL DEFAULT 0.00,
  `PromptContext` longtext DEFAULT NULL,
  `FullPrompt` longtext DEFAULT NULL,
  `Date` datetime(6) NOT NULL DEFAULT current_timestamp(6),
  `DateUpdated` datetime(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6),
  PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `#__safecoder_ai_tools_prompt_choices` (
  `PromptID` int(11) NOT NULL DEFAULT 0,
  `Text` longtext DEFAULT NULL,
  `Index` int(11) NOT NULL DEFAULT 0,
  `FinishReason` varchar(255) NOT NULL DEFAULT ''
);
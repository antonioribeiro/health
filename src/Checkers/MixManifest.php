<?php

namespace PragmaRX\Health\Checkers;

use Exception;
use Illuminate\Support\Str;
use PragmaRX\Health\Support\Result;

class MixManifest extends Base
{
    /**
     * Check resource.
     *
     * @return Result
     *
     * @throws \Exception
     */
    public function check()
    {
        $this->checkFilePresence($file = $this->getManifestFileName());

        $loaded = $this->loadJson($file);

        foreach ($loaded as $item => $asset) {
            if (! $this->ignored($item)) {
                $this->checkFilePresence(base_path($this->target->assetsRoot.$asset));
            }
        }

        return $this->makeHealthyResult();
    }

    /**
     * Get the mix manifest file name.
     *
     * @return string
     */
    public function getManifestFileName()
    {
        $file = $this->target->file;

        if (Str::startsWith($file, '/')) {
            return $file;
        }

        return base_path($file);
    }

    /**
     * Check presence of a file in disk.
     *
     * @return string
     */
    public function checkFilePresence($fileName)
    {
        if (! file_exists($fileName)) {
            throw new Exception("File doesn't exist: ".$fileName);
        }
    }

    /**
     * Load manifest.
     *
     * @return string
     */
    public function loadJson($fileName)
    {
        $contents = file_get_contents($fileName);

        if (blank($fileName)) {
            throw new Exception('Manifest is empty or permission to read the file was denied: '.$fileName);
        }

        $contents = json_decode($contents, true);

        if (json_last_error() !== \JSON_ERROR_NONE) {
            throw new Exception('Error parsing manifest: '.$fileName.' - Error: '.$this->getJSONErrorMessage(json_last_error()));
        }

        return $contents;
    }

    /**
     * Snippet took from Symfony/Translation.
     *
     * Translates JSON_ERROR_* constant into meaningful message.
     */
    private function getJSONErrorMessage(int $errorCode): string
    {
        switch ($errorCode) {
            case \JSON_ERROR_DEPTH:
                return 'Maximum stack depth exceeded';
            case \JSON_ERROR_STATE_MISMATCH:
                return 'Underflow or the modes mismatch';
            case \JSON_ERROR_CTRL_CHAR:
                return 'Unexpected control character found';
            case \JSON_ERROR_SYNTAX:
                return 'Syntax error, malformed JSON';
            case \JSON_ERROR_UTF8:
                return 'Malformed UTF-8 characters, possibly incorrectly encoded';
            default:
                return 'Unknown error';
        }
    }

    /**
     * Load manifest.
     *
     * @return string
     */
    public function ignored($item)
    {
        if (! isset($this->target->ignoreItems)) {
            return false;
        }

        return $this->target->ignoreItems->contains($item);
    }
}

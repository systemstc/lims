<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait HasManuscriptContent
{
    /**
     * Extracts base64 images from HTML content, saves them as files,
     * and replaces base64 strings with public URLs.
     *
     * @param string|null $content
     * @param string $folder
     * @return string|null
     */
    protected function processManuscriptImages($content, $folder = 'manuscripts')
    {
        if (empty($content)) {
            return $content;
        }

        // Use DOMDocument to parse HTML and find images
        $dom = new \DOMDocument();
        
        // Suppress errors for malformed HTML
        libxml_use_internal_errors(true);
        
        // Load HTML with UTF-8 encoding
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        
        libxml_clear_errors();

        $images = $dom->getElementsByTagName('img');
        $changed = false;

        foreach ($images as $img) {
            $src = $img->getAttribute('src');

            // Check if it's a base64 image
            if (preg_match('/^data:image\/(\w+);base64,/', $src, $type)) {
                $changed = true;
                $extension = strtolower($type[1]); // e.g., png, jpg, jpeg
                
                // Decode base64 data
                $data = substr($src, strpos($src, ',') + 1);
                $data = base64_decode($data);

                if ($data === false) {
                    continue;
                }

                // Generate a unique filename
                $filename = Str::uuid() . '.' . $extension;
                $path = $folder . '/' . $filename;

                // Save to storage (public disk)
                Storage::disk('public')->put($path, $data);

                // Update src attribute to public URL
                $url = Storage::disk('public')->url($path);
                $img->setAttribute('src', $url);
            }
        }

        if ($changed) {
            // Save the modified HTML
            $content = $dom->saveHTML();
            
            // Remove the XML encoding declaration if it was added
            $content = str_replace('<?xml encoding="utf-8" ?>', '', $content);
        }

        return $content;
    }
}

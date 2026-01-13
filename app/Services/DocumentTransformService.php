<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use DOMDocument;
use DOMXPath;

class DocumentTransformService
{
    /**
     * Transform HTML document to clean lesson content
     */
    public function transformHtmlToLesson(string $htmlPath, array $options = []): array
    {
        if (!file_exists($htmlPath)) {
            throw new \Exception("HTML file not found: {$htmlPath}");
        }

        $html = file_get_contents($htmlPath);
        
        // Parse HTML
        $doc = new DOMDocument();
        @$doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        
        // Extract title
        $title = $this->extractTitle($doc);
        
        // Extract images first (before cleaning)
        $images = $this->extractImages($doc, dirname($htmlPath), $html);
        
        // Clean and extract body content
        $content = $this->cleanHtmlContent($doc);
        
        // Replace image paths in content with storage URLs
        $content = $this->replaceImagePaths($content, $images);
        
        // Extract external links (YouTube, etc)
        $externalLinks = $this->extractExternalLinks($content);
        
        // Estimate reading time
        $readingTime = $this->estimateReadingTime($content);
        
        // Copy attachments if exist
        $attachments = [];
        $attachmentDir = dirname($htmlPath) . DIRECTORY_SEPARATOR . pathinfo($htmlPath, PATHINFO_FILENAME) . '_files';
        if (is_dir($attachmentDir)) {
            $attachments = $this->copyAttachments($attachmentDir);
        }
        
        return [
            'title' => $title,
            'content' => $content,
            'images' => $images,
            'attachments' => $attachments,
            'external_links' => $externalLinks,
            'reading_time' => $readingTime,
            'type' => 'text', // or 'document'
        ];
    }

    /**
     * Extract title from HTML document
     */
    protected function extractTitle(DOMDocument $doc): string
    {
        $xpath = new DOMXPath($doc);
        
        // Try h1 first
        $h1 = $xpath->query('//h1');
        if ($h1->length > 0) {
            $title = trim($h1->item(0)->textContent);
            if (!empty($title)) {
                return $title;
            }
        }
        
        // Try title tag
        $titleTag = $xpath->query('//title');
        if ($titleTag->length > 0) {
            $title = trim($titleTag->item(0)->textContent);
            if (!empty($title)) {
                return $title;
            }
        }
        
        // Try first paragraph with bold and large font (MS Word pattern)
        $boldLargeP = $xpath->query('//p[.//b or .//strong][contains(., "Pengantar") or contains(., "Bab") or contains(., "Chapter")]');
        if ($boldLargeP->length > 0) {
            $title = trim($boldLargeP->item(0)->textContent);
            $title = preg_replace('/\s+/', ' ', $title); // Normalize whitespace
            if (!empty($title) && strlen($title) < 200) {
                return $title;
            }
        }
        
        // Try p with large font (20pt or larger)
        $largeP = $xpath->query('//p[@class="MsoTitle"] | //p[contains(@style, "font-size:20") or contains(@style, "font-size:18")]');
        if ($largeP->length > 0) {
            $title = trim($largeP->item(0)->textContent);
            $title = preg_replace('/\s+/', ' ', $title); // Normalize whitespace
            if (!empty($title) && strlen($title) < 200) {
                return $title;
            }
        }
        
        // Try first bold paragraph with reasonable length
        $firstBold = $xpath->query('//p[.//b or .//strong]');
        if ($firstBold->length > 0) {
            $title = trim($firstBold->item(0)->textContent);
            $title = preg_replace('/\s+/', ' ', $title); // Normalize whitespace
            if (!empty($title) && strlen($title) > 5 && strlen($title) < 200) {
                return $title;
            }
        }
        
        return 'Untitled Document';
    }

    /**
     * Clean HTML content - remove MS Word clutter
     */
    protected function cleanHtmlContent(DOMDocument $doc): string
    {
        $xpath = new DOMXPath($doc);
        
        // Remove style tags
        $styles = $xpath->query('//style');
        foreach ($styles as $style) {
            $style->parentNode->removeChild($style);
        }
        
        // Remove script tags
        $scripts = $xpath->query('//script');
        foreach ($scripts as $script) {
            $script->parentNode->removeChild($script);
        }
        
        // Remove XML declarations
        $xmls = $xpath->query('//xml');
        foreach ($xmls as $xml) {
            $xml->parentNode->removeChild($xml);
        }
        
        // Get body content
        $body = $xpath->query('//body | //div[@class="WordSection1"]');
        if ($body->length === 0) {
            return '';
        }
        
        $content = $doc->saveHTML($body->item(0));
        
        // Clean MS Word specific classes and styles
        $content = preg_replace('/<(\/?)o:p>/i', '', $content); // Remove <o:p> tags
        $content = preg_replace('/class="Mso[^"]*"/i', '', $content); // Remove MsoNormal classes
        $content = preg_replace('/style="[^"]*mso-[^"]*"/i', '', $content); // Remove mso-* styles
        $content = preg_replace('/\s+style=""/', '', $content); // Remove empty style attributes
        $content = preg_replace('/<span[^>]*>\s*<\/span>/i', '', $content); // Remove empty spans
        
        // Convert YouTube links to embed
        $content = $this->convertYouTubeLinksToEmbed($content);
        
        // Clean up whitespace
        $content = preg_replace('/\s+/', ' ', $content);
        $content = preg_replace('/>(\s+)</m', '><', $content);
        
        return trim($content);
    }

    /**
     * Extract images from HTML and copy to storage
     */
    protected function extractImages(DOMDocument $doc, string $basePath, string $rawHtml = ''): array
    {
        $images = [];
        $xpath = new DOMXPath($doc);
        
        // Get regular img tags from DOM
        $imgTags = $xpath->query('//img');
        
        foreach ($imgTags as $img) {
            $src = $img->getAttribute('src');
            if (!empty($src)) {
                $this->processImage($src, $basePath, $images);
            }
        }
        
        // Get VML imagedata tags using local-name (no namespace needed)
        $vmlTags = $xpath->query('//*[local-name()="imagedata"]');
        
        foreach ($vmlTags as $vml) {
            $src = $vml->getAttribute('src');
            if (!empty($src)) {
                $this->processImage($src, $basePath, $images);
            }
        }
        
        // Also extract from raw HTML using regex (for conditional comments)
        if (!empty($rawHtml)) {
            // Match src="path" or src='path' in imagedata and img tags
            preg_match_all('/(?:src=["\']([^"\']*)["\'\s])/i', $rawHtml, $matches);
            if (!empty($matches[1])) {
                foreach ($matches[1] as $src) {
                    // Only process image files
                    if (preg_match('/\.(jpg|jpeg|png|gif|bmp|webp)$/i', $src)) {
                        $this->processImage($src, $basePath, $images);
                    }
                }
            }
        }
        
        return $images;
    }

    /**
     * Process and copy a single image
     */
    protected function processImage(string $src, string $basePath, array &$images): void
    {
        // Check if already processed
        foreach ($images as $existing) {
            if ($existing['original'] === $src) {
                return; // Skip duplicates
            }
        }
        
        // Get full path
        $fullPath = $basePath . '/' . $src;
        
        if (file_exists($fullPath)) {
            // Generate unique filename
            $filename = Str::uuid() . '.' . pathinfo($fullPath, PATHINFO_EXTENSION);
            $storagePath = 'lessons/images/' . $filename;
            
            // Copy to storage
            $content = file_get_contents($fullPath);
            Storage::disk('public')->put($storagePath, $content);
            
            $images[] = [
                'original' => $src,
                'url' => Storage::url($storagePath),
                'path' => $storagePath,
            ];
        }
    }

    /**
     * Replace image paths in HTML content with storage URLs
     */
    protected function replaceImagePaths(string $content, array $images): string
    {
        foreach ($images as $image) {
            // Replace both src and v:shapes references
            $content = str_replace($image['original'], $image['url'], $content);
            // Also handle HTML entities
            $content = str_replace(htmlentities($image['original']), $image['url'], $content);
        }
        
        return $content;
    }

    /**
     * Extract external links (YouTube, etc)
     */
    protected function extractExternalLinks(string $content): array
    {
        $links = [];
        
        // Extract YouTube links
        preg_match_all('/https?:\/\/(www\.)?(youtube\.com|youtu\.be)\/[^\s<]+/i', $content, $matches);
        
        if (!empty($matches[0])) {
            foreach ($matches[0] as $url) {
                $videoId = $this->extractYouTubeVideoId($url);
                if ($videoId) {
                    $links[] = [
                        'type' => 'youtube',
                        'url' => $url,
                        'video_id' => $videoId,
                        'title' => 'YouTube Video',
                    ];
                }
            }
        }
        
        return $links;
    }

    /**
     * Convert YouTube links to iframe embeds
     */
    protected function convertYouTubeLinksToEmbed(string $content): string
    {
        // Pattern to match YouTube URLs
        $pattern = '/<p[^>]*>https?:\/\/(www\.)?(youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)(\?[^<]*)?(.*?)<\/p>/i';
        
        $content = preg_replace_callback($pattern, function($matches) {
            $videoId = $matches[3];
            return '<div class="video-container my-4">
                <iframe width="100%" height="315" 
                    src="https://www.youtube.com/embed/' . $videoId . '" 
                    frameborder="0" 
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                    allowfullscreen
                    class="rounded-xl">
                </iframe>
            </div>';
        }, $content);
        
        return $content;
    }

    /**
     * Extract YouTube video ID from URL
     */
    protected function extractYouTubeVideoId(string $url): ?string
    {
        preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $url, $matches);
        return $matches[1] ?? null;
    }

    /**
     * Estimate reading time in minutes
     */
    protected function estimateReadingTime(string $content): int
    {
        // Average reading speed: 200-250 words per minute
        $text = strip_tags($content);
        $wordCount = str_word_count($text);
        $minutes = ceil($wordCount / 225); // 225 words per minute average
        
        return max(1, $minutes); // Minimum 1 minute
    }

    /**
     * Copy attachments folder to storage
     */
    public function copyAttachments(string $sourcePath, string $destinationPrefix = 'lessons/attachments'): array
    {
        $copiedFiles = [];
        
        if (!is_dir($sourcePath)) {
            return $copiedFiles;
        }
        
        $files = File::allFiles($sourcePath);
        
        foreach ($files as $file) {
            $filename = $file->getFilename();
            $storagePath = $destinationPrefix . '/' . Str::uuid() . '_' . $filename;
            
            Storage::disk('public')->put($storagePath, file_get_contents($file->getRealPath()));
            
            $copiedFiles[] = [
                'original' => $filename,
                'path' => $storagePath,
                'url' => Storage::url($storagePath),
                'size' => $file->getSize(),
                'mime' => mime_content_type($file->getRealPath()),
            ];
        }
        
        return $copiedFiles;
    }
}

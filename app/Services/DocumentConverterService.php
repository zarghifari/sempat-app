<?php

namespace App\Services;

use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Settings;
use Illuminate\Support\Facades\Storage;

class DocumentConverterService
{
    /**
     * Convert DOCX/DOC file to HTML
     *
     * @param string $filePath
     * @return string
     */
    public function convertToHtml($filePath)
    {
        try {
            // Load the document
            $phpWord = IOFactory::load($filePath);
            
            // Create HTML writer
            $htmlWriter = IOFactory::createWriter($phpWord, 'HTML');
            
            // Save to temporary file
            $tempHtmlPath = storage_path('app/temp/' . uniqid() . '.html');
            
            // Ensure temp directory exists
            if (!file_exists(storage_path('app/temp'))) {
                mkdir(storage_path('app/temp'), 0755, true);
            }
            
            $htmlWriter->save($tempHtmlPath);
            
            // Read the HTML content
            $htmlContent = file_get_contents($tempHtmlPath);
            
            // Clean up HTML - extract body content only
            $htmlContent = $this->cleanHtmlContent($htmlContent);
            
            // Delete temporary file
            unlink($tempHtmlPath);
            
            return $htmlContent;
            
        } catch (\Exception $e) {
            \Log::error('Document conversion failed: ' . $e->getMessage());
            throw new \Exception('Failed to convert document: ' . $e->getMessage());
        }
    }
    
    /**
     * Convert PDF to text (basic extraction)
     *
     * @param string $filePath
     * @return string
     */
    public function extractPdfText($filePath)
    {
        // For PDF, we'll just store the file and show a viewer
        // Full PDF text extraction requires additional libraries like pdftotext
        return '<p><strong>PDF Document uploaded.</strong> File will be available for download.</p>';
    }
    
    /**
     * Clean and format HTML content
     *
     * @param string $html
     * @return string
     */
    private function cleanHtmlContent($html)
    {
        // Remove HTML, HEAD, and BODY tags to get only content
        $html = preg_replace('/<html[^>]*>/', '', $html);
        $html = preg_replace('/<\/html>/', '', $html);
        $html = preg_replace('/<head[^>]*>.*?<\/head>/is', '', $html);
        $html = preg_replace('/<body[^>]*>/', '', $html);
        $html = preg_replace('/<\/body>/', '', $html);
        
        // Remove style tags
        $html = preg_replace('/<style[^>]*>.*?<\/style>/is', '', $html);
        
        // Clean up excessive whitespace
        $html = preg_replace('/\s+/', ' ', $html);
        $html = preg_replace('/>\s+</', '><', $html);
        
        // Basic formatting improvements
        $html = str_replace('<p></p>', '', $html);
        
        return trim($html);
    }
    
    /**
     * Store uploaded document file
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $directory
     * @return string Path to stored file
     */
    public function storeDocument($file, $directory = 'lessons/documents')
    {
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs($directory, $filename, 'public');
        
        return $path;
    }
    
    /**
     * Delete document file
     *
     * @param string $path
     * @return bool
     */
    public function deleteDocument($path)
    {
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }
        
        return false;
    }
    
    /**
     * Get supported document extensions
     *
     * @return array
     */
    public function getSupportedExtensions()
    {
        return ['docx', 'doc', 'pdf'];
    }
}

<?php

namespace App\Services;

use App\Models\DocumentImport;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Element\AbstractElement;
use PhpOffice\PhpWord\Element\Text;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\Element\Image;
use PhpOffice\PhpWord\Element\ListItem;

class DocumentImportService
{
    protected $extractedImages = [];
    protected $imageCounter = 0;

    /**
     * Process the document import
     */
    public function processImport(DocumentImport $import): void
    {
        try {
            // Mark as processing
            $import->markAsProcessing();

            // Get the file path
            $filePath = Storage::disk('public')->path($import->file_path);

            if (!file_exists($filePath)) {
                throw new \Exception('File not found: ' . $filePath);
            }

            // Load the document
            $phpWord = IOFactory::load($filePath);

            // Extract metadata
            $metadata = $this->extractMetadata($phpWord);

            // Convert to HTML
            $htmlContent = $this->convertToHtml($phpWord);

            // Count words (approximate)
            $wordCount = $this->countWords($htmlContent);

            // Mark as completed with results
            $import->markAsCompleted([
                'html_content' => $htmlContent,
                'extracted_images' => $this->extractedImages,
                'metadata' => $metadata,
                'word_count' => $wordCount,
                'image_count' => count($this->extractedImages),
            ]);

        } catch (\Exception $e) {
            $import->markAsFailed($e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Extract document metadata
     */
    protected function extractMetadata($phpWord): array
    {
        $properties = $phpWord->getDocInfo();
        
        return [
            'title' => $properties->getTitle() ?? '',
            'subject' => $properties->getSubject() ?? '',
            'creator' => $properties->getCreator() ?? '',
            'keywords' => $properties->getKeywords() ?? '',
            'description' => $properties->getDescription() ?? '',
            'last_modified_by' => $properties->getLastModifiedBy() ?? '',
            'created' => $properties->getCreated() ?? '',
            'modified' => $properties->getModified() ?? '',
        ];
    }

    /**
     * Convert document to HTML
     */
    protected function convertToHtml($phpWord): string
    {
        $html = '<div class="document-content">';

        foreach ($phpWord->getSections() as $section) {
            $html .= $this->processSectionElements($section->getElements());
        }

        $html .= '</div>';

        return $html;
    }

    /**
     * Process section elements recursively
     */
    protected function processSectionElements(array $elements): string
    {
        $html = '';

        foreach ($elements as $element) {
            $html .= $this->processElement($element);
        }

        return $html;
    }

    /**
     * Process individual element
     */
    protected function processElement(AbstractElement $element): string
    {
        $html = '';

        // Text elements
        if ($element instanceof Text) {
            $text = htmlspecialchars($element->getText());
            $style = $this->getTextStyle($element);
            
            if ($style) {
                $html .= '<span style="' . $style . '">' . $text . '</span>';
            } else {
                $html .= $text;
            }
        }
        
        // TextRun (formatted text with mixed styles)
        elseif ($element instanceof TextRun) {
            $html .= '<p>';
            foreach ($element->getElements() as $childElement) {
                $html .= $this->processElement($childElement);
            }
            $html .= '</p>';
        }
        
        // List items
        elseif ($element instanceof ListItem) {
            $html .= '<li>';
            foreach ($element->getElements() as $childElement) {
                $html .= $this->processElement($childElement);
            }
            $html .= '</li>';
        }
        
        // Images
        elseif ($element instanceof Image) {
            $html .= $this->processImage($element);
        }
        
        // Tables
        elseif ($element instanceof Table) {
            $html .= $this->processTable($element);
        }
        
        // Other text containers
        elseif (method_exists($element, 'getText')) {
            $text = htmlspecialchars($element->getText());
            $html .= '<p>' . $text . '</p>';
        }

        return $html;
    }

    /**
     * Get text styling
     */
    protected function getTextStyle($element): string
    {
        $styles = [];
        
        if (method_exists($element, 'getFontStyle')) {
            $fontStyle = $element->getFontStyle();
            
            if ($fontStyle) {
                // Bold
                if (method_exists($fontStyle, 'isBold') && $fontStyle->isBold()) {
                    $styles[] = 'font-weight: bold';
                }
                
                // Italic
                if (method_exists($fontStyle, 'isItalic') && $fontStyle->isItalic()) {
                    $styles[] = 'font-style: italic';
                }
                
                // Underline
                if (method_exists($fontStyle, 'getUnderline') && $fontStyle->getUnderline()) {
                    $styles[] = 'text-decoration: underline';
                }
                
                // Font size
                if (method_exists($fontStyle, 'getSize') && $fontStyle->getSize()) {
                    $styles[] = 'font-size: ' . $fontStyle->getSize() . 'pt';
                }
                
                // Font color
                if (method_exists($fontStyle, 'getColor') && $fontStyle->getColor()) {
                    $styles[] = 'color: #' . $fontStyle->getColor();
                }
            }
        }

        return implode('; ', $styles);
    }

    /**
     * Process image element
     */
    protected function processImage($element): string
    {
        try {
            $source = $element->getSource();
            $this->imageCounter++;
            
            // Save image to storage
            $imageContent = file_get_contents($source);
            $extension = pathinfo($source, PATHINFO_EXTENSION) ?: 'png';
            $filename = 'document-image-' . time() . '-' . $this->imageCounter . '.' . $extension;
            
            $path = 'document-imports/images/' . $filename;
            Storage::disk('public')->put($path, $imageContent);
            
            $this->extractedImages[] = [
                'filename' => $filename,
                'path' => $path,
                'url' => Storage::disk('public')->url($path),
                'size' => strlen($imageContent),
            ];
            
            $imageUrl = Storage::disk('public')->url($path);
            
            return '<img src="' . $imageUrl . '" class="document-image" alt="Document Image" />';
            
        } catch (\Exception $e) {
            return '<!-- Image extraction failed: ' . $e->getMessage() . ' -->';
        }
    }

    /**
     * Process table element
     */
    protected function processTable($table): string
    {
        $html = '<table class="document-table border-collapse border border-gray-300">';
        
        foreach ($table->getRows() as $row) {
            $html .= '<tr>';
            
            foreach ($row->getCells() as $cell) {
                $html .= '<td class="border border-gray-300 p-2">';
                
                foreach ($cell->getElements() as $cellElement) {
                    $html .= $this->processElement($cellElement);
                }
                
                $html .= '</td>';
            }
            
            $html .= '</tr>';
        }
        
        $html .= '</table>';
        
        return $html;
    }

    /**
     * Count words in HTML content
     */
    protected function countWords(string $html): int
    {
        $text = strip_tags($html);
        $text = preg_replace('/\s+/', ' ', $text);
        $words = explode(' ', trim($text));
        
        return count(array_filter($words));
    }

    /**
     * Create a lesson from imported document
     */
    public function createLessonFromImport(DocumentImport $import, int $moduleId, array $options = []): \App\Models\Lesson
    {
        if (!$import->isCompleted()) {
            throw new \Exception('Document import must be completed before creating a lesson');
        }

        $lesson = \App\Models\Lesson::create([
            'uuid' => (string) Str::uuid(),
            'module_id' => $moduleId,
            'title' => $options['title'] ?? $import->metadata['title'] ?? $import->original_filename,
            'slug' => Str::slug($options['title'] ?? $import->metadata['title'] ?? $import->original_filename),
            'description' => $options['description'] ?? $import->metadata['description'] ?? null,
            'type' => 'document',
            'content' => $import->html_content,
            'order' => $options['order'] ?? 1,
            'duration_minutes' => $options['duration_minutes'] ?? ceil($import->word_count / 200), // Avg reading speed
            'is_published' => $options['is_published'] ?? true,
            'created_by' => $import->user_id,
        ]);

        // Link the lesson to the import
        $import->update(['lesson_id' => $lesson->id]);

        return $lesson;
    }

    /**
     * Get import statistics
     */
    public function getStatistics(int $userId = null): array
    {
        $query = DocumentImport::query();
        
        if ($userId) {
            $query->where('user_id', $userId);
        }

        return [
            'total' => $query->count(),
            'pending' => $query->clone()->pending()->count(),
            'processing' => $query->clone()->processing()->count(),
            'completed' => $query->clone()->completed()->count(),
            'failed' => $query->clone()->failed()->count(),
            'total_images_extracted' => $query->clone()->completed()->sum('image_count'),
            'total_words_processed' => $query->clone()->completed()->sum('word_count'),
        ];
    }
}

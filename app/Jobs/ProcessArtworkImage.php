<?php

namespace App\Jobs;

// app/Jobs/ProcessArtworkImage.php

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class ProcessArtworkImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $artwork;
    protected $imagePath;

    public function __construct($artwork, $imagePath)
    {
        $this->artwork = $artwork;
        $this->imagePath = $imagePath;
    }

    public function handle()
    {
        try {
            // Load the original image
            $originalImage = Image::make(Storage::path($this->imagePath));
            
            // Example: Crop to remove borders (adjust these values)
            $croppedImage = $originalImage->crop(
                $originalImage->width() - 100,  // width minus 50px from each side
                $originalImage->height() - 100, // height minus 50px from each side
                50,  // x-offset (left crop)
                50   // y-offset (top crop)
            );
            
            // For advanced detection (requires OpenCV):
            // $this->detectAndExtractArtwork($originalImage);
            
            // Save the processed image
            $newPath = 'artworks/processed/'.basename($this->imagePath);
            Storage::put($newPath, (string) $croppedImage->encode());
            
            // Update the artwork record
            $this->artwork->update(['processed_image_path' => $newPath]);
            
        } catch (\Exception $e) {
            \Log::error("Image processing failed: ".$e->getMessage());
        }
    }
    
    // Advanced: Using OpenCV for artwork detection
    protected function detectAndExtractArtwork($image)
    {
        // This requires OpenCV PHP bindings
        // Implementation would depend on your specific requirements
    }
}
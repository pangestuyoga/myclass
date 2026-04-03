<?php

namespace App\MediaLibrary;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class CustomPathGenerator implements PathGenerator
{
    public function getPath(Media $media): string
    {
        $feature = $media->getCustomProperty('feature', 'misc');
        $date = $media->getCustomProperty('date', $media->created_at?->toDateString() ?? now()->toDateString());
        $docType = $media->getCustomProperty('doc_type');

        $feature = str($feature)->slug();
        $date = str($date);
        $docType = $docType ? str($docType)->slug() : null;

        $path = "{$feature}/";

        if ($docType) {
            $path .= "{$docType}/";
        }

        $path .= "{$date}/";

        return $path;
    }

    public function getPathForConversions(Media $media): string
    {
        return $this->getPath($media).'conversions/';
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getPath($media).'responsive/';
    }
}

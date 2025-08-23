<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Cloudinary\Transformation\Resize;
use Exception;

class CloudinaryService
{
    private $cloudinary;

    public function __construct()
    {
        $this->cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                'api_key' => env('CLOUDINARY_API_KEY'),
                'api_secret' => env('CLOUDINARY_API_SECRET'),
            ],
            'url' => [
                'secure' => true
            ]
        ]);
    }

    /**
     * Sube una imagen a Cloudinary
     */
    public function uploadImage($file, $folder = 'products', $options = [])
    {
        try {
            $defaultOptions = [
                'folder' => $folder,
                'resource_type' => 'image',
                'quality' => 'auto',
                'fetch_format' => 'auto',
            ];

            $uploadOptions = array_merge($defaultOptions, $options);

            $result = $this->cloudinary->uploadApi()->upload($file, $uploadOptions);

            return [
                'success' => true,
                'url' => $result['secure_url'],
                'public_id' => $result['public_id'],
                'format' => $result['format'],
                'width' => $result['width'],
                'height' => $result['height'],
                'bytes' => $result['bytes']
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Elimina una imagen de Cloudinary
     */
    public function deleteImage($publicId)
    {
        try {
            $result = $this->cloudinary->uploadApi()->destroy($publicId);
            
            return [
                'success' => $result['result'] === 'ok',
                'result' => $result['result']
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Obtiene URL con transformaciones
     */
    public function getTransformedUrl($publicId, $transformations = [])
    {
        try {
            $url = $this->cloudinary->image($publicId);
            
            // Aplicar transformaciones si se proporcionan
            foreach ($transformations as $transformation) {
                $url = $url->addTransformation($transformation);
            }
            
            return $url->toUrl();

        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Extrae public_id de una URL de Cloudinary
     */
    public function extractPublicIdFromUrl($url)
    {
        // Ejemplo: https://res.cloudinary.com/cloud_name/image/upload/v1234567890/products/abc123.jpg
        $pattern = '/\/v\d+\/(.+)\.\w+$/';
        preg_match($pattern, $url, $matches);
        
        return isset($matches[1]) ? $matches[1] : null;
    }
}
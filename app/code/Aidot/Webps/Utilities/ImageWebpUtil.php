<?php
namespace Aidot\Webps\Utilities;

use Exception;
use Psr\Log\LoggerInterface;

class ImageWebpUtil {
    protected LoggerInterface $logger;
    public function __construct(
        LoggerInterface $logger
    )
    {
        $this->logger = $logger;
    }
    public function changeFormat($path,$format='webp'){
        if(preg_match('/\.(png|jpe?g)$/i',$path)){
            $img = new \Imagick($path);
            $img->setImageFormat($format);
            $newPath = preg_replace("/\.(png|jpe?g)$/i",'.'.$format,$path);
            if(file_exists($newPath)){
                unlink($newPath);
            }
            $img->writeImage($newPath);

        }
    }

    public function compressImage($tinyKey,$path){
        if(preg_match('/\.(png|jpe?g)$/i',$path)){
            try{
                \Tinify\setKey($tinyKey);
                $source = \Tinify\fromFile($path);
                $source->toFile($path);
            }catch(Exception $e){
                $this->logger->error('tinypng compress image error : '.$e->getMessage());
            }
            
        }
    }
}

<?php

namespace Library\Baaane\ImageUploader\Action;

use Exception;
use Library\Baaane\ImageUploader\Core\Upload;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use Library\Baaane\ImageUploader\Action\MobileImageSize;
use Library\Baaane\ImageUploader\Action\DesktopImageSize;
use Library\Baaane\ImageUploader\Action\ThumbnailImageSize;
use Library\Baaane\ImageUploader\Builder\ReflectionClassBuilder;
use Library\Baaane\ImageUploader\Exceptions\ImageUploaderException;
use Library\Baaane\ImageUploader\Exceptions\InvalidImageTypeException;

class ImageUploader
{
	/**
	 * Different class sizes
	 * @var array $imageClassSize
	 */
	private $imageClassSize = [
		'thumbnail' => ThumbnailImageSize::class,
		'mobile'	=> MobileImageSize::class,
		'desktop'	=> DesktopImageSize::class,
	];

    /**
     * File path
     *
     * @param string $filePath
     */
    public function __construct($filePath = NULL)
    {
        $this->filePath = $filePath;
    }

	/**
	 * Upload the image
	 *
	 * @param array $data
	 * @return array
	 *
	 */
	public function upload(array $data)
	{	
		try {
			if($data['error']){
				throw new ImageUploaderException($data['error']);
			}

			if($this->checkImageType($data)){
				$upload = new Upload($this->filePath);
				$fileData = $upload->handle($data);
				$result = $this->resize($fileData, $data['new_size']);

				return $result;
			}

		} catch (ImageUploaderException $e) {
			throw $e;
		}
	}

	/**
	 * Resizing the image
	 * Thumbnail|Mobile|Desktop	
	 *
	 * @param array $data
	 * @return array
	 *
	 */
	public function resize($fileData, $size)
	{
		foreach ($this->imageClassSize as $key => $value) {
			$builder = ReflectionClassBuilder::create($this->imageClassSize[$key]);
			$data[] = $builder->get($fileData,$size);

			foreach ($data as $dkey => $dvalue) {
				$this->image_optimization($dvalue);
				$result[$key] = $dvalue;
			}
		}

		return $result;	
	}

	/**
	 * Set width and height
	 *
	 * @param int $width
	 * @param int $height
	 * @return array
	 *
	 */
	public function setThumbnailSize($width = NULL, $height = NULL)
	{
		$width 	= (isset($width) ? $width : 0);
		$height = (isset($height) ? $height : 0);
		$this->result['thumbnail'] = ['width' => $width, 'height' => $height];

		return $this;
	}

	/**
	 * Set width and height
	 *
	 * @param int $width
	 * @param int $height
	 * @return array
	 *
	 */
	public function setMobileSize($width = NULL, $height = NULL)
	{
		$width 	= (isset($width) ? $width : 0);
		$height = (isset($height) ? $height : 0);
		$this->result['mobile'] = ['width' => $width, 'height' => $height];

		return $this;
	}

	/**
	 * Set width and height
	 *
	 * @param int $width
	 * @param int $height
	 * @return array
	 *
	 */
	public function setDesktopSize($width = NULL, $height = NULL)
	{
		$width 	= (isset($width) ? $width : 0);
		$height = (isset($height) ? $height : 0);
		$this->result['desktop'] = ['width' => $width, 'height' => $height];

		return $this;
	}

	/**
	 * Get
	 * @return array
	 *
	 */
	public function get()
	{
		return $data = $this->result;
	}


	/**
	 * Re-arrange the array	
	 *
	 * @param array $data
	 * @return array
	 *
	 */
	public function reArray(&$data_array)
	{
		$data = [];
        foreach($data_array as $key => $value){
            foreach($value as $vkey => $vvalue){
                $data[$vkey][$key] = $vvalue;
            }
        }

	    return $data;
	}

	/**
	 * Validate image
	 *
	 * @param array $data
	 * @return boolean
	 *
	 */
	public function checkImageType($data)
	{
		$allowed_ext = [
			'image/jpeg',
			'image/png',
			'image/gif'
		];

		// Get image file extension
    	$file_mime = mime_content_type($data['tmp_name']);

    	// Validate file input to check if is with valid extension
		if(!in_array($file_mime, $allowed_ext)){
			throw InvalidImageTypeException::checkMimeType($file_mime);
		}

		return TRUE;
	}

	/**
     * Optimize the file
     *
     * @param string $filename with path
     */
	private function image_optimization($filename)
	{
		$optimizerChain = OptimizerChainFactory::create();
		$optimizerChain->optimize($filename);
	}
}
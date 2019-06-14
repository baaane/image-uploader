<?php

namespace Library\Baaane\ImageUploader\Domains;

class ImageJpeg 
{
	/**
	 * Get the image information 
	 *
	 * @param string $tmp_name
	 * @return object
	 *
	 */
	public function info($tmp_name)
	{
		$data = imagecreatefromjpeg($tmp_name);
		return $data;
	}

	/**
	 * Create new image
	 *
	 * @param string $new
	 * @param string $name
	 * @param string $final
	 * @return string
	 *
	 */
	public function create($new, $name, $final)
	{	
		imagejpeg($new, $final, 85);
		$data = rtrim($final);
		return $data;
	}
}
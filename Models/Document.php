<?php
/**
 * This is the Documents model
 * @author Phelix Juma <jumaphelix@kuzalab.com>
 * @author Allan Otieno <allan@kuzalab.com>
 * @copyright (c) 2019, Kuza Lab
 * @package Kuza Krypton PHP Framework
 */

namespace Kuza\Krypton\Framework\Models;

use Kuza\Krypton\Exceptions\ConfigurationException;
use Kuza\Krypton\Exceptions\CustomException;
use Kuza\Krypton\Classes\Requests;
use Kuza\Krypton\Classes\S3;
use Kuza\Krypton\Config\Config;
use Kuza\Krypton\Framework\Framework\DBConnection;

final class Document extends DBConnection {

    public $id;
    public $name;
    public $file_name;
    public $file_uri_path;
    public $type;
    public $category;
    public $size;
    public $extension;
    public $mime_type;

    public $link;
    public $hash_link;

    public $is_image = false;
    public $is_video = false;
    public $is_pdf = false;
    public $is_word = false;
    public $is_zip = false;

    public $created_at;
    public $created_by;
    public $is_archived = false;
    public $archived_by;
    public $archived_at;

    /**
     * mime types for images
     * @var array
     */
    private $images_mime_types = [
        // images
        'png' => 'image/png',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'gif' => 'image/gif',
        'bmp' => 'image/bmp'
    ];

    /**
     * mime types for zip archive file
     * @var array
     */
    private $zip_mime_types = [
        // archives
        'zip' => 'application/zip'
    ];

    /**
     * mime types for video
     * @var array
     */
    private $video_mime_types = [
        // video
        'webm' => 'video/webm',
        '3gp' => 'video/3gpp',
        'mp4' => 'video/mp4',
        'flv'=>'video/x-flv'
    ];

    /**
     * mime types for pdf
     * @var array
     */
    private $pdf_mime_types = [
        // adobe
        'pdf' => 'application/pdf'
    ];

    /**
     * mime types for word documents
     * @var array
     */
    private $word_mime_types = [
        // ms office
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'rtf' => 'application/rtf',
        'xls' => 'application/vnd.ms-excel',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'ppt' => 'application/vnd.ms-powerpoint',
        'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        // open office
        'odt' => 'application/vnd.oasis.opendocument.text',
        'ods' => 'application/vnd.oasis.opendocument.spreadsheet'
    ];

    /**
     * Documents constructor.
     */
    public function __construct() {
        parent::__construct("documents");
    }

    /**
     * Set the details of the document.
     * @param $data
     * @throws ConfigurationException
     */
    private function setDetails($data) {
        $this->id = isset($data['id'])  ? $data['id'] : "";
        $this->name = isset($data['name']) ? $data['name'] : "";
        $this->file_name = isset($data['file_name']) ? $data['file_name'] : "";
        $this->file_uri_path = isset($data['file_uri_path']) ? $data['file_uri_path'] : "";
        $this->type = isset($data['type']) ? $data['type'] : "";
        $this->category = isset($data['category']) ? $data['category'] : "";
        $this->size = isset($data['size']) ? $data['size'] : "";
        $this->extension = isset($data['extension']) ? $data['extension'] : "";
        $this->mime_type = isset($data['mime_type']) ? $data['mime_type'] : "";
        $this->image_height = isset($data['image_height']) ? $data['image_height'] : "";
        $this->image_width = isset($data['image_width']) ? $data['image_width'] : "";

        $this->setLink();
        //$this->setHashLink(86400);

        $this->is_image = $this->isImage();
        $this->is_video = $this->isVideo();
        $this->is_pdf = $this->isPDF();
        $this->is_word = $this->isWord();
        $this->is_zip = $this->isZip();

        $this->created_at = $data['created_at'];
        $this->created_by = $data['created_by'];
        $this->is_archived = $data['is_archived'];
        $this->archived_by = $data['archived_by'];
        $this->archived_at = $data['archived_at'];
    }

    /**
     * Set document by id
     * @param $id
     * @throws ConfigurationException
     */
    public function setDocumentById($id) {
        $records = parent::selectOne($id);

        $data = isset($records) && is_array($records) && sizeof($records) ? $records : [];

        $this->setDetails($data);
    }

    /**
     * Get the details of a document
     */
    public function getDetails() {
       return $this->toArray();
    }

    /**
     * Check if the document exists or not
     * @param $id
     * @return bool
     */
    public function isDocument($id) {
        $criteria = ["id"=>$id];

        $this->prepareCriteria($criteria);

        return parent::exists($criteria);
    }

    /**
     * Check if the file is an image or not
     * @return bool
     */
    private function isImage() {
        foreach($this->images_mime_types as $ext => $mime) {
            if($ext == $this->extension && $this->mime_type == $mime) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if the file is a video or not
     * @return bool
     */
    private function isVideo() {
        foreach($this->video_mime_types as $ext => $mime) {
            if($ext == $this->extension && $this->mime_type == $mime) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if the file is a pdf or not
     * @return bool
     */
    private function isPDF() {
        if($this->extension == "pdf" && $this->mime_type == "application/pdf") {
            return true;
        }
        return false;
    }

    /**
     * Check if the file is a zip file or not
     * @return bool
     */
    private function isZip() {
        if($this->extension == "zip" && $this->mime_type == "application/zip") {
            return true;
        }
        return false;
    }

    /**
     * Check if the file is a word document or not
     * @return bool
     */
    private function isWord() {
        foreach($this->word_mime_types as $ext => $mime) {
            if($ext == $this->extension && $this->mime_type == $mime) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get the link to the file.
     * If the file is private, it gets the signed URL, otherwise it returns the public URL
     * @return mixed
     */
    public function getLink() {
        return $this->link;
    }

    /**
     * Get the link to the file
     * This is the link to the file in S3.
     * @throws ConfigurationException
     */
    public function setLink() {
        $this->link = "";
        if (!empty($this->name)) {
            // get the base URL
            $baseURL = Config::getAWSCloudFrontDocumentURL();

            // prepare the file_uri_path
            $file_uri_path = !empty($this->file_uri_path) ? $this->file_uri_path . "/" : "";

            $this->link = $baseURL . $file_uri_path . $this->name . "." . $this->extension;
        }
    }

    /**
     * Get the CloudFront link to a file
     * @throws ConfigurationException
     */
    public function getCloudFrontLink() {
        if (!empty($this->name)) {
            // get the base URL
            $baseURL = Config::getAWSCloudFrontDocumentURL();

            // prepare the file_uri_path
            $file_uri_path = !empty($this->file_uri_path) ? $this->file_uri_path . "/" : "";

            return $baseURL . $file_uri_path . $this->name . "." . $this->extension;
        }
        return "";
    }

    /**
     * Set the hash link for the document
     * This is done only if the file is not public
     * @param $timeout
     * @throws CustomException
     */
    public function setHashLink($timeout) {
        $this->hash_link = "";
        if (!empty($this->name)) {

            try {
                $this->hash_link = S3::getSignedURL($this->getCloudFrontLink(), $timeout);
            } catch (\Exception $e) {
                throw new CustomException($e->getMessage(), $e->getCode());
            }

        }
    }

    /**
     * Create a new document
     * @param $data
     * @return bool
     * @throws CustomException
     */
    public function createDocument($data){
        $this->prepareInsertData($data);

        parent::insert($data);

        if($this->is_error !== false){
            throw new CustomException("Database Error: ".$this->message,Requests::RESPONSE_INTERNAL_SERVER_ERROR);
        }
        return true;
    }

    /**
     * Delete a document
     * The document is archived
     * @param $documentId
     * @return mixed
     * @throws CustomException
     */
    public function deleteDocument($documentId){

        $data = [];
        $this->prepareDeleteData($data);

        parent::update($data,['id'=>$documentId]);

        if($this->is_error !== false) {
            throw new CustomException("Database Error:".$this->message,Requests::RESPONSE_INTERNAL_SERVER_ERROR);
        }
        return true;
    }
}

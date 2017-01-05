<?php
// Include the SDK using the Composer autoloader
include_once("../tools/aws/aws-autoloader.php");
use Aws\S3\MultipartUploader;
use Aws\Exception\MultipartUploadException;

/**
 * Conexión servicio amazon web service
 */
class AwsCore extends ObjectModel
{
        public $s3Client;
        public $bucket = 'imagenes-fluzfluz';
        public $shopDomainProd = 'fluzfluz.co';

        /**
         * Datos de acceso
         */
        public function __construct()
        {
                $this->s3Client = new Aws\S3\S3Client([
                  'version' => 'latest',
                  'region'  => 'us-east-1',
                  'credentials' => [
                        'key'    => Configuration::get('PS_IMAGE_KEY'),
                        'secret' => Configuration::get('PS_IMAGE_SECRET'),
                    ],
                ]);
        }

        /**
         * Recupera objetos de Amazon S3
         *
         * @param obj string nombre de objeto
         * @return object 
         */
        public function getObject($obj = '', $bucketType = '')
        {
                $result = $this->s3Client->getObject([
                    'Bucket' => $this->bucket . $bucketType,
                    'Key'    => $obj
                ]);

                return $result;
        }

        /**
         * Obtiene la URL de un objeto
         * Este método devuelve una URL sin firmar para el bucket y key.
         *
         * @param obj string nombre de objeto
         * @return string URL del objeto
         */
        public function getObjectUrl($obj = '', $bucketType = '')
        {
                return $this->s3Client->getObjectUrl($this->bucket . $bucketType, $obj);
        }

        /**
         * Subidas de múltiples partes están diseñadas 
         * para mejorar la experiencia de carga de objetos más grandes
         * Nota: Se recomienda a los clientes de Amazon S3 
         * que utilicen subidas múltiples para objetos de más de 100 MB.
         *
         * @see https://docs.aws.amazon.com/aws-sdk-php/v3/guide/service/s3-multipart-upload.html
         * @param obj string ruta del objeto
         * @return object 
         */
        public function setObject($srcObj = '', $obj = '', $bucketType = '')
        {
                $uploader = new MultipartUploader($this->s3Client, $srcObj, [
                        'bucket' => $this->bucket . $bucketType,
                        'key'    => $obj,
                        'acl'    => 'public-read'  
                ]);

                try {
                        return $uploader->upload();
                } catch (MultipartUploadException $e) {
                        return $e->getMessage() . "\n";
                }
        }
        
        public function deleteObject($keyname = ''){
            $folder = Configuration::get('PS_SHOP_DOMAIN') == $this->shopDomainProd ? $folder : 'dev/' . $folder;
            $result = $this->s3Client->deleteObject(array(
                'Bucket' => $bucket,
                'Key'    => $folder.$keyname
            ));
            return $result;
        }

        /**
         * Carga un objeto de hasta 5 GB
         *
         * @see http://docs.aws.amazon.com/AmazonS3/latest/dev/UploadObjSingleOpPHP.html
         * @param srcObj string ruta del objeto
         * @param obj string nombre del objeto
         * @param folder string directorio en el bucket
         * @return object
         */
        public function setObjectImage($srcObj = '', $obj = '', $folder = '')
        {
                $folder = Configuration::get('PS_SHOP_DOMAIN') == $this->shopDomainProd ? $folder : 'dev/' . $folder;
                try {
                        $result = $this->s3Client->putObject(array(
                                'Bucket' => $this->bucket,
                                'Key' => $folder . $obj, 
                                'SourceFile' => $srcObj,
                                'ACL' => 'public-read', 
                                'CacheControl' => 'public',
                                'Expires' => strtotime("+180 day")
                        ));
                        return $result['ObjectURL'];
                } catch (Exception $e) {
                    return false;
                }
        }
}



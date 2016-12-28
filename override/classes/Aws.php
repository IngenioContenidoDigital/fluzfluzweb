<?php
// Include the SDK using the Composer autoloader
include_once("../tools/aws/aws-autoloader.php");
use Aws\S3\MultipartUploader;
use Aws\Exception\MultipartUploadException;

/**
 * Conexi�n servicio amazon web service
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
         * Este m�todo devuelve una URL sin firmar para el bucket y key.
         *
         * @param obj string nombre de objeto
         * @return string URL del objeto
         */
        public function getObjectUrl($obj = '', $bucketType = '')
        {
                return $this->s3Client->getObjectUrl($this->bucket . $bucketType, $obj);
        }

        /**
         * Subidas de m�ltiples partes est�n dise�adas 
         * para mejorar la experiencia de carga de objetos m�s grandes
         * Nota: Se recomienda a los clientes de Amazon S3 
         * que utilicen subidas m�ltiples para objetos de m�s de 100 MB.
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



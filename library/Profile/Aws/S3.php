<?php


namespace Profile\Aws;

use Aws\S3\S3Client;
use Aws\Common\Enum\Region;
use Guzzle\Http\EntityBody;

class S3
{

    /**
     * @var S3Client
     **/
    private $client;


    /**
     * @var string
     **/
    private $bucket;


    /**
     * @var string
     **/
    private $aws_ini_path = 'config/aws.ini';



    /**
     * コンストラクタ
     *
     * @return void
     **/
    public function __construct ()
    {
        $this->_ifExistsAwsIni();

        // AWSクライアントの設定
        $ini = parse_ini_file(ROOT.'/'.$this->aws_ini_path);

        // メンバ変数の設定
        $this->bucket = $ini['bucket'];

        $this->client = S3Client::factory(
            array(
                'key' => $ini['key'],
                'secret' => $ini['secret'],
                'region' => Region::AP_NORTHEAST_1
            )
        );
    }


    /**
     * Aws 設定ファイルが存在しているかどうか
     *
     * @return void
     **/
    private function _ifExistsAwsIni ()
    {
        if (! file_exists(ROOT.'/'.$this->aws_ini_path)) {
            throw new \Exception('config/aws.ini ファイルが存在しません');
        }
    }


    /**
     * 指定パスのファイルがS3に存在するかどうか
     *
     * @param String $path  ファイルパス
     * @return boolean
     **/
    public function doesObjectExist ($path)
    {
        return $this->client->doesObjectExist($this->bucket, $path);
    }


    /**
     * 指定パスのファイルをダウンロードする
     *
     * @param String $path  S3のパス
     * @return Guzzle\Service\Resource\Model
     **/
    public function download ($path)
    {
        try {
            $response = $this->client->getObject(array(
                'Bucket' => $this->bucket,
                'Key' => $path
            ));
        
        } catch (\Exception $e) {
            throw $e;
        }

        return $response;
    }


    /**
     * 指定パスにファイルをアップロードする
     *
     * @param  string $file_path  ファイルへのパス
     * @return void
     **/
    public function upload ($file_path)
    {
        try {
            $to_path = str_replace(ROOT.'/', '', $file_path);
            $to_path = str_replace('public_html/', '', $to_path);

            $this->client->putObject(array(
                'SourceFile' => $file_path,
                'Key' => $to_path,
                'Bucket' => $this->bucket
            ));

        } catch (\Exception $e) {
            throw $e;
        }

        return true;
    }


    /**
     * 指定パスのファイルを削除する
     *
     * @param String $path  S3のパス
     * @return boolean
     **/
    public function delete ($path)
    {
        try {
            $this->client->deleteObject(array(
                'Bucket' => $this->bucket,
                'Key' => $path
            ));
        
        } catch (\Exception $e) {
            throw $e;
        }

        return true;
    }
}

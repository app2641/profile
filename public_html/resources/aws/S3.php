<?php


require_once dirname(__FILE__).'/AWSSDKforPHP/sdk.class.php';

class S3 extends AmazonS3
{
    const BUCKET = 'app2641.com';


    public function __construct ()
    {
        $config = dirname(__FILE__).'/config.ini';

        if (! file_exists($config)) {
            throw new Exception('config.iniが見つかりません');
        }


        $ini = parse_ini_file($config);

        parent::__construct(
            array(
                'key' => $ini['key'],
                'secret' => $ini['secret']
            )
        );

        $this->set_region(self::REGION_APAC_NE1);
    }



    /**
     * 再帰的にデータをアップロードする
     *
     * @author app2641
     **/
    public function upload ($path)
    {
        if (is_file($path)) {
            // ファイルの場合
            $file = preg_replace('/^.*profile\//', '', $path);
            $file = str_replace('//', '/', $file);

            $response = $this->create_object(
                $this::BUCKET,
                $file,
                array(
                    'fileUpload' => $path
                )
            );

            if ($response->status != 200) {
                echo 'failed uploaded '.$file.PHP_EOL;
            }
        
        } elseif (is_dir($path)) {
            // ディレクトリの場合
            if ($dh = opendir($path)) {
                while ($entry = readdir($dh)) {
                    if ($entry != '.' && $entry != '..') {
                        $entry_path = $path.'/'.$entry;
                        $this->upload($entry_path);
                    }
                }
                closedir($dh);
            }
        }
    }
}

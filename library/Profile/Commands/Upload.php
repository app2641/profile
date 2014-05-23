<?php

use Emerald\Command\AbstractCommand;
use Emerald\Command\CommandInterface;

use Profile\Aws\S3;

class Upload extends AbstractCommand implements CommandInterface
{

    /**
     * @var array
     **/
    private $params;


    /**
     * @var string
     **/
    private $path;


    /**
     * @var Profile\Aws\S3
     **/
    private $S3;
    

    /**
     * コマンドの実行
     *
     * @param Array $params  パラメータ配列
     * @return void
     **/
    public function execute (Array $params)
    {
        try {
            $this->params = $params;
            $this->_validateParameters();

            $this->S3 = new S3();
            $this->_upload($this->path);

        } catch (\Exception $e) {
            $this->errorLog($e->getMessage());
        }
    }


    /**
     * パラメータのバリデート
     *
     * @return void
     **/
    private function _validateParameters ()
    {
        if (! isset($this->params[1])) {
            throw new \Exception('アップロード対象のファイルを指定してください');
        }

        $this->path = ROOT.'/'.$this->params[1];
        if (! is_readable($this->path)) {
            throw new \Exception('アップロード対象のファイルが存在しません');
        }
    }


    /**
     * S3 へアップロードする
     *
     * @param  string $target_path 対象のパス
     * @return void
     **/
    private function _upload ($target_path)
    {
        if (is_file($target_path)) {
            $this->_uploadFile($target_path);
        
        } elseif (is_dir($target_path)) {
            $this->_uploadDirectory($target_path);
        }
    }


    /**
     * ファイルを S3 へアップロードする
     *
     * @param  string $file_path  ファイルへのパス
     * @return void
     **/
    private function _uploadFile ($file_path)
    {
        $this->S3->upload($file_path);
    }


    /**
     * ディレクトリを再帰的に S3 へアップロードする
     *
     * @param  string $dir_path  ディレクトリへのパス
     * @return void
     **/
    private function _uploadDirectory ($dir_path)
    {
        if ($dh = opendir($dir_path)) {
            while ($file = readdir($dh)) {
                if ($file == '.' || $file == '..' || $file == '.DS_Store') continue;
                $target_path = $dir_path.'/'.$file;
                $this->_upload($target_path);
            }
            closedir($dh);
        }
    }


    /**
     * ヘルプメッセージの表示
     *
     * @return String
     **/
    public static function help ()
    {
        return '指定したディレクトリ及びファイルを S3 へアップロードする';
    }
}

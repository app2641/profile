<?php

use Emerald\Command\AbstractCommand;
use Emerald\Command\CommandInterface;

class Compile extends AbstractCommand implements CommandInterface
{

    /**
     * @var array
     **/
    private $params;


    /**
     * コンパイル先のディレクトリ
     *
     * @var string
     **/
    private $compile_dir;


    /**
     * 生成するHTMLファイル名
     *
     * @var string
     **/
    private $html_file;


    /**
     * コンパイルするSlimファイル名
     *
     * @var string
     **/
    private $slim_file;


    /**
     * コマンドの実行
     *
     * @param Array $params  パラメータ配列
     * @return void
     **/
    public function execute (Array $params)
    {
        try {
            defined('DS') || define('DS', DIRECTORY_SEPARATOR);

            $this->params = $params;
            $this->_validateParameters();

            $this->_initCompileDirectory();
            $this->_compileSlimFile();

            $this->log('copied compile command!', 'success');

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
            throw new \Exception('コンパイル先のパスを指定してください');
        }


        // パスの頭に ./ が付いていたら除去する
        $param = preg_replace('/^\.\//', '', $this->params[1]);

        if (! preg_match('/.*\.html$/', $param)) {
            throw new \Exception('コンパイル先にはhtmlを指定してください');
        }

        $this->html_file = basename($param);
        $this->slim_file = str_replace('.html', '.slim', $this->html_file);
        $this->compile_dir = str_replace(DS.$this->html_file, '', $param);

        if (! file_exists(ROOT.'/public_html/resources/slim/'.$this->slim_file)) {
            throw new \Exception('該当するslimファイルが存在しません');
        }
    }


    /**
     * コンパイルディレクトリの初期化
     *
     * @return void
     **/
    private function _initCompileDirectory ()
    {
        if (! is_dir(ROOT.'/'.$this->compile_dir)) {
            mkdir (ROOT.'/'.$this->compile_dir, 0755, true);
        }
    }


    /**
     * slim から html へコンパイルする
     *
     * @return void
     **/
    private function _compileSlimFile ()
    {
        $command = sprintf(
            'slimrb %s > %s',
            ROOT.'/public_html/resources/slim/'.$this->slim_file,
            ROOT.'/'.$this->compile_dir.'/'.$this->html_file
        );

        touch ('/tmp/profile.query');
        file_put_contents('/tmp/profile.query', $command);
        exec('cat /tmp/profile.query | pbcopy');
    }


    /**
     * ヘルプメッセージの表示
     *
     * @return String
     **/
    public static function help ()
    {
        return '引数にコンパイル後に望む html のパスを指定すると、'.PHP_EOL.
            '  Slim ファイルをコンパイルして保存する';
    }
}

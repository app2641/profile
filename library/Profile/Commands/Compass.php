<?php

use Emerald\Command\AbstractCommand;
use Emerald\Command\CommandInterface;

class Compass extends AbstractCommand implements CommandInterface
{

    /**
     * @var array
     **/
    private $params;


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

            $this->_compassCompile();

        } catch (\Exception $e) {
            $this->errorLog($e->getMessage());
        }
    }


    /**
     * パラメータをバリデートする
     *
     * @return void
     **/
    private function _validateParameters ()
    {
        $params = $this->params;

        if (! isset($params[1])) {
            throw new \Exception('Compassでコンパイルするsassファイル名を指定してください');
        }

        $path = ROOT.'/public_html/resources/sass/'.$params[1].'.scss';
        if (! file_exists($path)) {
            throw new \Exception('指定したsassファイルが存在しません');
        }
    }


    /**
     * Compassコンパイルでcssファイルを生成する
     *
     * @return void
     **/
    private function _compassCompile ()
    {
        $path = ROOT.'/public_html/resources/sass';
        chdir($path);

        $command = 'compass compile '.$this->params[1].'.scss';
        passthru($command);
    }



    /**
     * ヘルプメッセージの表示
     *
     * @return String
     **/
    public static function help ()
    {
        return '引数に与えたsassファイルをCompassコンパイルする';
    }
}

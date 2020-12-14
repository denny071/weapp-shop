<?php

if (! function_exists('get_test_image')) {
    /**
     * 添加表注释
     *
     * @param $table
     * @param $comment
     * @return bool
     */
    function get_test_image($type,$ext = "jpg", $count = 1){

        if($count < 1) {
            $count = 1;
        }
        $temp = [];
        for ($i = 0; $i < $count; $i ++) {
            $temp[] = env("APP_URL")."/test/".$type."_".rand(0,9).".".$ext;
        }
       return implode(",",$temp);
    }
}



if (! function_exists('add_table_comment')) {
    /**
     * 添加表注释
     *
     * @param $table
     * @param $comment
     * @return bool
     */
    function add_table_comment($table,$comment){
        $sql = "alter table `".env('DB_PREFIX').$table."` comment '{$comment}'";
        Illuminate\Support\Facades\DB::statement($sql);
        return true;
    }
}


if (! function_exists('throwErrorMessage')) {

    /**
     * throwErrorMessage  抛出错误消息
     *
     * @param  string $module   模块
     * @param  string $code     编码
     * @param  string $append   附加信息
     * @return Exception
     */
    function throwErrorMessage(string $module,string $code, $append = ""): \Exception
    {

        $message = Denny071\LaravelApidoc\Helper::getMessage($module,$code);

        $message = $code."|".$message.($append?"(".$append.")":"");

        throw new App\Exceptions\InternalException($message);
    }
}

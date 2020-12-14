<?php
namespace Database\Seeders;


use Illuminate\Database\Seeder;
use App\Models\ExpressCost;

class ExpressCostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dataList = [
            "北京市" => "1",
            "天津市" => "2",
            "上海市" => "3",
            "重庆市" => "4",
            "河北省" => "5",
            "山西省" => "6",
            "辽宁省" => "7",
            "吉林省" => "8",
            "黑龙江省" => "9",
            "江苏省" => "10",
            "浙江省" => "11",
            "安徽省" => "12",
            "福建省" => "13",
            "江西省" => "14",
            "山东省" => "15",
            "河南省" => "17",
            "湖北省" => "18",
            "湖南省" => "19",
            "广东省" => "20",
            "海南省" => "21",
            "四川省" => "22",
            "贵州省" => "23",
            "云南省" => "24",
            "陕西省" => "25",
            "甘肃省" => "26",
            "青海省" => "27",
            "台湾省" => "28",
            "内蒙古" => "29",
            "广西" => "30",
            "西藏" => "31",
            "宁夏" => "32",
            "新疆" => "33",
            "香港" => "34",
            "澳门" => "35",
        ];

        foreach ($dataList as $province => $freight) {
            $expressCost = new ExpressCost;
            $expressCost->express_id = 42;
            $expressCost->province = $province;
            $expressCost->freight = $freight;
            $expressCost->save();
        }

    }
}

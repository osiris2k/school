<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DataTypeTableSeeder extends Seeder
{

    public function run()
    {

        // data type and data_type_options

        $textId = DB::table('data_types')->insertGetId(array(
                'name' => 'text'
            )
        );

        // DB::table('data_type_options')->insert(array(
        //         'name'         => 'size',
        //         'type'         =>'int',
        //         'is_mandatory' =>1,
        //         'default'      =>400,
        //         'data_type_id'  =>$textId,
        //     )
        // );
        DB::table('data_type_options')->insert(array(
                'name'         => 'max',
                'type'         => 'int',
                'is_mandatory' => 1,
                'default'      => 255,
                'data_type_id' => $textId,
            )
        );
        DB::table('data_type_options')->insert(array(
                'name'         => 'min',
                'type'         => 'int',
                'is_mandatory' => 1,
                'default'      => 0,
                'data_type_id' => $textId,
            )
        );

        $numberId = DB::table('data_types')->insertGetId(array(
                'name' => 'number'
            )
        );

        $textAreaId = DB::table('data_types')->insertGetId(array(
                'name' => 'textarea'
            )
        );

        $richTextId = DB::table('data_types')->insertGetId(array(
                'name' => 'richtext'
            )
        );

        $dateId = DB::table('data_types')->insertGetId(array(
                'name' => 'date'
            )
        );

        $dateTimeId = DB::table('data_types')->insertGetId(array(
                'name' => 'datetime'
            )
        );

        $checkBoxId = DB::table('data_types')->insertGetId(array(
                'name' => 'checkbox'
            )
        );

        DB::table('data_type_options')->insert(array(
                'name'         => 'list',
                'type'         => 'string',
                'is_mandatory' => 1,
                'data_type_id' => $checkBoxId,
            )
        );

        DB::table('data_type_options')->insert(array(
                'name'         => 'listObject',
                'type'         => 'string',
                'is_mandatory' => 1,
                'data_type_id' => $checkBoxId,
            )
        );

        $radioId = DB::table('data_types')->insertGetId(array(
                'name' => 'radio'
            )
        );

        DB::table('data_type_options')->insert(array(
                'name'         => 'list',
                'type'         => 'string',
                'is_mandatory' => 1,
                'data_type_id' => $radioId,
            )
        );

        $googleId = DB::table('data_types')->insertGetId(array(
                'name' => 'google_coordinate'
            )
        );

        DB::table('data_type_options')->insert(array(
                'name'         => 'latitude',
                'type'         => 'string',
                'is_mandatory' => 1,
                'data_type_id' => $googleId,
                'default'      => '13.7424086'
            )
        );
        DB::table('data_type_options')->insert(array(
                'name'         => 'longitude',
                'type'         => 'string',
                'is_mandatory' => 1,
                'data_type_id' => $googleId,
                'default'      => '100.5613505'
            )
        );

        $imageId = DB::table('data_types')->insertGetId(array(
                'name' => 'image'
            )
        );

        DB::table('data_type_options')->insert(array(
                'name'         => 'width',
                'type'         => 'int',
                'is_mandatory' => 0,
                'default'      => '*',
                'data_type_id' => $imageId,
            )
        );
        DB::table('data_type_options')->insert(array(
                'name'         => 'height',
                'type'         => 'int',
                'is_mandatory' => 0,
                'default'      => '*',
                'data_type_id' => $imageId,
            )
        );
        DB::table('data_type_options')->insert(array(
                'name'         => 'caption text',
                'type'         => 'string',
                'is_mandatory' => 0,
                'default'      => '-',
                'data_type_id' => $imageId,
            )
        );
        DB::table('data_type_options')->insert(array(
                'name'         => 'mimes',
                'type'         => 'string',
                'is_mandatory' => 0,
                'default'      => 'png,jpg,gif',
                'data_type_id' => $imageId,
            )
        );

        /**
         * Add Title and Alt tag for Image data-type
         *
         * @edited maris
         * @date   2015/11/20
         */
        DB::table('data_type_options')->insert(array(
                'name'         => 'caption title',
                'type'         => 'string',
                'is_mandatory' => 0,
                'default'      => '-',
                'data_type_id' => $imageId,
            )
        );
        DB::table('data_type_options')->insert(array(
                'name'         => 'alt',
                'type'         => 'string',
                'is_mandatory' => 0,
                'default'      => '-',
                'data_type_id' => $imageId,
            )
        );

        /**
         * Add Class option for Image data-type
         *
         * @edited maris
         * @date   2016/06/13
         */
        DB::table('data_type_options')->insert(array(
                'name'         => 'class',
                'type'         => 'string',
                'is_mandatory' => 0,
                'default'      => '-',
                'data_type_id' => $imageId,
            )
        );

        $imagesId = DB::table('data_types')->insertGetId(array(
                'name' => 'images'
            )
        );

        DB::table('data_type_options')->insert(array(
                'name'         => 'width',
                'type'         => 'int',
                'is_mandatory' => 0,
                'default'      => '*',
                'data_type_id' => $imagesId,
            )
        );
        DB::table('data_type_options')->insert(array(
                'name'         => 'height',
                'type'         => 'int',
                'is_mandatory' => 0,
                'default'      => '*',
                'data_type_id' => $imagesId,
            )
        );
        DB::table('data_type_options')->insert(array(
                'name'         => 'caption text',
                'type'         => 'string',
                'is_mandatory' => 0,
                'default'      => '-',
                'data_type_id' => $imagesId,
            )
        );
        DB::table('data_type_options')->insert(array(
                'name'         => 'mimes',
                'type'         => 'string',
                'is_mandatory' => 0,
                'default'      => 'png,jpg,gif',
                'data_type_id' => $imagesId,
            )
        );

        /**
         * Add Title and Alt tag for Images data-type
         *
         * @edited maris
         * @date   2015/11/20
         */
        DB::table('data_type_options')->insert(array(
                'name'         => 'caption title',
                'type'         => 'string',
                'is_mandatory' => 0,
                'default'      => '-',
                'data_type_id' => $imagesId,
            )
        );
        DB::table('data_type_options')->insert(array(
                'name'         => 'alt',
                'type'         => 'string',
                'is_mandatory' => 0,
                'default'      => '-',
                'data_type_id' => $imagesId,
            )
        );

        /**
         * Add Class option for Images data-type
         *
         * @edited maris
         * @date   2016/06/13
         */
        DB::table('data_type_options')->insert(array(
                'name'         => 'class',
                'type'         => 'string',
                'is_mandatory' => 0,
                'default'      => '-',
                'data_type_id' => $imagesId,
            )
        );

        $dropDowmId = DB::table('data_types')->insertGetId(array(
                'name' => 'dropdown'
            )
        );
        DB::table('data_type_options')->insert(array(
                'name'         => 'list',
                'type'         => 'string',
                'is_mandatory' => 0,
                'data_type_id' => $dropDowmId,
            )
        );

        $multiplePageId = DB::table('data_types')->insertGetId(array(
                'name' => 'multiplepage',
            )
        );
        DB::table('data_type_options')->insert(array(
                'name'         => 'listObject',
                'type'         => 'string',
                'is_mandatory' => 0,
                'data_type_id' => $multiplePageId,
            )
        );

        $singlePageId = DB::table('data_types')->insertGetId(array(
                'name' => 'singlepage'
            )
        );
        DB::table('data_type_options')->insert(array(
                'name'         => 'listObject',
                'type'         => 'string',
                'is_mandatory' => 0,
                'data_type_id' => $singlePageId,
            )
        );
        $vdoId = DB::table('data_types')->insertGetId(array(
                'name' => 'video'
            )
        );
        DB::table('data_type_options')->insert(array(
                'name'         => 'target',
                'type'         => 'string',
                'is_mandatory' => 0,
                'data_type_id' => $vdoId,
            )
        );

        $linkId = DB::table('data_types')->insertGetId(array(
                'name' => 'link'
            )
        );
        DB::table('data_type_options')->insert(array(
                'name'         => 'target',
                'type'         => 'string',
                'is_mandatory' => 0,
                'data_type_id' => $linkId,
            )
        );

        $emailId = DB::table('data_types')->insertGetId(array(
                'name' => 'email',
            )
        );
        DB::table('data_type_options')->insert(array(
                'name'         => 'target',
                'type'         => 'string',
                'is_mandatory' => 0,
                'data_type_id' => $emailId,
            )
        );

        $urlId = DB::table('data_types')->insertGetId(array(
                'name' => 'url'
            )
        );
        DB::table('data_type_options')->insert(array(
                'name'         => 'target',
                'type'         => 'string',
                'is_mandatory' => 0,
                'data_type_id' => $urlId,
            )
        );

        $fileId = DB::table('data_types')->insertGetId(array(
                'name' => 'file'
            )
        );
        DB::table('data_type_options')->insert(array(
                'name'         => 'max',
                'type'         => 'int',
                'default'      => 200000,
                'is_mandatory' => 0,
                'data_type_id' => $fileId,
            )
        );
        DB::table('data_type_options')->insert(array(
                'name'         => 'mimes',
                'type'         => 'string',
                'is_mandatory' => 0,
                'default'      => 'pdf,png,jpg,gif',
                'data_type_id' => $fileId,
            )
        );
    }
}

?>
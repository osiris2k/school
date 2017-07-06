<?php

use Illuminate\Database\Seeder;

// composer require laracasts/testdummy
use Illuminate\Support\Facades\DB;
use Laracasts\TestDummy\Factory as TestDummy;

class ContentObjectTypesTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('content_object_types')->insert(array(
                'content_object_type'       => 'CONTENT',
                'content_object_types_name' => 'Contents',
                'active'                    => 1,
                'created_at'                => strtotime('now'),
                'updated_at'                => strtotime('now')
            )
        );

        DB::table('content_object_types')->insert(array(
                'content_object_type'       => 'MEDIA',
                'content_object_types_name' => 'Medias',
                'active'                    => 1,
                'created_at'                => strtotime('now'),
                'updated_at'                => strtotime('now')
            )
        );

        DB::table('content_object_types')->insert(array(
                'content_object_type'       => 'MULTI_MEDIA',
                'content_object_types_name' => 'Multimedia',
                'active'                    => 1,
                'created_at'                => strtotime('now'),
                'updated_at'                => strtotime('now')
            )
        );
    }
}

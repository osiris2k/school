// <?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Model::unguard();

		// $tables = array('users','submissions','site_values',
  //           'sites','site_properties','roles',
  //   		'password_resets','menus','menu_groups',
  //           'medias','languages','languages_translation','form_values',
  //   		'form_properties','form_objects','data_types',
  //           'data_type_options','content_values','contents',
  //           'content_properties','content_objects');
  //   	foreach ($tables as $table) {
  //   		DB::table($table)->truncate();
  //   	}

        DB::table('roles')->insert(array(
                'name'     => 'Developer',
                'priority' => 9
            )
        );
        DB::table('roles')->insert(array(
                'name'       => 'Admin',
                'priority'   => 8
            )
        );
        DB::table('roles')->insert(array(
                'name'     => 'Editorial',
                'priority' => 7
            )
        );
         DB::table('roles')->insert(array(
                'name'     => 'Supervisor',
                'priority' => 9
            )
        );
    	DB::table('languages')->insert(array(
                'name'       => 'en',
                'initial'    => '1',
                'created_at' => new DateTime,
                'updated_at' => new DateTime
            )
        );
        DB::table('users')->insert(array(
                'email'      => 'developers@quo-global.com',
                'firstname'  => 'developers',
                'lastname'   => 'developers',
                'role_id'    => '1',
                'password'   => \Hash::make('developers'),
                'created_at' => new DateTime,
                'updated_at' => new DateTime
            )
        );
        DB::table('users')->insert(array(
                'email'      => 'admin@quo-global.com',
                'firstname'  => 'admin',
                'lastname'   => 'admin',
                'role_id'    => '2',
                'password'   => \Hash::make('admin'),
                'created_at' => new DateTime,
                'updated_at' => new DateTime
            )
        );
        DB::table('users')->insert(array(
                'email'      => 'editorial@quo-global.com',
                'firstname'  => 'editorial',
                'lastname'   => 'editorial',
                'role_id'    => '3',
                'password'   => \Hash::make('editorial'),
                'created_at' => new DateTime,
                'updated_at' => new DateTime
            )
        );
        DB::table('users')->insert(array(
                'email'      => 'supervisor@quo-global.com',
                'firstname'  => 'supervisor',
                'lastname'   => 'supervisor',
                'role_id'    => '4',
                'password'   => \Hash::make('supervisor'),
                'created_at' => new DateTime,
                'updated_at' => new DateTime
            )
        );
        DB::table('sites')->insert(array(
                'name' => 'developer',
                'main_site'=>1,
                'created_at' => new DateTime,
                'updated_at' => new DateTime
            )
        );
		 DB::table('site_languages')->insert(array(
                'site_id'=>1,
				'language_id' => 1,
                'created_at' => new DateTime,
                'updated_at' => new DateTime
            )
        );
        
        
        $this->call('DataTypeTableSeeder');
        $this->call('CountriesTableSeederTableSeeder');
        $this->call('ContentObjectTypesTableSeeder');
	}

}

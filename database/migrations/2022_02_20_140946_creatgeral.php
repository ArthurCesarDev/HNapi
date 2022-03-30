<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function(Blueprint $table){
        $table->id();
        $table->string('name');
        $table->string('email')->unique();
        $table->string('matricular')->unique();
        $table->string('password');
          });

      Schema::create('units', function(Blueprint $table){
        $table->id();
        $table->string('name');
        $table->string('id_owner');           
          });

      Schema::create('unitpeoples', function(Blueprint $table){
        $table->id();
        $table->integer('id_unit');
        $table->string('name');
        $table->date('birthdate');     
          });

       Schema::create('unitvehicles', function(Blueprint $table){
        $table->id();
        $table->integer('id_unit');
        $table->string('title');
        $table->string('color');
        $table->string('place');  
          });

       Schema::create('unitpets', function(Blueprint $table){
        $table->id();
        $table->integer('id_unit');
        $table->string('name');
        $table->string('racer');     
           });

       Schema::create('walls', function(Blueprint $table){
       $table->id();
       $table->string('title');
       $table->string('body');
       $table->datetime('datecreat');   
           });

       Schema::create('walllikes', function(Blueprint $table){
        $table->id();
        $table->integer('id_wall');
        $table->integer('id_user');
           
           });

        Schema::create('docs', function(Blueprint $table){
         $table->id();
         $table->string('title');
         $table->string('fileurl');     
           });

       Schema::create('billets', function(Blueprint $table){
        $table->id();
        $table->integer('id_unit');
        $table->string('title');
        $table->string('fileurl');      
           });

       Schema::create('warnings', function(Blueprint $table){
        $table->id();
        $table->integer('id_unit');
        $table->string('title');
        $table->string('status')->default('IN_REVIEW');
        $table->date('datecreat');
        $table->text('photos');        
          });

        Schema::create('foundandlost', function(Blueprint $table){
        $table->id();
        $table->string('status')->default('LOST');
        $table->string('photo');
        $table->string('description');
        $table->string('whare');
        $table->date('datecreate');      
          });
            
         Schema::create('areas', function(Blueprint $table){
         $table->id();
         $table->integer('allowed')->default(1); 
         $table->string('title');
         $table->string('cover'); 
         $table->string('days');   
         $table->time('start_time'); 
         $table->time('end_time');        
          });


         Schema::create('areasdisabledays', function(Blueprint $table){
         $table->id();
         $table->integer('id_area'); 
         $table->date('day');  
               
             });         
        Schema::create('reservations', function(Blueprint $table){
        $table->id();
        $table->integer('id_unit');
        $table->integer('area');  
        $table->datetime('reservation_date');  
                   
  });    
            

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropifExists('users');
        Schema::dropifExists('units');
        Schema::dropifExists('unitpeoples');
        Schema::dropifExists('unitvehicles');
        Schema::dropifExists('unitpets');
        Schema::dropifExists('walls');
        Schema::dropifExists('walllikes');
        Schema::dropifExists('docs');
        Schema::dropifExists('billets');
        Schema::dropifExists('warnings');
        Schema::dropifExists('fondanlosts');
        Schema::dropifExists('areas');
        Schema::dropifExists('areasdisabledays');
        Schema::dropifExists('reservations');

    }
};

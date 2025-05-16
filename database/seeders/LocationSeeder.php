<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Location;  // ← IMPORT


class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    // database/seeders/LocationSeeder.php
public function run()
{
  $comunidades = ['Capachica','Miraflores','Lago Azul','Toctoro','Siale','San Cristóbal','Yancaco Grande','Chillora','Capano','Collpa'];
  foreach($comunidades as $c){
    Location::firstOrCreate(['name'=>$c,'type'=>'comunidad']);
  }
  $centros = ['Llachón','Yapura','Hilata','Ccotos','Escallani','Izañura'];
  foreach($centros as $c){
    Location::firstOrCreate(['name'=>$c,'type'=>'centro_poblado']);
  }
}

}

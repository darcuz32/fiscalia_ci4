<?php 
namespace App\Libraries;

require_once __DIR__ . '/ssp.class.php';


class Datatable{
   function simple( $params, $query, $primaryKey, $columns){
      $config = config('Database');
      $group  = $config->defaultGroup;

      $sql_details = [
         'user' => $config->{$group}['username'],
         'pass' => $config->{$group}['password'],
         'db'   => $config->{$group}['database'],
         'host' => $config->{$group}['hostname'],
      ];
      $query="($query) xyz";
      
      return \SSP::simple($params, $sql_details, $query, $primaryKey, $columns);

   }
}
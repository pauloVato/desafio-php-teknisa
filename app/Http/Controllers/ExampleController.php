<?php

namespace App\Http\Controllers;

class ExampleController extends Controller
{
    
    public function __construct()
    {
        
    }

   public function createCar(Request $request){
      $email = Email::create($request->all());
      return response()->json($email);
  }
 
  public function updateCar(Request $request, $id){
      $email  = Email::find($id);
      $email->save();
      return response()->json($email);
  }  
  public function deleteCar($id){
      $email  = Email::find($id);
      $email->delete();
      return response()->json('Removed successfully.');
  }
  public function index(){
      $emails  = Email::all();
      return response()->json($emails);
 
  }

}

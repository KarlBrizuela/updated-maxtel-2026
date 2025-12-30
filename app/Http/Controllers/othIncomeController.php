<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;
use Storage;
use Yajra\DataTables\DataTables;

class othIncomeController extends Controller
{

    function search_multi_array($array, $key, $value) {
        foreach ($array as $subarray) {
            if (isset($subarray[$key]) && $subarray[$key] == $value) {
                return $subarray;
            }
        }
        return null;
    }
    

    public function income_management(){
   

            $tbl_employee = json_decode(json_encode(DB::connection("intra_payroll")->table("tbl_employee")->where("is_active", 1)->orderby("last_name")->orderby("first_name")->orderby("middle_name")->get()),true);
            $lib_bir_non_taxable = json_decode(json_encode(DB::connection("intra_payroll")->table("lib_bir_non_taxable")->where("is_active", 1)->get()),true);
            
            $lib_bir_taxable = json_decode(json_encode(DB::connection("intra_payroll")->table("lib_bir_taxable")->where("is_active", 1)->get()),true);
            $lib_income = json_decode(json_encode(DB::connection("intra_payroll")->table("lib_income")->where("is_active", 1)->get()),true);
       
            return view("other_income.index")
                ->with("lib_bir_non_taxable", $lib_bir_non_taxable)
                ->with("lib_bir_taxable", $lib_bir_taxable)
                ->with("lib_income", $lib_income)
                ->with("tbl_employee", $tbl_employee)
            ;  
    }

    public function employee_array(){
        $tbl_employee = DB::connection("intra_payroll")->table("tbl_employee")->where("is_active", 1)->orderby("last_name")->orderby("first_name")->orderby("middle_name")->get();

        return json_encode($tbl_employee);
        

    }

    public function emp_other_income_data(Request $request){
       $data = DB::connection("intra_payroll")->table("tbl_income_file")
            ->where("income_id", $request->id)
            ->get();

        return json_encode($data);


    }

    public function save_other_income(Request $request){
        $insert_array = array();
        $other_income_id = $request->other_income_id;
        $amount = $request->amount;
        $amount_2 = $request->amount_2;
        $amount_3 = $request->amount_3;
        $amount_4 = $request->amount_4;
        $amount_5 = $request->amount_5;
        
        $income_type = $request->income_type;
        $user_encoder = Auth::user()->id;
        $todate = date("Y-m-d H:i:s");
        $selected_emp = $request->select_emp;
        if($request->select_emp == "custom_emp"){
          	if($request->delimited != null){
            $list = explode("|", $request->delimited);
           	
                foreach($list as $emp_list){
                  
                    $data = explode(";", $emp_list);
                   
                    $ins_arr = array(
                        "emp_id" => $data[0],
                        "income_id" => $other_income_id,
                        "income_type" => $data[1],
                        "date_created" => $todate,
                        "user_id" => $user_encoder,
                        "selected_emp" => $selected_emp
                    );
                   
                    if($data[1] == "DAILY" || $data[1] == "MONTHLY" ){
                        $ins_arr["amount"] = $data[2];
                        $ins_arr["amount_2"] = "0";
                        $ins_arr["amount_3"] = "0";
                        $ins_arr["amount_4"] = "0";
                        $ins_arr["amount_5"] = "0";
                    }elseif($data[1] == "SEMI"){
                        $ins_arr["amount"] = $data[2];
                        $ins_arr["amount_2"] = $data[3];
                        $ins_arr["amount_3"] = "0";
                        $ins_arr["amount_4"] = "0";
                        $ins_arr["amount_5"] = "0";
                    }elseif($data[1] == "WEEKLY"){
                        $ins_arr["amount"] = $data[2];
                        $ins_arr["amount_2"] = $data[3];
                        $ins_arr["amount_3"] = $data[4];
                        $ins_arr["amount_4"] = $data[5];
                        $ins_arr["amount_5"] = $data[6];
                        
                    }

                    array_push($insert_array, $ins_arr); 
                }
              
             }
          
          
          
              
        }elseif($request->select_emp == "all_emp"){
            $list = DB::connection("intra_payroll")->table("tbl_employee")
            ->select("id as emp_id", DB::raw("CONCAT('".$other_income_id."') as income_id"), DB::raw("CONCAT('".$amount."') as amount"), DB::raw("CONCAT('".$amount_2."') as amount_2"), DB::raw("CONCAT('".$amount_3."') as amount_3"), DB::raw("CONCAT('".$amount_4."') as amount_4"), DB::raw("CONCAT('".$amount_5."') as amount_5"), DB::raw("CONCAT('".$income_type."') as income_type") , DB::raw("CONCAT('".$todate."') as date_created"), DB::raw("CONCAT('".$user_encoder."') as user_id"), DB::raw("CONCAT('".$selected_emp."') as selected_emp") )
            
            ->where("is_active", 1)
            ->get();
            $insert_array = json_decode(json_encode($list), true);
        }elseif($request->select_emp == "daily_emp"){
            $list = DB::connection("intra_payroll")->table("tbl_employee")
            ->select("id as emp_id", DB::raw("CONCAT('".$other_income_id."') as income_id"), DB::raw("CONCAT('".$amount."') as amount"), DB::raw("CONCAT('".$amount_2."') as amount_2"), DB::raw("CONCAT('".$amount_3."') as amount_3"), DB::raw("CONCAT('".$amount_4."') as amount_4"), DB::raw("CONCAT('".$amount_5."') as amount_5"), DB::raw("CONCAT('".$income_type."') as income_type") , DB::raw("CONCAT('".$todate."') as date_created"), DB::raw("CONCAT('".$user_encoder."') as user_id"), DB::raw("CONCAT('".$selected_emp."') as selected_emp") )
            ->where('salary_type', 'DAILY')
            ->where("is_active", 1)
            ->get();
            $insert_array = json_decode(json_encode($list), true);

        }elseif($request->select_emp == "monthly_emp"){
            $list = DB::connection("intra_payroll")->table("tbl_employee")
            ->select("id as emp_id", DB::raw("CONCAT('".$other_income_id."') as income_id"), DB::raw("CONCAT('".$amount."') as amount"), DB::raw("CONCAT('".$amount_2."') as amount_2"), DB::raw("CONCAT('".$amount_3."') as amount_3"), DB::raw("CONCAT('".$amount_4."') as amount_4"), DB::raw("CONCAT('".$amount_5."') as amount_5"), DB::raw("CONCAT('".$income_type."') as income_type") , DB::raw("CONCAT('".$todate."') as date_created"), DB::raw("CONCAT('".$user_encoder."') as user_id"), DB::raw("CONCAT('".$selected_emp."') as selected_emp") )
            ->where('salary_type', 'MONTHLY')
            ->where("is_active", 1)
            ->get();
            $insert_array = json_decode(json_encode($list), true);
        }else{
            return json_encode("error");
        }

        DB::beginTransaction();
        try {
            
            DB::connection("intra_payroll")->table("tbl_income_file")
                ->where("income_id", $other_income_id)
                ->delete();
			
          	if(count($insert_array)>0){
            	DB::connection("intra_payroll")->table("tbl_income_file")  
                	->insert($insert_array); 
            }
            

            DB::commit();
            return json_encode("success");
        } catch (\Throwable $th) {
            DB::rollback();

            return json_encode($th->getMessage());
        }


       

     
    }

    



    public function oth_library_list(Request $request){
        $page_permission = Auth::user()->access[$request->page]["access"];
        $lib_bir_non_taxable = json_decode(json_encode(DB::connection("intra_payroll")->table("lib_bir_non_taxable")->where("is_active", 1)->get()),true);
            
        $lib_bir_taxable = json_decode(json_encode(DB::connection("intra_payroll")->table("lib_bir_taxable")->where("is_active", 1)->get()),true);


        $lib_bir_taxable = json_decode(json_encode(DB::connection("intra_payroll")->table("lib_bir_taxable")->where("is_active", 1)->get()),true);
        

        $data = DB::connection("intra_payroll")->table("lib_income")
            ->orderBy("name")
            ->get();

                $data = collect($data);

                return Datatables::of($data)
                ->addColumn('is_active', function($row){
                    if($row->is_active){
                        $btn = "<a class='btn btn-sm btn-success' > Active </a>";
                    }else{
                        $btn = "<a class='btn btn-sm btn-warning' > Inactive </a>";
                    }
                    return $btn;
                })

                ->addColumn('is_regular', function($row){
                    if($row->is_regular){
                        $btn = "<a class='btn btn-sm btn-success' > Regular </a>";
                    }else{
                        $btn = "<a class='btn btn-sm btn-warning' > One Time </a>";
                    }
                    return $btn;
                })
                  
                  
                
                ->addColumn('date_updated', function($row){
                  return date("F j, Y", strtotime($row->date_updated));
                })
                
                ->addColumn('tax_item', function($row) use ($lib_bir_non_taxable, $lib_bir_taxable){
                        if($row->tax_type == "NON"){
                            if($row->tax_item != "0"){
                                $data = $this->search_multi_array($lib_bir_non_taxable, "id", $row->tax_item);
                                return $data["name"];
                            }else{
                                return "Hidden Income";
                            }
                        }elseif($row->tax_type == "TAX"){
                            if($row->tax_item != "0"){
                                $data = $this->search_multi_array($lib_bir_taxable, "id", $row->tax_item);
                                return $data["name"];
                            }else{
                                return "Basic Pay";
                            }
                        }else{
                            return "Unknown";
                        }


                })
                ->addColumn('tax_type', function($row){
                    if($row->tax_type == "NON"){
                        return "NON TAXABLE";
                    }else if($row->tax_type == "TAX"){
                        return "TAXABLE";
                    }else{
                        return "Unknown";
                    }
                  })
                
                 ->addColumn('action', function($row) use ($page_permission){
                    if(preg_match("/U/i", $page_permission)){
                        $btn = "<a class='btn btn-sm btn-info' 
                        data-toggle='modal' 
                        data-id='".$row->id."'
                        data-code='".$row->code."'
                        data-name='".$row->name."'
                        
                        data-description='".$row->description."'
                        data-is_regular='".$row->is_regular."'
                        
                        data-tax_type='".$row->tax_type."'
                        data-tax_item='".$row->tax_item."'

                        data-is_active='".$row->is_active."'
                        data-target='#oth_library_modal'
                        
                        > Edit </a>";

                        if($row->is_regular){




                            $btn .= "<a class='ml-1 btn btn-sm btn-success' 
                            data-toggle='modal' 
                            data-id='".$row->id."'
                            data-code='".$row->code."'
                            data-name='".$row->name."'
                            
                            data-description='".$row->description."'
                            data-is_regular='".$row->is_regular."'
                            
                            data-tax_type='".$row->tax_type."'
                            data-tax_item='".$row->tax_item."'
    
                            data-is_active='".$row->is_active."'
                            data-target='#add_edit_employee'
                            
                            > Add/Edit Employee </a>";
                        }
                        // add delete in income
                        $btn .= " <button 
                        class='btn btn-sm btn-danger'
                        onclick='delete_income(" . $row->id . ")'
                        >
                        Delete
                        </button>";

                    }else{
                        $btn = "";
                    }
    
                   
                        return $btn;
                 })

                 ->rawColumns(['is_active','action','is_regular'])
                ->make(true);

    }

    function save_library(Request $request){
        $save_library = $request->save_library;
        $lib_code = $request->lib_code;
        $lib_name = $request->lib_name;
        $lib_desc = $request->lib_desc;
        $tax_type = $request->tax_type;
        $tax_item_non = $request->tax_item_non;
        $tax_item_tax = $request->tax_item_tax;
        $is_active = $request->is_active;
        $lib_is_regular = $request->lib_is_regular;

        if($tax_type == "NON"){
            $item = $tax_item_non;
        }elseif($tax_type == "TAX"){
            $item = $tax_item_tax;
        }else{
            $item = 0;
        }


        DB::beginTransaction();
        try {
            if($save_library == "new"){
                $check = DB::connection("intra_payroll")->table("lib_income")
                    ->where("code", $lib_code)
                    ->first();
                if($check != null){
                    return json_encode("duplicate");
                }
            
                
                $insert_array = array(
                    "code" => $lib_code,
                    "name" => $lib_name,
                    "description" => $lib_desc,
                    "is_regular" => $lib_is_regular,

                    "tax_type" => $tax_type,
                    "tax_item" => $item,
                    "is_active" => $is_active,
                    "date_created" => date("Y-m-d H:i:s"),
                    "user_id" => Auth::user()->id
                );
                DB::connection("intra_payroll")->table("lib_income")
                ->insert($insert_array);
            }else{
                $check = DB::connection("intra_payroll")->table("lib_income")
                    ->where("code", $lib_code)
                    ->where("id", "!=",$save_library)
                    ->first();
                if($check != null){
                    return json_encode("duplicate");
                }

                $update_array = array(
                    "code" => $lib_code,
                    "name" => $lib_name,
                    "description" => $lib_desc,
                    "is_regular" => $lib_is_regular,
                    "tax_type" => $tax_type,
                    "tax_item" => $item,
                    "is_active" => $is_active,
                    "user_id" => Auth::user()->id
                );
                DB::connection("intra_payroll")->table("lib_income")
                    ->where("id", $save_library)
                    ->update($update_array);


            }




            DB::commit();
            return json_encode("true");
        } catch (\Throwable $th) {
            DB::rollback();
            return json_encode($th->getMessage());
            //throw $th;
        }


        


    }
    // add delete in income
    public function delete_income(Request $request){
        DB::beginTransaction();
            try {
                DB::connection("intra_payroll")->table("lib_income")
                    ->where("id", $request->id)
                    ->delete();
                DB::commit();
                return json_encode("Deleted");
            } catch (\Throwable $th) {
                DB::rollback();
                return json_encode($th->getMessage());
            }

    }

}

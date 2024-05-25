<?php

namespace App\Http\Controllers;

use App\Models\Properties;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PropertiesController extends Controller
{
    //

    public function __invoke()
    {
        return response()->json(['message' => 'Welcome to the PropertiesController'], 200);
    }

    public function index(Request $request)
    {
       // return all records of properties with pagination from request
        $properties = Properties::paginate($request->per_page ?? 15);
        return response()->json($properties, 200);

    }

    public function retrievePropertyConditionally(Request $request)
    {
       $query_params =  $request->coloumn;
       $properties = Properties::query();
       foreach ($query_params as $key => $value) {
            if($key == 'amenities'){
                $properties->whereJsonContains('amenities', $value);
            }else{
                $properties->where($key, $value);
            }
       }
       // apply pagination
         $properties = $properties->paginate($request->per_page ?? 15);
        return response()->json($properties, 200);
    }


    public function storeDataFromCSV(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt',
        ]);

        $file = $request->file('file');
        $filePath = $file->getPathname();

        return $this->storeDataViaFile($filePath);
    }

    private function storeDataViaFile($filePath, $data_via_request = [])
    {
       $user_id = auth()->user()->id;
        $count = 0;

        // Map CSV headers to database column names
        $columnMapping = [
            'customer\'s mobile no.' => 'customer_mobile_no',
            'email id' => 'email_id',
            'customer name' => 'customer_name',
            'property for' => 'property_for',
            'property type' => 'property_type',
            'price' => 'price',
            'booking amount/security amount' => 'booking_amount_security_amount',
            'maintenance charges' => 'maintenance_charges',
            'address' => 'address',
            'state' => 'state',
            'city' => 'city',
            'location / colony' => 'location_colony',
            'name of project/society' => 'name_of_project_society',
            'covered area' => 'covered_area',
            'ca unit' => 'ca_unit',
            'plot area' => 'plot_area',
            'pa unit' => 'pa_unit',
            'no of bedroom' => 'no_of_bedroom',
            'no of bathroom' => 'no_of_bathroom',
            'no. of balconies' => 'no_of_balconies',
            'furnished' => 'furnished',
            'possession status' => 'possession_status',
            'age of const.' => 'age_of_const',
            'floor number' => 'floor_number',
            'total floors in building' => 'total_floors_in_building',
            'personal pantry - yes/no' => 'personal_pantry',
            'personal washroom - yes/no' => 'personal_washroom',
            'floors allowed for construction' => 'floors_allowed_for_construction',
            'any construction done - yes/no' => 'any_construction_done',
            'boundary wall made - yes/no' => 'boundary_wall_made',
            'is in a gated colony - yes/no' => 'is_in_a_gated_colony',
            'transaction type' => 'transaction_type',
            'additional rooms' => 'additional_rooms',
            'no. of car parking (covered)' => 'no_of_car_parking_covered',
            'no of car parking (open)' => 'no_of_car_parking_open',
            'number seats' => 'number_seats',
            'type of coworking space' => 'type_of_coworking_space',
            'amenities' => 'amenities',

        ];

        // Check if all headers are present

        try {
            // Iterate through each row in the CSV file
            if (empty($data_via_request)) {
                $file = fopen($filePath, 'r');

                // Get headers from the CSV file
                $headers = fgetcsv($file);

                $missingHeaders = array_diff(array_keys($columnMapping), $headers);
                if (!empty($missingHeaders)) {
                    return response()->json(['message' => 'Missing headers: ' . implode(', ', $missingHeaders)], 400);
                }

                while (($row = fgetcsv($file)) !== false) {
                    $return = $this->storeData($headers,$user_id, $row,$columnMapping, $count);
                    if($return->getStatusCode() != 200){
                        return $return;
                    }
                    $count++;
                }

                fclose($file);
            } else {
               return $this->storeData(array_keys($data_via_request), $user_id,array_values($data_via_request),$columnMapping);
            }
        } catch (\Exception $e) {

            Log::error("error_property_create",['message' =>$e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['message' => $e->getMessage() . ' last Successfull id => ' . $count], 400);
        }
        return response()->json(['message' => 'Data stored successfully'], 200);
    }

    public function storeDataViaRequest(Request $request)
    {

        $data = $request->all();

        return $this->storeDataViaFile(null, $data);
    }

    public function storeData($headers,$user_id, $row,$columnMapping, $count = 0)
    {
        $data = [];
        // Map CSV values to database column values based on headers
        foreach ($headers as $index => $header) {
            //validations after doing strtolower on all headers

            $header = strtolower($header);

            if ($header == 'customer\'s mobile no.') {
                if (!is_numeric($row[$index])) {
                    return response()->json(['message' => 'Customer\'s mobile No. should be numeric' . ' last Successfull id => ' . $count], 400);
                }
            } elseif ($header == 'email id') {
                if (!filter_var($row[$index], FILTER_VALIDATE_EMAIL)) {
                    return response()->json(['message' => 'Email ID is not valid' . ' last Successfull id => ' . $count], 400);
                }
            } elseif ($header == 'price' || $header == 'booking amount/security amount' || $header == 'maintenance charges' || $header == 'covered area' || $header == 'plot area') {
                if (!is_numeric($row[$index])) {
                    return response()->json(['message' => $header . ' should be numeric' . ' last Successfull id => ' . $count], 400);
                }
            } elseif ($header == 'no of bedroom' || $header == 'no of bathroom' || $header == 'no. of balconies' || $header == 'age of const.' || $header == 'floor number' || $header == 'total floors in building' || $header == 'floors allowed for construction' || $header == 'no. of car parking (covered)' || $header == 'no of car parking (open)' || $header == 'number seats') {
                if (!is_numeric($row[$index])) {
                    return response()->json(['message' => $header . ' should be numeric' . ' last Successfull id => ' . $count], 400);
                }
            } elseif ($header == 'furnished') {
                // check if in array from cont furnished
                if (!array_key_exists(strtolower($row[$index]), Properties::FURNISHED)) {
                    return response()->json(['message' => 'Furnished is not valid' . ' last Successfull id => ' . $count], 400);
                } else {
                    $row[$index] = Properties::FURNISHED[strtolower($row[$index])];
                }
            } elseif ($header == 'personal pantry - yes/no' || $header == 'personal washroom - yes/no' || $header == 'any construction done - yes/no' || $header == 'boundary wall made - yes/no' || $header == 'is in a gated colony - yes/no') {
                if (strtolower($row[$index]) != 'yes' && strtolower($row[$index]) != 'no') {
                    return response()->json(['message' => $header . ' should be either Yes or No' . ' last Successfull id => ' . $count], 400);
                } else {
                    $row[$index] = strtolower($row[$index]) == 'yes' ? true : false;
                }
            } elseif ($header == 'transaction type') {
                // check if in array from cont transaction type
                if (!array_key_exists(strtolower($row[$index]), Properties::TRANSACTION_TYPE)) {
                    return response()->json(['message' => 'Transaction Type is not valid' . ' last Successfull id => ' . $count], 400);
                } else {
                    $row[$index] = Properties::TRANSACTION_TYPE[strtolower($row[$index])];
                }
            } elseif ($header == 'possession status') {
                // check if in array from cont possession status
                if (!array_key_exists(strtolower($row[$index]), Properties::POSSESSION_STATUS)) {
                    return response()->json(['message' => 'Possession Status is not valid' . ' last Successfull id => ' . $count], 400);
                } else {
                    $row[$index] = Properties::POSSESSION_STATUS[strtolower($row[$index])];
                }
            } elseif ($header == 'type of coworking space') {
                // check if in array from cont type of coworking space
                if (!array_key_exists(strtolower($row[$index]), Properties::TYPE_OF_COWORKING_SPACE)) {
                    return response()->json(['message' => 'Type of Coworking Space is not valid' . ' last Successfull id => ' . $count], 400);
                } else {
                    $row[$index] = Properties::TYPE_OF_COWORKING_SPACE[strtolower($row[$index])];
                }
            } elseif ($header == 'ca unit' || $header == 'pa unit') {
                // check if in array from cont ca unit and pa unit
                if (!array_key_exists(strtolower($row[$index]), Properties::CA_UNIT) && !array_key_exists(strtolower($row[$index]), Properties::PA_UNIT)) {
                    return response()->json(['message' => $header . ' is not valid' . ' last Successfull id => ' . $count], 400);
                } else {
                    if ($header == 'ca unit') {
                        $row[$index] = Properties::CA_UNIT[strtolower($row[$index])];
                    } else {
                        $row[$index] = Properties::PA_UNIT[strtolower($row[$index])];
                    }
                }
            } elseif ($header == 'amenities') {
                // check if in array from cont amenities
                $amenities = explode(',', $row[$index]);
                foreach ($amenities as $amenity) {
                    if (!array_key_exists(trim(strtolower($amenity)), Properties::AMENITIES)) {
                        return response()->json(['message' => 'Amenity ' . $amenity . ' is not valid' . ' last Successfull id => ' . $count], 400);
                    } else {
                        $amen[] = Properties::AMENITIES[$amenity];
                    }
                }
                $row[$index] = json_encode($amen);
            } elseif ($header == 'state') {
                // check if in array from cont state
                $a = DB::table('state')->where('state', $row[$index])->first();
                if (!$a) {
                    return response()->json(['message' => 'State is not valid' . ' last Successfull id => ' . $count], 400);
                } else {
                    $row[$index] = $a->id;
                }
            } elseif ($header == 'city') {
                // check if in array from cont city
                $a = DB::table('city')->where('name', $row[$index])->first();
                if (!$a) {
                    return response()->json(['message' => 'City is not valid' . ' last Successfull id => ' . $count], 400);
                } else {
                    $row[$index] = $a->id;
                }
            }

            $columnName = $columnMapping[$header] ?? null;
            if ($columnName !== null) {
                // Process special cases like boolean values and JSON data
                $data[$columnName] = $row[$index];
            }
        }
        $data['user_id'] = $user_id;

        // Store data into database using Eloquent ORM
        Properties::insert($data);

        return response()->json(['message' => 'Property created successfully'], 200);
    }

    public function delete(Request $request)
    {
        $property = Properties::find($request->id);
        if ($property) {
            if (!in_array(auth()->user()->role, [User::ROLE_ADMIN,User::ROLE_OPS]) && $property->user_id != auth()->user()->id) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }
            $property->delete();
            return response()->json(['message' => 'Property deleted successfully'], 200);
        } else {
            return response()->json(['message' => 'Property not found'], 404);
        }
    }

    public function update(Request $request){
        $property = Properties::find($request->id);
        if ($property) {
            if (!in_array(auth()->user()->role, [User::ROLE_ADMIN,User::ROLE_OPS]) && $property->user_id != auth()->user()->id) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }
            $updatecolumn = $request->updateColoumns;
            foreach ($updatecolumn as $key => $value) {
                if($key == 'amenities'){
                $property->$key = json_encode( explode(',',$value));
                }else{
                    $property->$key = $value;
                }
            }
            try {
                $property->save();
            } catch (\Exception $e) {
                return response()->json(['message' => $e->getMessage()], 400);
            }
            return response()->json(['message' => 'Property updated successfully'], 200);
        } else {
            return response()->json(['message' => 'Property not found'], 404);
        }
    }
}

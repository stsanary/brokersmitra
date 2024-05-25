<?php

namespace App\Http\Controllers;

use App\Models\Plans;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PlansController extends Controller
{

    public function __invoke()
    {
        return response()->json(['message' => 'Welcome to the PlansController'], 200);
    }

    public function index()
    {   
        $plans = Plans::all();
        return response()->json($plans, 200);
    }

    public function store(Request $request)
    {
        //validations
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'string',
            'price' => 'required|integer',
            'duration' => 'required|integer',
            'status' => 'required|integer',
        ]);

        if (!in_array(auth()->user()->role, [User::ROLE_ADMIN])) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $plan = new Plans();
        $plan->name = $request->name;
        $plan->description = $request->description ?? '';
        $plan->price = $request->price;
        $plan->duration = $request->duration;
        $plan->status = $request->status;
        $plan->save();
        return response()->json(['message' => 'Plan created successfully'], 201);
    }

    public function show($id)
    {
        $plan =  Plans::find($id);
        return response()->json($plan, 200);
    }

    public function update(Request $request)
    {
        // validation on id
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);

        if (!in_array(auth()->user()->role, [User::ROLE_ADMIN])) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $plan = Plans::find($request->id);
        $plan->name = $request->name ?? $plan->name;
        $plan->description = $request->description ?? $plan->description;
        $plan->price = $request->price ?? $plan->price;
        $plan->duration = $request->duration ?? $plan->duration;
        $plan->status = $request->status ?? $plan->status;
        $plan->save();
        return response()->json(['message' => 'Plan updated successfully'], 200);
    }

    public function destroy($id)
    {
        $plan = Plans::find($id);

        if (!in_array(auth()->user()->role, [User::ROLE_ADMIN])) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // error if plan not found
        if (!$plan) {
            return response()->json(['message' => 'Plan not found'], 404);
        }
        $plan->delete();
        return response()->json(['message' => 'Plan deleted successfully'], 200);
    }

    public function activate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'plan_id' => 'required|integer',
            'user_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $user =  User::find($request->user_id);
        $plan = Plans::find($request->plan_id);
        $validity = now()->addDays($plan->duration);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if (!$plan) {
            return response()->json(['message' => 'Plan not found'], 404);
        }

        $user->plan_id = $plan->id;
        $user->plan_validity = $validity;
        $user->save();
        return response()->json(['message' => 'Plan with id ' . $plan->id . ' activated successfully'], 200);
    }


    public function deactivate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'plan_id' => 'required|integer',
            'user_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $user =  User::find($request->user_id);
        $plan = Plans::find($request->plan_id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if (!$plan) {
            return response()->json(['message' => 'Plan not found'], 404);
        }

        $user->plan_id = null;
        $user->plan_validity = null;
        $user->save();
        return response()->json(['message' => 'Plan with id ' . $plan->id . ' deactivated successfully'], 200);
    }

    public function getActivePlans()
    {
        return response()->json(['message' => 'List of all active plans'], 200);
    }

    public function getInactivePlans()
    {
        return response()->json(['message' => 'List of all inactive plans'], 200);
    }
}

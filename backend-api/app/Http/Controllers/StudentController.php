<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::all();
        return response()->json([
            "data" => $students
        ], 200);
    }
    public function show($id)
    {
        $students = Student::find($id);
        return response()->json([
            "data" => $students
        ], 200);
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            "student_id"=> "required",
            "first_name"=>"required",
            "last_name"=>"required"
        ],[
            "student_id"=> "กรุณากรอกรหัสนักศึกษา",
            "first_name"=>"กรุณากรอกชื่อ",
            "last_name"=>"กรุณากรอกสกุล"
        ]);
        $students = Student::find($id);
        $students->student_id = $request->input('student_id');
        $students->first_name = $request->input('first_name');
        $students->last_name = $request->input('last_name');
        $students->save();
        return response()->json([
            "message" => "Update Student Successfully!!"
        ], 200);
    }
    public function store(Request $request)
    {
        $request->validate([
            "student_id"=> "required",
            "first_name"=>"required",
            "last_name"=>"required"
        ],[
            "student_id"=> "กรุณากรอกรหัสนักศึกษา",
            "first_name"=>"กรุณากรอกชื่อ",
            "last_name"=>"กรุณากรอกสกุล"
        ]);
        $students = new Student;
        $students->student_id = $request->input('student_id');
        $students->first_name = $request->input('first_name');
        $students->last_name = $request->input('last_name');
        $students->save();
        return response()->json([
            "message" => "Create Student Successfully!!"
        ], 200);
    }
    public function destroy($id)
    {
        $students = Student::find($id);
        $students->delete();
        return response()->json([
            "message" => "Delete Student Successfully!!"
        ], 200);
    }
}

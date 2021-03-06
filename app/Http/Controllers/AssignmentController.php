<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AssignmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $assignments = Assignment::all();
        return view(
            'assignment.index',
            ['assignments' => $assignments]
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('assignment.create');
    }

    /**
     * Upload file.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function upload(Request $request)
    {
        $this->validate($request, [
            'uploaded_file' => ['required'],
        ]);
        $file =$request->file('uploaded_file');
        $assignment = new Assignment();
        $assignment->name = $file->getClientOriginalName();
        $filePath = "public/assignment/" . $assignment->name;
        $assignment->path = $filePath;
        if(!Storage::exists($filePath)) {
            $assignment->save();
            $path = Storage::putFileAs('public/assignment', $file, $assignment->name);

            if ($path) {
                return redirect()->action([AssignmentController::class, 'index'])->with('success_upload', 'Upload thành công!');
            }
            return redirect()->action([AssignmentController::class, 'index'])->with('error_upload', 'Upload failt!');
        }
        $path = Storage::putFileAs('public/assignment', $file, $assignment->name);
        if ($path) {
            return redirect()->action([AssignmentController::class, 'index'])->with('success_upload', 'Đã update file thành công!');
        }
        return redirect()->action([AssignmentController::class, 'index'])->with('error_upload', 'Update file failt!');
    }

    /**
     * Download file.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function download($id)
    {
        $assignment = Assignment::find($id);

        $index = strripos($assignment->path,"/");
        $name = substr($assignment->path, $index + 1);

        return Storage::download($assignment->path, $name);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $assignment = Assignment::find($id);
        Storage::delete($assignment->path);
        $assignment->delete();
        return redirect()->action([AssignmentController::class, 'index'])->with('success_delete', 'Delete thành công!');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TransactionType;
use DataTables;

class TransactionTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return view('admin.transactiontype.index');
    }

    public function getList(){
        $transactiontype = TransactionType::select([
            'id',
            'title',
            'title_es',
            'title_nl',
        ]);
        return DataTables::of($transactiontype)
        ->addColumn('action', function ($data) {
            $iconClass = "btn btn-icon btn-sm btn-info waves-effect";
            $html = '<div class="button-list">';
            $html .= "<a href='javascript:;' title='Edit' onclick='setEdit($data->id)' class='$iconClass'><i class='fa fa-pencil'></i></a>";
            $html .= "<a href='javascript:;' title='Delete' data-modal-id='deleteTransactionType' data-id='$data->id' onclick='DeleteConfirm(this)' class='$iconClass'><i class='fa fa-trash'></i></a>";
            $html .= "<a href='javascript:;' title='View' onclick='setEdit($data->id,".'"view"'.")' class='$iconClass'><i class='fa fa-eye'></i></a>";
            $html .= "</div>";
            return $html;
        })
        ->removeColumn('id')
        ->make();
    }

    
    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
        $this->validate($request,[
            "title"=>'required',
        ]);
        $id = $request->id;
        $transactiontype = TransactionType::find($id);
        if($transactiontype){
            $transactiontype->update($request->all());
        }
        else{
            $inputs = $request->all();
            TransactionType::create($inputs);
        }
        return response()->json(array(
            "status"  =>  "success",
            "message" =>  "Saved successfully.",
        ));
    }

    public function show($id)
    {
        //
        $transactiontype = TransactionType::find($id);
        $filteredArr = [
            'id'=>["type"=>"hidden",'value'=>$transactiontype->id],
            'title'=>["type"=>"text",'value'=>$transactiontype->title],
            'title_nl'=>["type"=>"text",'value'=>$transactiontype->title_nl],
            'title_es'=>["type"=>"text",'value'=>$transactiontype->title_es],
        ];
        return response()->json(array(
            "status"  =>  "success",
            "inputs"=>$filteredArr,
        ));
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
        $transactiontype = TransactionType::find($id);
        $transactiontype->delete();
        return response()->json(array(
            "status"  =>  "success",
            "message" =>  "Deleted successfully.",
        ));
    }
}

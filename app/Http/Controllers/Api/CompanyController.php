<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Http\Requests\UpdateCompanyRequest;

class CompanyController extends Controller
{
    public function show(Request $request)
    {
        $company = $request->user()->companies()->first();
        return response()->success('Company retrieved', $company);
    }

    public function update(UpdateCompanyRequest $request)
    {
        $company = $request->user()->companies()->first();
        $company->update($request->validated());
        return response()->success('Company updated', $company);
    }

    public function teams(Request $request)
    {
        $company = $request->user()->companies()->with('teams')->first();
        return response()->success('Teams retrieved', $company->teams);
    }
}

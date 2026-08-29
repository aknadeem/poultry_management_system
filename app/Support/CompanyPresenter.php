<?php

namespace App\Support;

use App\Models\PartyCompany;

class CompanyPresenter
{
    /**
     * @return array<string, mixed>
     */
    public static function listItem(PartyCompany $company): array
    {
        return [
            'id'              => $company->id,
            'company_name'    => $company->company_name,
            'company_address' => $company->company_address,
            'company_code'    => $company->company_code,
            'company_logo'    => $company->company_logo,
            'business_type'   => $company->businesstype?->name,
            'vendor_name'     => $company->vendor?->name,
            'is_active'       => (bool) $company->is_active,
            'created_at'      => $company->created_at?->format('Y-m-d'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function form(PartyCompany $company): array
    {
        return [
            'id'               => $company->id,
            'company_name'     => $company->company_name,
            'company_address'  => $company->company_address,
            'company_code'     => $company->company_code,
            'company_logo'     => $company->company_logo,
            'business_type_id' => $company->business_type_id,
            'business_type'    => $company->businesstype?->name,
            'vendor_name'      => $company->vendor?->name,
            'contact_no'       => $company->vendor?->contact_no,
            'email'            => $company->vendor?->email,
            'description'      => $company->vendor?->description,
            'is_active'        => (bool) $company->is_active,
            'created_at'       => $company->created_at?->format('Y-m-d'),
        ];
    }
}

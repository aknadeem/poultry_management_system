<?php

namespace App\Support;

use App\Models\Party;

class PartyPresenter
{
    /**
     * @return array<string, mixed>
     */
    public static function listItem(Party $party): array
    {
        $party->loadMissing(['farm', 'company', 'accounts', 'documents', 'balanceLimits']);

        $typeLabel = 'Party';
        if ($party->is_vendor || $party->is_customer) {
            $typeLabel = ($party->is_vendor ? 'Vendor' : 'Customer')
                .' '.($party->is_customer ? '/ Customer' : '/ Vendor');
        }

        return [
            'id' => $party->id,
            'name' => $party->name,
            'cnic_no' => $party->cnic_no,
            'contact_no' => $party->contact_no,
            'is_customer' => (bool) $party->is_customer,
            'is_vendor' => (bool) $party->is_vendor,
            'type_label' => $typeLabel,
            'farm_name' => $party->farm?->farm_name,
            'company_name' => $party->company?->company_name,
            'profile_picture' => $party->profile_picture,
            'accounts' => $party->accounts->map(fn ($account): array => [
                'id' => $account->id,
                'account_title' => $account->account_title,
                'account_number' => $account->account_number,
                'bank_name' => $account->bank_name,
                'opening_balance' => $account->opening_balance,
            ])->values()->all(),
            'documents' => $party->documents->map(fn ($document): array => [
                'id' => $document->id,
                'title' => $document->title,
                'document_name' => $document->document_name,
                'url' => $document->document_name
                    ? asset('storage/party/documents/'.$document->document_name)
                    : null,
            ])->values()->all(),
            'balance_limits' => $party->balanceLimits->map(fn ($limit): array => [
                'id' => $limit->id,
                'start_date' => $limit->start_date,
                'end_date' => $limit->end_date,
                'debit_limit' => $limit->debit_limit,
                'credit_limit' => $limit->credit_limit,
            ])->values()->all(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function form(Party $party): array
    {
        $party->loadMissing(['farm', 'company']);

        return [
            'id' => $party->id,
            'is_customer' => (int) $party->is_customer,
            'is_vendor' => (int) $party->is_vendor,
            'name' => $party->name,
            'guardian_name' => $party->guardian_name,
            'cnic_no' => $party->cnic_no,
            'email' => $party->email,
            'contact_no' => $party->contact_no,
            'business_no' => $party->business_no,
            'manual_number' => $party->manual_number,
            'address' => $party->address,
            'description' => $party->description,
            'country_id' => $party->country_id,
            'province_id' => $party->province_id,
            'city_id' => $party->city_id,
            'contact_person_id' => $party->contact_person_id,
            'customer_type_id' => $party->customer_type_id,
            'customer_division_id' => $party->customer_division_id,
            'vendor_type_id' => $party->vendor_type_id,
            'vendor_division_id' => $party->vendor_division_id,
            'opening_balance' => $party->balance,
            'balance_type' => $party->balance_type,
            'profile_picture' => $party->profile_picture,
            'cnic_front' => $party->cnic_front,
            'cnic_back' => $party->cnic_back,
            'signature' => $party->signature,
            'farm_type_id' => $party->farm?->farm_type_id,
            'farm_subtype_id' => $party->farm?->farm_subtype_id,
            'farm_name' => $party->farm?->farm_name,
            'farm_noc' => $party->farm?->farm_noc,
            'farm_address' => $party->farm?->farm_address,
            'farm_image' => $party->farm?->farm_image,
            'company_name' => $party->company?->company_name,
            'business_type_id' => $party->company?->business_type_id,
            'company_address' => $party->company?->company_address,
            'company_logo' => $party->company?->company_logo,
        ];
    }
}

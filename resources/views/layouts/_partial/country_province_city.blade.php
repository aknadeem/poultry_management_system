@php
$required = 'required';
$exProvince_id = 0;
$exCity_id = 0;
if($updateData->id){
$exProvince_id = $updateData->province_id;
$exCity_id = $updateData->city_id;
$required = '';
}
@endphp

<div class="col-3 mb-2">
    <label class="font_bold" for="Country"> Select Country* </label>
    <select name="country_id" class="form-control mySelect" id="Country" required data-toggle="select2"
        data-width="100%">

        <option value=""> Select Country</option>
        @forelse ($countries as $country)
        <option {{ ($updateData?->country_id == $country->id) ? "selected" : "" }}
            value="{{ $country->id }}"> {{$country->name}}</option>
        @empty
        @endforelse
    </select>

    @error('country_id')
    <span class="text-danger country_id_error"> {{ $message }} </span>
    @enderror
</div>
<div class="col-3 mb-2">
    <label class="font_bold" for="Province"> Select Province* </label>
    <select name="province_id" required id="Province" class="form-control mySelect" data-toggle="select2"
        data-width="100%">
        <option value=""> Select Province</option>
        @if ($updateData?->province_id)
        <option selected value="{{ $updateData->province->id }}"> {{
            $updateData->province->name }}</option>
        @endif
    </select>

    @error('province_id')
    <span class="text-danger province_id_error"> {{ $message }} </span>
    @enderror
</div>
<div class="col-3 mb-2">
    <label class="font_bold" for="City"> Select City* </label>
    <select name="city_id" id="City" class="form-control mySelect" required data-toggle="select2" data-width="100%">
        <option value=""> Select City</option>
        @if ($updateData?->city_id)
        <option selected value="{{ $updateData->city->id }}"> {{
            $updateData->city->name }}</option>
        @endif
    </select>

    @error('city_id')
    <span class="text-danger city_id_error"> {{ $message }} </span>
    @enderror
</div>

@section('modal_scripts')
<script>
    $(function() {
        var Countries = <?php  echo json_encode($countries); ?>  || {}
        var Provinces_list = {}
        $("#Country").change(function(){
            var country_id = parseInt($(this).val()) || 0
            var exProvince_id = parseInt(<?php echo json_encode($exProvince_id); ?>)
            var province_html= '';
            var selected = '';

           
            var country = Countries.find(x => x.id === country_id);
            if(country.provinces.length > 0){
                Provinces_list = country.provinces
                for (var i = 0; i < country.provinces.length; i++) {
                    if(exProvince_id == country.provinces[i].id){
                        selected = 'selected';
                    }
                    province_html+='<option '+selected+' value='+ country.provinces[i].id +'>'+ country.provinces[i].name +'</option>'; 
                }
            }else{
                province_html='<option> No Province Found </option>';
            }
            $('#Province').html(province_html);
            // $('#Province').change();
        });
        
        $("#Province").change(function(){
            var province_id = parseInt($(this).val()) || 0;
            var exCity_id = parseInt(<?php echo json_encode($exCity_id); ?>);
            var city_html= '';
            var selected = '';
            var province = Provinces_list.find(x => x.id === province_id);
            if(province.cities.length > 0){
                for (var i = 0; i < province.cities.length; i++) {
                    if(exCity_id == province.cities[i].id){
                        selected = 'selected';
                    }
                    city_html+='<option '+selected+' value='+ province.cities[i].id +'>'+ province.cities[i].name +'</option>'; 
                }
            }else{
                city_html='<option> No City Found </option>';
            }
            $('#City').html(city_html);
            // $('#Province').change();
        });
    });

</script>
@endsection